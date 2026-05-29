<?php
/**
 * Self-hosted Elementor template catalog.
 *
 * Intercepts all `wp_remote_get('studio.houzez.co/wp-json/favethemes-blocks/...')`
 * requests via the `pre_http_request` filter and serves responses from our
 * bundled catalog at `wp-content/plugins/estatesite-wpelementor/templates/`.
 *
 * Result: the ported Houzez Library modal (in the active theme's
 * `inc/blocks/class-library-source.php`) works without ever calling out to
 * studio.houzez.co — no runtime dependency on 3rd-party services.
 *
 * Catalog structure:
 *   templates/
 *     manifest.json                 — list of 367 templates (id/title/slug/image/category)
 *     content/{id}.json             — Elementor element tree per template
 *     images/{id}.{ext}             — preview thumbnail per template
 *
 * Lives in the Elementor package because templates ARE Elementor element
 * trees — they're inert without Elementor loaded.
 *
 * @package EstateSite\Elementor
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Templates {

	private const REMOTE_API_HOST = 'studio.houzez.co';
	private const REMOTE_API_PATH = '/wp-json/favethemes-blocks/v1/templates';

	/** Cache the loaded manifest in-process so list calls don't re-parse JSON. */
	private static $manifest_cache = null;

	/** Lazy-loaded attachment_id → relative_path map (templates/asset-id-map.php). */
	private static $asset_id_map = null;

	public static function register(): void {
		add_filter( 'pre_http_request', [ self::class, 'intercept_remote' ], 10, 3 );

		// Custom REST routes (optional — JS uses pre_http_request path instead,
		// but having native REST endpoints is useful for our own tooling).
		add_action( 'rest_api_init', [ self::class, 'register_rest_routes' ] );

		// Lazy-populate uploads dir on every page load if missing. Cheap check
		// (single file_exists). Most loads no-op. Catches cases where activation
		// hook didn't fire (e.g. plugin installed via wp-cli or manually).
		add_action( 'admin_init', [ self::class, 'maybe_populate_uploads' ] );

		// Template attachment-ID resolution. Inserted templates reference
		// attachment IDs from Houzez Studio (e.g. 22221, 22262, ...) that don't
		// exist on customer sites — only the underlying SVG/PNG files do.
		// These filters substitute the real file path/URL when an Elementor
		// widget tries to look up such an ID, but only when no real attachment
		// of that ID exists (never hijack a customer's real attachment).
		add_filter( 'get_attached_file',         [ self::class, 'filter_attached_file' ],         10, 2 );
		add_filter( 'wp_get_attachment_url',     [ self::class, 'filter_attachment_url' ],        10, 2 );
		add_filter( 'wp_get_attachment_image_src', [ self::class, 'filter_attachment_image_src' ], 10, 3 );
	}

	/**
	 * One-shot copy of bundled template assets from plugin/templates/source-assets/
	 * into wp-content/uploads/estatesite-wpelementor/.
	 *
	 * Idempotent: skips if the destination already has files (preserves any
	 * customer customizations).
	 *
	 * Called from:
	 *   - register_activation_hook (clean install path)
	 *   - admin_init via maybe_populate_uploads() (safety net for non-activation installs)
	 *
	 * @return array { copied, skipped, ok }
	 */
	public static function populate_uploads_dir( bool $force = false ): array {
		$src_dir  = self::base_dir() . '/source-assets';
		$dest_dir = self::uploads_dir();

		if ( ! is_dir( $src_dir ) ) {
			return [ 'ok' => false, 'message' => 'Bundled source-assets/ dir missing in plugin.' ];
		}

		// Check destination state.
		$dest_existed = is_dir( $dest_dir ) && self::dir_has_files( $dest_dir );
		if ( $dest_existed && ! $force ) {
			return [ 'ok' => true, 'copied' => 0, 'skipped' => 1, 'message' => 'Uploads dir already populated; skipping (use force=true to overwrite).' ];
		}

		@mkdir( $dest_dir, 0755, true );
		if ( ! is_writable( $dest_dir ) ) {
			return [ 'ok' => false, 'message' => "Destination $dest_dir not writable." ];
		}

		$copied = self::recursive_copy( $src_dir, $dest_dir );
		return [ 'ok' => true, 'copied' => $copied, 'message' => "Populated $copied files." ];
	}

	/**
	 * Cheap admin_init guard. Single is_dir + is_empty check; no-ops on every load
	 * once populated. Only kicks the recursive copy if the dir is missing/empty.
	 */
	public static function maybe_populate_uploads(): void {
		// Marker option avoids redundant disk checks on every admin pageload.
		if ( get_option( 'estatesite_templates_uploads_populated' ) ) {
			return;
		}
		$result = self::populate_uploads_dir( false );
		if ( $result['ok'] && ( $result['copied'] ?? 0 ) >= 0 ) {
			update_option( 'estatesite_templates_uploads_populated', time(), false );
		}
	}

	/**
	 * Base wp-content/uploads/estatesite-wpelementor/ path. Respects multisite.
	 */
	public static function uploads_dir(): string {
		$wp_uploads = wp_upload_dir();
		return trailingslashit( $wp_uploads['basedir'] ?? WP_CONTENT_DIR . '/uploads' ) . 'estatesite-wpelementor';
	}

	/**
	 * Base wp-content/uploads/estatesite-wpelementor/ URL.
	 */
	public static function uploads_url(): string {
		$wp_uploads = wp_upload_dir();
		return trailingslashit( $wp_uploads['baseurl'] ?? content_url( 'uploads' ) ) . 'estatesite-wpelementor';
	}

	// ---------------------------------------------------------------------
	// Template attachment-ID resolution.
	//
	// Templates reference attachment IDs from Houzez Studio (e.g. 22221).
	// Those IDs don't exist as real attachments on customer sites — only
	// the underlying SVG/PNG files do. We intercept WP's attachment lookups
	// for those IDs and serve from our uploads dir instead.
	//
	// Safety: every filter first checks if a real attachment with that ID
	// already exists. If so, we return the unfiltered value untouched. We
	// only step in when WP would otherwise return false/empty.
	// ---------------------------------------------------------------------

	private static function asset_id_map(): array {
		if ( self::$asset_id_map === null ) {
			$file = self::base_dir() . '/asset-id-map.php';
			self::$asset_id_map = is_readable( $file ) ? (array) include $file : [];
		}
		return self::$asset_id_map;
	}

	private static function map_id_to_path( int $id ): ?string {
		$map = self::asset_id_map();
		return isset( $map[ $id ] ) ? $map[ $id ] : null;
	}

	/**
	 * Per-request memoized file_exists for resolved asset paths. Save/render
	 * cycles can call into the filters dozens of times for the same IDs; this
	 * avoids redundant stat() syscalls within a single request.
	 */
	private static $file_exists_cache = [];

	private static function resolved_file_exists( string $path ): bool {
		if ( ! isset( self::$file_exists_cache[ $path ] ) ) {
			self::$file_exists_cache[ $path ] = file_exists( $path );
		}
		return self::$file_exists_cache[ $path ];
	}

	/**
	 * Hooked to `get_attached_file`. Returns the file path under our uploads
	 * dir when the attachment ID is one of ours AND no real file is mapped.
	 */
	public static function filter_attached_file( $file, $attachment_id ) {
		if ( $file && self::resolved_file_exists( $file ) ) {
			return $file; // real attachment — leave alone.
		}
		$rel = self::map_id_to_path( (int) $attachment_id );
		if ( $rel === null ) {
			return $file;
		}
		$candidate = self::uploads_dir() . '/' . $rel;
		return self::resolved_file_exists( $candidate ) ? $candidate : $file;
	}

	/**
	 * Hooked to `wp_get_attachment_url`. Returns our uploads URL when the
	 * attachment ID is one of ours AND no real attachment URL exists.
	 *
	 * NOTE: WP's `wp_get_attachment_url()` returns false BEFORE this filter
	 * runs when the attachment post doesn't exist (see post.php line ~6977).
	 * So this filter only helps when there IS a post but its URL resolves
	 * to falsy — a narrow case. The image_src filter below handles the
	 * common cases (image widget, background image).
	 */
	public static function filter_attachment_url( $url, $attachment_id ) {
		if ( $url ) {
			return $url; // real attachment — leave alone.
		}
		$rel = self::map_id_to_path( (int) $attachment_id );
		if ( $rel === null ) {
			return $url;
		}
		return self::uploads_url() . '/' . $rel;
	}

	/**
	 * Hooked to `wp_get_attachment_image_src`. WP returns `false` for unknown
	 * attachments — we synthesize the [url, width, height, is_intermediate]
	 * tuple Elementor expects. Width/height left at 0 (Elementor falls back to
	 * natural image dimensions in CSS) which is fine for icons/decorative use.
	 */
	public static function filter_attachment_image_src( $image, $attachment_id, $size ) {
		if ( $image !== false ) {
			return $image; // real attachment — leave alone.
		}
		$rel = self::map_id_to_path( (int) $attachment_id );
		if ( $rel === null ) {
			return $image;
		}
		$path = self::uploads_dir() . '/' . $rel;
		if ( ! self::resolved_file_exists( $path ) ) {
			return $image;
		}
		return [ self::uploads_url() . '/' . $rel, 0, 0, false ];
	}

	private static function dir_has_files( string $dir ): bool {
		$it = @opendir( $dir );
		if ( $it === false ) return false;
		while ( ( $f = readdir( $it ) ) !== false ) {
			if ( $f !== '.' && $f !== '..' ) {
				closedir( $it );
				return true;
			}
		}
		closedir( $it );
		return false;
	}

	private static function recursive_copy( string $src, string $dest ): int {
		$count = 0;
		$it    = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $src, \FilesystemIterator::SKIP_DOTS ),
			\RecursiveIteratorIterator::SELF_FIRST
		);
		foreach ( $it as $file ) {
			$rel  = substr( $file->getPathname(), strlen( $src ) + 1 );
			$target = $dest . '/' . $rel;
			if ( $file->isDir() ) {
				if ( ! is_dir( $target ) ) {
					@mkdir( $target, 0755, true );
				}
			} else {
				if ( ! file_exists( $target ) ) {
					@copy( $file->getPathname(), $target );
					$count++;
				}
			}
		}
		return $count;
	}

	/**
	 * Intercept any wp_remote_* call to studio.houzez.co/wp-json/favethemes-blocks/v1/templates*
	 * Returns a synthetic WP_Http response shaped like a real one.
	 */
	public static function intercept_remote( $pre, $args, $url ) {
		// Already handled by another filter — bail.
		if ( $pre !== false ) {
			return $pre;
		}

		$parts = wp_parse_url( $url );
		if ( ! is_array( $parts ) ) {
			return $pre;
		}
		if ( ( $parts['host'] ?? '' ) !== self::REMOTE_API_HOST ) {
			return $pre;
		}
		if ( strpos( $parts['path'] ?? '', self::REMOTE_API_PATH ) !== 0 ) {
			return $pre;
		}

		$query = [];
		if ( isset( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
		}

		// Dispatch based on query type.
		if ( ! empty( $query['all'] ) ) {
			$body = self::manifest_response_body();
		} elseif ( ! empty( $query['id'] ) ) {
			$body = self::single_template_response_body( (int) $query['id'] );
		} else {
			// Unknown shape — pass through to real network.
			return $pre;
		}

		if ( $body === null ) {
			// Template ID not found in our catalog. Return 404 shape.
			return self::synthetic_response( 404, '{"code":"not_found","message":"Template not found"}' );
		}

		return self::synthetic_response( 200, $body );
	}

	/**
	 * Get the full manifest response in the shape the Library JS expects.
	 *
	 * Top-level fields the JS template uses:
	 *   elements        — array of templates (image + link rewritten)
	 *   tags            — array of category filters [{title, slug, count}, ...]
	 *   total_records   — total template count
	 *   generated_at    — timestamp (for cache busting / debug display)
	 *   version         — version string
	 *   source          — "EstateSite" (replaces upstream "Houzez Studio")
	 *
	 * Filterable via `estatesite_template_preview_url` so sites can repoint
	 * (e.g. a multi-site network might point at a self-hosted gallery).
	 */
	private static function manifest_response_body(): ?string {
		$manifest = self::manifest();
		if ( ! $manifest ) {
			return null;
		}

		$images_base_url = self::images_base_url();
		$preview_base    = self::preview_base_url();
		$rewritten       = [];
		foreach ( $manifest['elements'] as $tpl ) {
			$id = (int) ( $tpl['id'] ?? 0 );
			if ( ! $id ) continue;

			// Rewrite image URL to local copy.
			$local_image = self::find_local_image( $id );
			if ( $local_image ) {
				$tpl['image'] = $images_base_url . $local_image;
			}

			// Rewrite preview link to our EstateSite template-gallery URL.
			$slug = $tpl['slug'] ?? '';
			if ( $slug && $preview_base ) {
				$tpl['link'] = rtrim( $preview_base, '/' ) . '/' . $slug . '/';
			}

			$rewritten[] = $tpl;
		}

		$tags = self::tags();

		$response = [
			'elements'      => $rewritten,
			'tags'          => $tags,
			'total_records' => count( $rewritten ),
			'generated_at'  => gmdate( 'Y-m-d H:i:s' ),
			'version'       => '1.0.0',
			'source'        => 'EstateSite',
		];

		return wp_json_encode( $response );
	}

	/**
	 * Load the tags catalog. Cached per-request.
	 * Source: templates/tags.json (bundled from upstream during initial fetch).
	 * If missing, derive from manifest categories.
	 */
	public static function tags(): array {
		static $cached = null;
		if ( $cached !== null ) {
			return $cached;
		}
		$tags_file = self::base_dir() . '/tags.json';
		if ( is_readable( $tags_file ) ) {
			$decoded = json_decode( file_get_contents( $tags_file ), true );
			if ( is_array( $decoded ) ) {
				$cached = $decoded;
				return $cached;
			}
		}
		// Fallback: derive from manifest categories.
		$cached   = [];
		$manifest = self::manifest();
		if ( $manifest ) {
			$slugs = [];
			foreach ( $manifest['elements'] as $tpl ) {
				foreach ( explode( ',', $tpl['category'] ?? '' ) as $cat ) {
					$cat = trim( $cat );
					if ( $cat && $cat !== 'block' && $cat !== 'template' ) {
						$slugs[ $cat ] = ( $slugs[ $cat ] ?? 0 ) + 1;
					}
				}
			}
			ksort( $slugs );
			foreach ( $slugs as $slug => $count ) {
				$cached[] = [
					'title' => ucwords( str_replace( '-', ' ', $slug ) ),
					'slug'  => $slug,
					'count' => $count,
				];
			}
		}
		return $cached;
	}

	/**
	 * Base URL for the live preview page of each template.
	 * Defaults to estatesite.eu/templates — filterable per-site.
	 */
	public static function preview_base_url(): string {
		return (string) apply_filters(
			'estatesite_template_preview_url',
			'https://estatesite.eu/templates'
		);
	}

	/**
	 * Get a single template's content body. Returns null if file missing.
	 *
	 * Asset URLs inside the JSON are stored as the placeholder
	 *   {{ESELE_UPLOADS_URL}}/<relative-path>
	 * to keep template files portable across domains and protocols. We resolve
	 * the placeholder against the live `wp-content/uploads/estatesite-wpelementor/`
	 * URL at serve time (correct host, correct scheme, multisite-aware).
	 *
	 * The token contains no slashes/backslashes/quotes so str_replace works
	 * identically whether the token sits inside a JSON value (single-escaped
	 * slashes) or inside an htmlCache string (triple-escaped slashes). The
	 * replacement value is JSON-escaped per nesting level so resolved URLs
	 * stay syntactically valid in their containing context.
	 */
	private static function single_template_response_body( int $id ): ?string {
		$file = self::content_dir() . "/$id.json";
		if ( ! is_readable( $file ) ) {
			return null;
		}
		$body = file_get_contents( $file );
		if ( $body === false ) {
			return null;
		}
		return self::resolve_upload_tokens( $body );
	}

	/**
	 * Replace {{ESELE_UPLOADS_URL}} tokens with the live uploads URL.
	 *
	 * The token literal is identical everywhere, but its containing context
	 * determines how slashes in the *replacement value* must be escaped:
	 *   - inside JSON string values, followed by `\/path`     → single-escape: \/uploads\/...
	 *   - inside htmlCache strings,  followed by `\\\/path`   → triple-escape: \\\/uploads\\\/...
	 *
	 * We disambiguate by the slash form that immediately follows the token.
	 * Triple-escape MUST be replaced first — replacing the shorter `\/`
	 * variant first would consume part of the longer `\\\/` form.
	 */
	private static function resolve_upload_tokens( string $body ): string {
		$uploads_url = self::uploads_url();
		$single      = str_replace( '/', '\\/', $uploads_url );      // \/host\/path
		$triple      = str_replace( '/', '\\\\\\/', $uploads_url );  // \\\/host\\\/path

		// 1. Token followed by triple-escaped slash (htmlCache content).
		$body = str_replace( '{{ESELE_UPLOADS_URL}}\\\\\\/', $triple . '\\\\\\/', $body );
		// 2. Token followed by single-escaped slash (JSON string values).
		$body = str_replace( '{{ESELE_UPLOADS_URL}}\\/', $single . '\\/', $body );
		// 3. Any orphan tokens not followed by a slash — fall back to raw URL.
		$body = str_replace( '{{ESELE_UPLOADS_URL}}', $uploads_url, $body );

		return $body;
	}

	/**
	 * Build a synthetic WP_Http response array matching what wp_remote_get returns.
	 */
	private static function synthetic_response( int $code, string $body ): array {
		return [
			'headers'  => new \WpOrg\Requests\Utility\CaseInsensitiveDictionary( [
				'content-type'   => 'application/json; charset=UTF-8',
				'content-length' => (string) strlen( $body ),
			] ),
			'body'     => $body,
			'response' => [
				'code'    => $code,
				'message' => $code === 200 ? 'OK' : 'Not Found',
			],
			'cookies'      => [],
			'http_response'=> null,
			'filename'     => null,
		];
	}

	/**
	 * Load the manifest. Cached per-request.
	 */
	public static function manifest(): ?array {
		if ( self::$manifest_cache !== null ) {
			return self::$manifest_cache ?: null;
		}
		$file = self::base_dir() . '/manifest.json';
		if ( ! is_readable( $file ) ) {
			self::$manifest_cache = false;
			return null;
		}
		$data = json_decode( file_get_contents( $file ), true );
		self::$manifest_cache = is_array( $data ) ? $data : false;
		return self::$manifest_cache ?: null;
	}

	/**
	 * Find a local image file for a template ID (matches *.png, *.jpg, *.webp etc.).
	 * Returns the basename, or null if missing.
	 */
	private static function find_local_image( int $id ): ?string {
		$images_dir = self::base_dir() . '/images';
		foreach ( [ 'png', 'jpg', 'jpeg', 'webp', 'gif' ] as $ext ) {
			$file = "$images_dir/$id.$ext";
			if ( is_readable( $file ) ) {
				return "$id.$ext";
			}
		}
		return null;
	}

	// ---------------------------------------------------------------------
	// Public REST API (optional convenience for our own tooling/dashboard).
	// ---------------------------------------------------------------------

	public static function register_rest_routes(): void {
		// Manifest endpoint — matches what blocks-templates.js expects from
		// the all-templates.json file on studio.houzez.co.
		register_rest_route( 'estatesite/v1', '/templates', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'rest_list' ],
			'permission_callback' => '__return_true',
		] );

		// Single template by numeric ID.
		register_rest_route( 'estatesite/v1', '/templates/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'rest_single_by_id' ],
			'permission_callback' => '__return_true',
		] );

		// Single template by slug — JS uses slug-keyed URLs like {slug}.json
		// (e.g. 'footer-18'). Match what blocks-templates.js expects.
		register_rest_route( 'estatesite/v1', '/templates/by-slug/(?P<slug>[a-z0-9-]+)', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'rest_single_by_slug' ],
			'permission_callback' => '__return_true',
		] );
	}

	public static function rest_list( $request ) {
		$body = self::manifest_response_body();
		if ( $body === null ) {
			return new \WP_Error( 'no_catalog', 'Template catalog not available.', [ 'status' => 500 ] );
		}
		// Body is already a JSON string with image+link rewritten.
		$decoded = json_decode( $body, true );
		return rest_ensure_response( $decoded );
	}

	public static function rest_single_by_id( $request ) {
		$id = (int) $request['id'];
		return self::rest_single_response( $id );
	}

	public static function rest_single_by_slug( $request ) {
		$slug     = (string) $request['slug'];
		$manifest = self::manifest();
		if ( ! $manifest ) {
			return new \WP_Error( 'no_catalog', 'Template catalog not available.', [ 'status' => 500 ] );
		}
		foreach ( $manifest['elements'] as $tpl ) {
			if ( ( $tpl['slug'] ?? '' ) === $slug ) {
				return self::rest_single_response( (int) $tpl['id'], $tpl );
			}
		}
		return new \WP_Error( 'not_found', 'Template not found', [ 'status' => 404 ] );
	}

	/**
	 * Build a single-template response in the shape the library JS expects:
	 *   { id, title, slug, link, image, type, category, content: [...] }
	 *
	 * @param int        $id       Template ID.
	 * @param array|null $manifest_entry Pre-fetched manifest row, optional.
	 */
	private static function rest_single_response( int $id, ?array $manifest_entry = null ) {
		$content_body = self::single_template_response_body( $id );
		if ( $content_body === null ) {
			return new \WP_Error( 'not_found', 'Template content missing', [ 'status' => 404 ] );
		}
		$content = json_decode( $content_body, true );
		if ( ! is_array( $content ) ) {
			return new \WP_Error( 'parse_error', 'Template JSON invalid', [ 'status' => 500 ] );
		}

		// Find manifest entry if not provided.
		if ( $manifest_entry === null ) {
			$manifest = self::manifest();
			if ( $manifest ) {
				foreach ( $manifest['elements'] as $row ) {
					if ( (int) ( $row['id'] ?? 0 ) === $id ) {
						$manifest_entry = $row;
						break;
					}
				}
			}
		}

		// Build the JS-shaped response merging manifest metadata + content tree.
		$response = $manifest_entry ?? [];
		$response['id']      = $id;
		$response['content'] = $content['content'] ?? $content;

		// Rewrite image + link in the merged response.
		$local_image = self::find_local_image( $id );
		if ( $local_image ) {
			$response['image'] = self::images_base_url() . $local_image;
		}
		if ( ! empty( $response['slug'] ) ) {
			$response['link'] = rtrim( self::preview_base_url(), '/' ) . '/' . $response['slug'] . '/';
		}

		return rest_ensure_response( $response );
	}

	// ---------------------------------------------------------------------
	// Path helpers
	// ---------------------------------------------------------------------

	public static function base_dir(): string {
		return ESELE_DIR . 'templates';
	}

	public static function content_dir(): string {
		return self::base_dir() . '/content';
	}

	public static function images_base_url(): string {
		return ESELE_URL . 'templates/images/';
	}

	/**
	 * Inventory counts (for dashboard).
	 */
	public static function stats(): array {
		$manifest = self::manifest();
		$content_files = glob( self::content_dir() . '/*.json' );
		$image_files   = glob( self::base_dir() . '/images/*' );
		return [
			'manifest_entries' => $manifest ? count( $manifest['elements'] ?? [] ) : 0,
			'content_files'    => is_array( $content_files ) ? count( $content_files ) : 0,
			'image_files'      => is_array( $image_files ) ? count( $image_files ) : 0,
		];
	}
}
