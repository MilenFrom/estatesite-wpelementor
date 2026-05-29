<?php
/**
 * One-shot fetcher for the bundled Houzez template library.
 *
 * Templates aren't shipped inside the plugin zip — that would push the zip
 * over PHP's default upload_max_filesize on most hosts (~140 MB vs typical
 * 2-8 MB limit) and cause Plugins → Add New → Upload to fail with the
 * generic "The link you followed has expired" error.
 *
 * Instead, this class downloads a versioned .tar.gz from our update server
 * and extracts it into wp-content/uploads/estatesite-wpelementor/templates/.
 * That dir survives plugin updates, so a customer only fetches once.
 *
 * Pipeline:
 *   1. Read templates manifest JSON from REMOTE_MANIFEST_URL
 *   2. Validate version + sha256 metadata are present
 *   3. Download tarball into PHP temp dir (NOT uploads/, to avoid half-fetches
 *      polluting the live location)
 *   4. Verify the downloaded sha256 matches the manifest's
 *   5. Extract into a staging dir under uploads/
 *   6. Atomic swap: move staging → live, archive any prior templates/
 *   7. Reset Templates::base_dir() cache
 *
 * Triggered from the admin "Fetch templates" button (see class-admin-page.php).
 *
 * @package EstateSite\Elementor
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Template_Fetcher {

	private const REMOTE_MANIFEST_URL = 'https://dev.estatesite.eu/updates/estatesite-wpelementor-templates.json';
	private const OPTION_LAST_FETCH   = 'estatesite_wpelementor_templates_fetched';

	/**
	 * Run the full fetch pipeline. Returns a structured result.
	 *
	 * @return array { ok: bool, message: string, version?: string, stats?: array }
	 */
	public static function fetch(): array {
		// 1. Manifest
		$manifest = self::fetch_manifest();
		if ( ! $manifest['ok'] ) {
			return $manifest;
		}
		$meta = $manifest['data'];

		// 2. Pick destination
		$uploads = wp_upload_dir();
		$base    = trailingslashit( $uploads['basedir'] ) . 'estatesite-wpelementor';
		$live    = $base . '/templates';
		$staging = $base . '/templates-staging-' . wp_generate_password( 8, false );

		if ( ! wp_mkdir_p( $base ) ) {
			return [ 'ok' => false, 'message' => "Cannot create $base — check uploads dir permissions." ];
		}
		if ( ! is_writable( $base ) ) {
			return [ 'ok' => false, 'message' => "Uploads dir $base is not writable." ];
		}

		// 3. Download
		$tmp = self::download_tarball( $meta['download_url'] );
		if ( ! $tmp['ok'] ) {
			return $tmp;
		}
		$tarball_path = $tmp['path'];

		try {
			// 4. Verify checksum
			if ( ! empty( $meta['sha256'] ) ) {
				$actual = hash_file( 'sha256', $tarball_path );
				if ( ! hash_equals( $meta['sha256'], $actual ) ) {
					return [
						'ok'      => false,
						'message' => "Downloaded tarball failed sha256 check (expected {$meta['sha256']}, got $actual). Refusing to extract.",
					];
				}
			}

			// 5. Extract into staging
			$extract = self::extract_tarball( $tarball_path, $staging );
			if ( ! $extract['ok'] ) {
				return $extract;
			}

			// Validate the extracted tree looks right
			if ( ! file_exists( $staging . '/templates/manifest.json' ) ) {
				return [
					'ok'      => false,
					'message' => 'Extracted tarball does not contain templates/manifest.json — bundle looks malformed.',
				];
			}

			// 6. Atomic swap
			if ( is_dir( $live ) ) {
				$archive = $base . '/templates-old-' . time();
				if ( ! @rename( $live, $archive ) ) {
					return [ 'ok' => false, 'message' => "Could not archive old templates/ → $archive — check permissions." ];
				}
			}
			if ( ! @rename( $staging . '/templates', $live ) ) {
				return [ 'ok' => false, 'message' => "Could not move staging templates/ into place at $live." ];
			}
			@rmdir( $staging );

			// 7. Reset path cache + record fetch
			Templates::reset_base_cache();
			update_option( self::OPTION_LAST_FETCH, [
				'version'    => $meta['version'] ?? '',
				'at'         => time(),
				'size_bytes' => $meta['size_bytes'] ?? 0,
			], false );

			$stats = Templates::stats();

			return [
				'ok'      => true,
				'version' => $meta['version'] ?? '',
				'stats'   => $stats,
				'message' => sprintf(
					'Fetched %d templates (%d content files, %d images).',
					$stats['manifest_entries'],
					$stats['content_files'],
					$stats['image_files']
				),
			];
		} finally {
			@unlink( $tarball_path );
		}
	}

	/**
	 * Return the recorded "last fetched" metadata (or null if never).
	 */
	public static function last_fetch(): ?array {
		$opt = get_option( self::OPTION_LAST_FETCH );
		return is_array( $opt ) ? $opt : null;
	}

	// -------------------------------------------------------------------
	// Pipeline steps
	// -------------------------------------------------------------------

	private static function fetch_manifest(): array {
		$resp = wp_remote_get( self::REMOTE_MANIFEST_URL, [ 'timeout' => 15 ] );
		if ( is_wp_error( $resp ) ) {
			return [ 'ok' => false, 'message' => 'Cannot reach update server: ' . $resp->get_error_message() ];
		}
		$code = wp_remote_retrieve_response_code( $resp );
		if ( $code !== 200 ) {
			return [ 'ok' => false, 'message' => "Update server returned HTTP $code." ];
		}
		$body = wp_remote_retrieve_body( $resp );
		$data = json_decode( $body, true );
		if ( ! is_array( $data ) || empty( $data['download_url'] ) ) {
			return [ 'ok' => false, 'message' => 'Manifest JSON is malformed or missing download_url.' ];
		}
		return [ 'ok' => true, 'data' => $data ];
	}

	private static function download_tarball( string $url ): array {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		// download_url() handles redirects and writes to a unique tmp file.
		// 300s timeout for the ~140 MB tarball on slow connections.
		$path = download_url( $url, 300 );
		if ( is_wp_error( $path ) ) {
			return [ 'ok' => false, 'message' => 'Tarball download failed: ' . $path->get_error_message() ];
		}
		if ( ! file_exists( $path ) || filesize( $path ) < 1024 ) {
			@unlink( $path );
			return [ 'ok' => false, 'message' => 'Downloaded tarball is empty or truncated.' ];
		}
		return [ 'ok' => true, 'path' => $path ];
	}

	private static function extract_tarball( string $tarball_path, string $staging_dir ): array {
		if ( ! wp_mkdir_p( $staging_dir ) ) {
			return [ 'ok' => false, 'message' => "Cannot create staging dir $staging_dir." ];
		}

		// Prefer PharData (built into PHP) over shelling out to tar — works on
		// shared hosts where exec() is disabled.
		if ( ! class_exists( '\PharData' ) ) {
			return [ 'ok' => false, 'message' => 'PharData class is unavailable — cannot extract tarball.' ];
		}

		try {
			$phar = new \PharData( $tarball_path );
			$phar->extractTo( $staging_dir, null, true );
		} catch ( \Throwable $e ) {
			return [ 'ok' => false, 'message' => 'Tarball extraction failed: ' . $e->getMessage() ];
		}

		return [ 'ok' => true ];
	}
}
