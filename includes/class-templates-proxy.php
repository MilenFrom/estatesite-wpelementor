<?php
/**
 * Customer-side REST proxy that fetches a template from the EstateSite library
 * (dev.estatesite.eu by default, overridable via ESELE_TEMPLATES_LIBRARY_URL or
 * the `esele_templates_library_url` filter) and sideloads its remote images
 * into the local media library before returning the rewritten JSON to the
 * caller.
 *
 * Why this exists:
 * Elementor's import_template AJAX endpoint expects an already-local template
 * payload — it does not fetch from a remote library, nor does it do anything
 * for absolute http(s) image URLs other than embed them verbatim. We want
 * customers to be able to insert a template from our central library and end
 * up with self-hosted images (no hot-linking, no broken images if we ever
 * relocate the library). This proxy is the bridge.
 *
 * Algorithm:
 *   1. Fetch the template JSON from library_url() . '/by-slug/{slug}'.
 *   2. Walk the `content` element tree and for every attachment-shaped value
 *      (`['url'=>..., 'id'=>...]`) call Elementor's Import_Images->import()
 *      which downloads the file, creates a WP attachment, and dedupes via
 *      sha1(url) stored in `_elementor_source_image_hash` postmeta.
 *   3. Capture a rewrites map (source URL → new local URL) and apply it to
 *      any inline string occurrences (`htmlCache`, `_text`, etc.) — these
 *      are NOT covered by the attachment walk because they're free-form
 *      HTML/text that happens to embed the same URLs.
 *   4. Return the rewritten payload + sideload_stats so the editor can show
 *      a summary.
 *
 * @package EstateSite\Elementor
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Templates_Proxy {

	/**
	 * Wire the REST route registration into the WP boot sequence.
	 */
	public static function register(): void {
		add_action( 'rest_api_init', [ __CLASS__, 'register_route' ] );
	}

	/**
	 * POST /wp-json/estatesite-wpelementor/v1/insert-template
	 *
	 * Body: { "slug": "agency-hero-v2" }
	 */
	public static function register_route(): void {
		register_rest_route(
			'estatesite-wpelementor/v1',
			'/insert-template',
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ __CLASS__, 'handle' ],
				'permission_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
				'args' => [
					'slug' => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
						'validate_callback' => static function ( $value ) {
							return is_string( $value ) && (bool) preg_match( '/^[a-z0-9-]+$/', $value );
						},
					],
				],
			]
		);
	}

	/**
	 * Handle the insert-template request.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function handle( \WP_REST_Request $request ) {
		// Sideloading 100+ images can easily exceed php max_execution_time on
		// small VPSes. The REST request is user-initiated and bounded by the
		// number of attachments in the template, so removing the cap is safe.
		@set_time_limit( 0 );

		if ( ! class_exists( '\Elementor\TemplateLibrary\Classes\Import_Images' ) ) {
			return new \WP_Error(
				'elementor_missing',
				__( 'Elementor Import_Images class is not available. Is Elementor active?', 'estatesite-wpelementor' ),
				[ 'status' => 500 ]
			);
		}

		$slug = (string) $request->get_param( 'slug' );
		$url  = Templates::library_url() . '/by-slug/' . rawurlencode( $slug );

		$response = wp_safe_remote_get( $url, [ 'timeout' => 30 ] );
		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'library_unreachable',
				/* translators: %s: error message from wp_safe_remote_get */
				sprintf( __( 'Could not reach template library: %s', 'estatesite-wpelementor' ), $response->get_error_message() ),
				[ 'status' => 502 ]
			);
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			return new \WP_Error(
				'library_http_error',
				/* translators: %d: HTTP status code */
				sprintf( __( 'Template library returned HTTP %d.', 'estatesite-wpelementor' ), $code ),
				[ 'status' => 502 ]
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( ! is_array( $data ) || empty( $data['content'] ) || ! is_array( $data['content'] ) ) {
			return new \WP_Error(
				'invalid_template_payload',
				__( 'Template library response is missing a valid content tree.', 'estatesite-wpelementor' ),
				[ 'status' => 502 ]
			);
		}

		$rewrites = [];
		$stats    = [
			'sideloaded'      => 0,
			'deduped'         => 0,
			'failed'          => 0,
			'inline_replaced' => 0,
		];

		$content = $data['content'];
		self::walk_and_sideload( $content, $rewrites, $stats );

		if ( ! empty( $rewrites ) ) {
			self::rewrite_inline_urls( $content, $rewrites, $stats );
		}

		$data['content']         = $content;
		$data['sideload_stats']  = $stats;

		return rest_ensure_response( $data );
	}

	/**
	 * Recursively walk the Elementor element tree.
	 *
	 * Element shape (simplified):
	 *   {
	 *     "id": "...",
	 *     "elType": "section|column|widget",
	 *     "settings": { ... },
	 *     "elements": [ ...children... ]
	 *   }
	 *
	 * We only touch `settings`; `elements` is recursed into.
	 *
	 * @param array $nodes    Reference so child-tree edits propagate up.
	 * @param array $rewrites Reference accumulator: source URL → new URL.
	 * @param array $stats    Reference counters.
	 */
	private static function walk_and_sideload( array &$nodes, array &$rewrites, array &$stats ): void {
		foreach ( $nodes as &$node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}
			if ( isset( $node['settings'] ) && is_array( $node['settings'] ) ) {
				self::sideload_settings( $node['settings'], $rewrites, $stats );
			}
			if ( isset( $node['elements'] ) && is_array( $node['elements'] ) ) {
				self::walk_and_sideload( $node['elements'], $rewrites, $stats );
			}
		}
		unset( $node );
	}

	/**
	 * Process a single element's settings array, sideloading any
	 * attachment-shaped values in place.
	 *
	 * We preserve all sibling keys (size, alt, source, etc.) via array_merge —
	 * Elementor controls rely on these. We only overwrite `id` and `url`.
	 *
	 * @param array $settings Reference so edits propagate to the parent node.
	 * @param array $rewrites Reference accumulator.
	 * @param array $stats    Reference counters.
	 */
	private static function sideload_settings( array &$settings, array &$rewrites, array &$stats ): void {
		foreach ( $settings as $key => &$value ) {
			if ( self::looks_like_attachment( $value ) ) {
				$new = self::sideload_one( $value, $rewrites, $stats );
				if ( is_array( $new ) ) {
					$value = array_merge( $value, $new );
				}
				continue;
			}

			if ( is_array( $value ) && self::is_attachment_list( $value ) ) {
				$value = array_map(
					static function ( $item ) use ( &$rewrites, &$stats ) {
						if ( ! self::looks_like_attachment( $item ) ) {
							return $item;
						}
						$new = self::sideload_one( $item, $rewrites, $stats );
						return is_array( $new ) ? array_merge( $item, $new ) : $item;
					},
					$value
				);
				continue;
			}

			if ( is_array( $value ) && self::looks_like_repeater_rows( $value ) ) {
				foreach ( $value as &$row ) {
					if ( is_array( $row ) ) {
						self::sideload_settings( $row, $rewrites, $stats );
					}
				}
				unset( $row );
				continue;
			}
		}
		unset( $value );
	}

	/**
	 * Sideload a single attachment via Elementor's Import_Images.
	 *
	 * Returns the new attachment array (id + url) or null if the source URL
	 * is already local, malformed, or the import failed.
	 *
	 * @param array $attachment Source attachment (must have a non-empty `url`).
	 * @param array $rewrites   Reference: source URL → new URL.
	 * @param array $stats      Reference counters.
	 * @return array|null
	 */
	private static function sideload_one( array $attachment, array &$rewrites, array &$stats ): ?array {
		static $cache = [];

		$src = isset( $attachment['url'] ) ? (string) $attachment['url'] : '';
		if ( $src === '' ) {
			return null;
		}

		if ( isset( $cache[ $src ] ) ) {
			$stats['deduped']++;
			$rewrites[ $src ] = $cache[ $src ]['url'];
			return array_merge( $attachment, $cache[ $src ] );
		}

		// Already pointing at this site's uploads — nothing to do.
		if ( strpos( $src, home_url() ) === 0 ) {
			return null;
		}

		// Not a remote http(s) URL we can fetch.
		if ( ! preg_match( '#^https?://#i', $src ) ) {
			return null;
		}

		$importer = new \Elementor\TemplateLibrary\Classes\Import_Images();
		$result   = $importer->import( [
			'url' => $src,
			'id'  => 0,
		] );

		if ( ! is_array( $result ) || empty( $result['url'] ) ) {
			$stats['failed']++;
			return null;
		}

		$cache[ $src ] = [
			'id'  => isset( $result['id'] ) ? (int) $result['id'] : 0,
			'url' => (string) $result['url'],
		];
		$rewrites[ $src ] = $cache[ $src ]['url'];
		$stats['sideloaded']++;

		return array_merge( $attachment, $cache[ $src ] );
	}

	/**
	 * Walk the element tree's leaf strings (htmlCache, _text, and any other
	 * field that may have inlined the source URL as a literal) and rewrite
	 * source URLs to their new sideloaded equivalents.
	 *
	 * Mirrors the three escape forms used by Templates::resolve_upload_tokens()
	 * (see class-templates.php:457-470):
	 *
	 *   - plain:        https://host/path
	 *   - single-escape: https:\/\/host\/path        (inside JSON string values)
	 *   - triple-escape: https:\\\/\\\/host\\\/path  (inside htmlCache)
	 *
	 * @param array $nodes    Tree reference.
	 * @param array $rewrites Source → new URL map.
	 * @param array $stats    Reference counters.
	 */
	private static function rewrite_inline_urls( array &$nodes, array $rewrites, array &$stats ): void {
		if ( empty( $rewrites ) ) {
			return;
		}

		$search  = [];
		$replace = [];
		foreach ( $rewrites as $from => $to ) {
			$from = (string) $from;
			$to   = (string) $to;

			// Plain.
			$search[]  = $from;
			$replace[] = $to;

			// Single-escape (\/).
			$search[]  = str_replace( '/', '\\/', $from );
			$replace[] = str_replace( '/', '\\/', $to );

			// Triple-escape (\\\/).
			$search[]  = str_replace( '/', '\\\\\\/', $from );
			$replace[] = str_replace( '/', '\\\\\\/', $to );
		}

		$count = 0;
		array_walk_recursive( $nodes, static function ( &$leaf ) use ( $search, $replace, &$count ) {
			if ( ! is_string( $leaf ) ) {
				return;
			}
			$leaf = str_replace( $search, $replace, $leaf, $hits );
			if ( $hits ) {
				$count += $hits;
			}
		} );

		$stats['inline_replaced'] = $count;
	}

	// -------------------------------------------------------------------------
	// Shape detection helpers.
	// -------------------------------------------------------------------------

	/**
	 * Looks like an Elementor "Media" control value: ['url' => string, 'id' => ...].
	 *
	 * @param mixed $v
	 */
	private static function looks_like_attachment( $v ): bool {
		return is_array( $v )
			&& isset( $v['url'] )
			&& is_string( $v['url'] )
			&& $v['url'] !== '';
	}

	/**
	 * Looks like a "Gallery" control value: a list of attachments.
	 *
	 * @param array $v
	 */
	private static function is_attachment_list( array $v ): bool {
		if ( $v === [] ) {
			return false;
		}
		$first = reset( $v );
		return is_array( $first ) && isset( $first['url'] );
	}

	/**
	 * Looks like a Repeater control value: list of assoc-array rows whose
	 * top-level keys are NOT attachment-shaped (those are caught earlier).
	 * Each row can itself contain attachment fields, so we recurse.
	 *
	 * @param array $v
	 */
	private static function looks_like_repeater_rows( array $v ): bool {
		if ( $v === [] ) {
			return false;
		}
		$first = reset( $v );
		if ( ! is_array( $first ) ) {
			return false;
		}
		if ( isset( $first['url'] ) ) {
			// Caught by is_attachment_list().
			return false;
		}
		return self::is_assoc( $first );
	}

	/**
	 * True if the array has any non-sequential string/int keys (i.e. not a
	 * pure 0..N-1 list).
	 *
	 * @param array $v
	 */
	private static function is_assoc( array $v ): bool {
		if ( $v === [] ) {
			return false;
		}
		return array_keys( $v ) !== range( 0, count( $v ) - 1 );
	}
}
