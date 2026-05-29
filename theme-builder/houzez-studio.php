<?php
/**
 * Houzez Studio loader — adapted for inclusion inside estatesite-wpelementor.
 *
 * Originally a standalone WordPress plugin (Houzez Studio v1.3.3 by Favethemes).
 * Ported into EstateSite Elementor pkg in Phase 4-extended.
 *
 * Differences from the standalone version:
 *   - Plugin header docblock removed (WP must NOT detect this as a plugin)
 *   - `register_activation_hook` / `register_deactivation_hook` removed
 *     (activation lifecycle is handled by the parent wpelementor plugin)
 *   - Bootstrap function `run_houzez_studio()` is called from
 *     wpelementor's Plugin::boot(), not auto-invoked at file load
 *
 * @package EstateSite\Elementor\ThemeBuilder
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'FTS_VERSION' )            || define( 'FTS_VERSION',            '1.3.3' );
defined( 'FTS_NOTICE_MIN_PHP_VERSION' ) || define( 'FTS_NOTICE_MIN_PHP_VERSION', '7.0' );
defined( 'FTS_NOTICE_MIN_WP_VERSION' )  || define( 'FTS_NOTICE_MIN_WP_VERSION', '6.0' );
defined( 'FTS_DELIMITER' )          || define( 'FTS_DELIMITER',          '|' );
defined( 'FTS_FILE' )               || define( 'FTS_FILE',               __FILE__ );
defined( 'FTS_DIR_PATH' )           || define( 'FTS_DIR_PATH',           plugin_dir_path( __FILE__ ) );
defined( 'FTS_DIR_URL' )            || define( 'FTS_DIR_URL',            plugin_dir_url( __FILE__ ) );
defined( 'FTS_PHP_MIN_REQUIREMENTS_NOTICE' ) || define( 'FTS_PHP_MIN_REQUIREMENTS_NOTICE',
	'wp_php_min_requirements_' . FTS_NOTICE_MIN_PHP_VERSION . '_' . FTS_NOTICE_MIN_WP_VERSION );

// Core class.
require_once FTS_DIR_PATH . 'includes/class-houzez-studio.php';

/**
 * Entry point — called from wpelementor's Plugin::boot().
 * Idempotent: instantiating twice is harmless because Houzez_Studio's
 * run() method registers hooks via WordPress (which dedupes by callback).
 */
if ( ! function_exists( 'run_houzez_studio' ) ) {
	function run_houzez_studio() {
		$plugin = new HouzezStudio\Houzez_Studio();
		$plugin->run();
	}
}
