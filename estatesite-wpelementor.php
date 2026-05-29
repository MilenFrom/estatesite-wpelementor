<?php
/**
 * Plugin Name: EstateSite Elementor
 * Plugin URI:  https://estatesite.eu
 * Description: Elementor widgets, dynamic tags, theme builder integration, and templates library for EstateSite.
 * Version:     0.1.0
 * Author:      Estate Site
 * Author URI:  https://estatesite.eu
 * Text Domain: estatesite-wpelementor
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Requires Plugins: estatesite-wpcore, elementor
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

define( 'ESELE_VERSION',  '0.1.0' );
define( 'ESELE_FILE',     __FILE__ );
define( 'ESELE_DIR',      plugin_dir_path( __FILE__ ) );
define( 'ESELE_URL',      plugin_dir_url( __FILE__ ) );
define( 'ESELE_BASENAME', plugin_basename( __FILE__ ) );

/*
 * Ported Houzez widget files reference HOUZEZ_PLUGIN_URL (and a few other
 * HTF constants) to build asset URLs like:
 *   HOUZEZ_PLUGIN_URL . 'elementor/assets/css/author-box.css'
 *
 * Alias them to our plugin paths. Guarded with defined() so a still-active
 * houzez-theme-functionality plugin would win — but in our deployment that
 * plugin is sunsetted.
 */
defined( 'HOUZEZ_PLUGIN_URL' )          || define( 'HOUZEZ_PLUGIN_URL',          ESELE_URL );
defined( 'HOUZEZ_PLUGIN_DIR' )          || define( 'HOUZEZ_PLUGIN_DIR',          ESELE_DIR );
defined( 'HOUZEZ_PLUGIN_PATH' )         || define( 'HOUZEZ_PLUGIN_PATH',         ESELE_DIR );
defined( 'HOUZEZ_PLUGIN_BASENAME' )     || define( 'HOUZEZ_PLUGIN_BASENAME',     ESELE_BASENAME );
defined( 'HOUZEZ_PLUGIN_IMAGES_URL' )   || define( 'HOUZEZ_PLUGIN_IMAGES_URL',   ESELE_URL . 'assets/images/' );
defined( 'HOUZEZ_TEMPLATES' )           || define( 'HOUZEZ_TEMPLATES',           ESELE_DIR . 'templates/' );
defined( 'HOUZEZ_DS' )                  || define( 'HOUZEZ_DS',                  DIRECTORY_SEPARATOR );
defined( 'HOUZEZ_VERSION' )             || define( 'HOUZEZ_VERSION',             ESELE_VERSION );
defined( 'HOUZEZ_DB_VERSION' )          || define( 'HOUZEZ_DB_VERSION',          '4.0.0' );
defined( 'HOUZEZ_PLUGIN_CORE_VERSION' ) || define( 'HOUZEZ_PLUGIN_CORE_VERSION', ESELE_VERSION );

// Activation hook: populate wp-content/uploads/estatesite-wpelementor/ from
// the bundled source-assets/ dir so template-inserted SVGs/PNGs survive
// future plugin updates (uploads/ is preserved, plugin dir is wiped+replaced).
register_activation_hook( __FILE__, function () {
	require_once __DIR__ . '/includes/class-templates.php';
	\EstateSite\Elementor\Templates::populate_uploads_dir( false );
	// Mark as populated so the admin_init lazy-populate doesn't re-check.
	update_option( 'estatesite_templates_uploads_populated', time(), false );
} );

// PSR-4 autoloader.
spl_autoload_register( function ( $class ) {
	if ( strpos( $class, 'EstateSite\\Elementor\\' ) !== 0 ) {
		return;
	}
	$relative = substr( $class, strlen( 'EstateSite\\Elementor\\' ) );
	$parts    = explode( '\\', $relative );
	$last     = array_pop( $parts );
	$file     = ESELE_DIR . 'includes/';
	if ( $parts ) {
		$file .= strtolower( implode( '/', $parts ) ) . '/';
	}
	$file .= 'class-' . strtolower( str_replace( '_', '-', $last ) ) . '.php';
	if ( is_readable( $file ) ) {
		require_once $file;
	}
} );

// Ported Houzez framework classes live under namespace Houzez\Classes (kept
// verbatim so widget code that does `new Classes\houzez_plugin_nav_walker()`
// resolves without rewrites). Files live in includes/htf-classes/ and don't
// follow PSR-4 filenames, so register a small map-based loader.
spl_autoload_register( function ( $class ) {
	if ( strpos( $class, 'Houzez\\Classes\\' ) !== 0 ) {
		return;
	}
	static $map = [
		'houzez_plugin_nav_walker'        => 'menu-walker.php',
		'houzez_plugin_mobile_nav_walker' => 'mobile-menu-walker.php',
	];
	$short = substr( $class, strlen( 'Houzez\\Classes\\' ) );
	if ( ! isset( $map[ $short ] ) ) {
		return;
	}
	$file = ESELE_DIR . 'includes/htf-classes/' . $map[ $short ];
	if ( is_readable( $file ) ) {
		require_once $file;
	}
} );

// ---------------------------------------------------------------------------
// Update pipeline — native WP filter pointing at our own JSON manifest.
// See estatesite-wpcore for the architecture rationale. The Update_Checker
// class lives in Core, which is a hard dependency loaded before this file.
// We register the hook at plugins_loaded priority 6 (after Core's priority 5
// bootstrap) so the class is available.
// ---------------------------------------------------------------------------
add_action( 'plugins_loaded', static function () {
	if ( ! class_exists( '\EstateSite\Core\Update_Checker' ) ) {
		return; // Core missing — admin notice already shown by dep check below.
	}
	new \EstateSite\Core\Update_Checker(
		'plugin',
		ESELE_BASENAME,
		ESELE_VERSION,
		defined( 'ESTATESITE_UPDATE_ENDPOINT_WPELEMENTOR' )
			? ESTATESITE_UPDATE_ENDPOINT_WPELEMENTOR
			: 'https://dev.estatesite.eu/updates/estatesite-wpelementor.json'
	);
}, 6 );

// Bootstrap at default priority — Core (priority 5) is already loaded.
add_action( 'plugins_loaded', function () {
	// Hard dependency: Core plugin.
	if ( ! defined( 'ESCORE_VERSION' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>'
				. esc_html__( 'EstateSite Elementor requires EstateSite Core plugin to be active.', 'estatesite-wpelementor' )
				. '</p></div>';
		} );
		return;
	}

	// Hard dependency: Elementor (free is enough).
	if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>'
				. esc_html__( 'EstateSite Elementor requires the Elementor plugin to be active.', 'estatesite-wpelementor' )
				. '</p></div>';
		} );
		return;
	}

	\EstateSite\Elementor\Plugin::instance();
}, 10 );
