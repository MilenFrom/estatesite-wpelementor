<?php
/**
 * Elementor package bootstrap singleton.
 *
 * @package EstateSite\Elementor
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	/** @var Plugin|null */
	private static $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->boot();
		}
		return self::$instance;
	}

	private function __construct() {}

	private function boot(): void {
		add_action( 'init', [ $this, 'load_textdomain' ], 1 );

		// Self-hosted template catalog (367 Elementor templates bundled in
		// `templates/`). Intercepts wp_remote_get to studio.houzez.co and
		// serves responses from local files. Registered early so it's ready
		// before any library code might fire HTTP requests.
		Templates::register();

		// HTF helper functions — small procedural files (2.5K LOC) that
		// Elementor widgets depend on (e.g. houzez_search_builder_custom_field_elementor).
		// Must load BEFORE elementor-loader so widget classes see these functions.
		$htf_helpers = [
			'helpers.php',
			'functions.php',
			'functions-options.php',
			'functions-rewrite.php',
			'security-helpers.php',
		];
		foreach ( $htf_helpers as $f ) {
			$path = ESELE_DIR . 'includes/htf-functions/' . $f;
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}

		// HTF utility classes that ported Elementor widgets reference at runtime.
		// Limited to manager/helper classes — we intentionally do NOT load the
		// HTF *-post-type.php files because Core already registers CPTs and a
		// double-registration would conflict.
		$htf_utility_classes = [
			'class-image-sizes.php',
			'class-currencies.php',
			'class-fields-builder.php',
			'class-menu.php',
			'class-taxonomies.php',
		];
		foreach ( $htf_utility_classes as $f ) {
			$path = ESELE_DIR . 'includes/htf-classes/' . $f;
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}

		// Ported Houzez Elementor extensions loader. Self-instantiates and
		// registers traits, categories, widgets, controls, tags, and assets
		// against the elementor/* hook family.
		require_once ESELE_DIR . 'includes/elementor-loader.php';

		// Absorbed estatesite-houzez widgets (sunset of standalone plugin).
		add_action( 'elementor/widgets/register', [ $this, 'register_absorbed_widgets' ] );

		// Theme Builder (ported from houzez-studio plugin v1.3.3).
		// Registers fts_builder CPT, render-template engine, display conditions,
		// and admin UI for managing headers/footers/single templates.
		$this->load_theme_builder();

		// Add a Theme Builder submenu under EstateSite's top-level menu.
		// Without this, the fts_builder CPT (which has show_in_menu=false)
		// has no admin entry point at all.
		add_action( 'admin_menu', [ $this, 'register_theme_builder_submenu' ], 20 );

		do_action( 'estatesite_elementor_loaded', $this );
	}

	/**
	 * Register a "Theme Builder" submenu under the EstateSite top-level menu.
	 * Links to the fts_builder CPT list. Only registered if the CPT exists
	 * (i.e. theme builder loaded successfully).
	 */
	public function register_theme_builder_submenu(): void {
		if ( ! post_type_exists( 'fts_builder' ) ) {
			return;
		}
		// Bail if the EstateSite top-level menu hasn't been registered (Core
		// inactive or registered later than priority 20).
		global $admin_page_hooks;
		if ( ! isset( $admin_page_hooks['estatesite'] ) ) {
			return;
		}
		add_submenu_page(
			'estatesite',
			__( 'Theme Builder', 'estatesite-wpelementor' ),
			__( 'Theme Builder', 'estatesite-wpelementor' ),
			'edit_pages',
			'edit.php?post_type=fts_builder'
		);
	}

	/**
	 * Load the Theme Builder subsystem (ported from houzez-studio).
	 * Skipped silently if Elementor isn't loaded — theme builder requires Elementor.
	 */
	private function load_theme_builder(): void {
		if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		$loader = ESELE_DIR . 'theme-builder/houzez-studio.php';
		if ( is_readable( $loader ) ) {
			require_once $loader;
			if ( function_exists( 'run_houzez_studio' ) ) {
				run_houzez_studio();
			}
		}
	}

	/**
	 * Load + register the 12 widgets absorbed from estatesite-houzez-master.
	 * These were originally in a separate plugin (1.7.0) that we're sunsetting.
	 */
	public function register_absorbed_widgets( $widgets_manager ): void {
		$absorbed_dir = ESELE_DIR . 'widgets/absorbed/';
		$files = [
			'class-estatesite-blog-posts.php'                => '\EstateSite\Elementor\Widgets\Blog_Posts',
			'class-estatesite-contact-form.php'              => '\EstateSite\Elementor\Widgets\Contact_Form',
			'class-estatesite-property-sub-units.php'        => '\EstateSite\Elementor\Widgets\Property_Sub_Units',
			// Other absorbed widgets are included but only register their classes
			// once we verify their dependencies don't fatal. Add to this list
			// as each is smoke-tested in later sessions.
		];

		foreach ( $files as $file => $class ) {
			$path = $absorbed_dir . $file;
			if ( ! is_readable( $path ) ) {
				continue;
			}
			require_once $path;
			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'estatesite-wpelementor',
			false,
			dirname( ESELE_BASENAME ) . '/languages'
		);
	}

	public function register_widget_category( $elements_manager ): void {
		$elements_manager->add_category( 'estatesite', [
			'title' => __( 'EstateSite', 'estatesite-wpelementor' ),
			'icon'  => 'fa fa-home',
		] );
	}
}
