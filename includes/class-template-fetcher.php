<?php
/**
 * DEPRECATED — No-op shim for the legacy template tarball fetcher.
 *
 * The Elementor template library is no longer bundled as a downloadable
 * tarball. The library is now served as a thin client from
 * dev.estatesite.eu directly, so there is nothing to fetch, extract, or
 * stage locally.
 *
 * This class is retained only so that any lingering callers (older admin
 * pages, hooks, or third-party code) do not fatal. Both public methods
 * return inert values:
 *
 *   - fetch()      → [ 'ok' => false, 'message' => '...deprecated...' ]
 *   - last_fetch() → null
 *
 * Do not add new callers. Use the Templates class directly for any
 * library-status queries.
 *
 * @package EstateSite\Elementor
 * @deprecated The tarball pipeline has been removed; this is a shim.
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Template_Fetcher {

	/**
	 * No-op. Returns a deprecation notice in the standard result envelope.
	 *
	 * @return array { ok: bool, message: string }
	 */
	public static function fetch(): array {
		return [
			'ok'      => false,
			'message' => 'Template_Fetcher is deprecated. The Elementor template library is now served from dev.estatesite.eu directly. No local fetch is required.',
		];
	}

	/**
	 * No-op. There is no recorded "last fetch" — fetching no longer happens.
	 *
	 * @return null
	 */
	public static function last_fetch(): ?array {
		return null;
	}
}
