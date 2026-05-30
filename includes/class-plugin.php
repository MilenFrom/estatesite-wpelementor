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
		Templates_Proxy::register();

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

		// Admin: "EE Templates" info page (thin-client model). The Elementor
		// template library is no longer bundled in the plugin zip or fetched
		// as a tarball — it's served live from dev.estatesite.eu and accessed
		// at runtime via the Templates class. This page just shows status and
		// offers a "Test connection" button against the remote manifest.
		if ( is_admin() ) {
			new \EstateSite\Elementor\Admin\Templates_Page();
		}

		// Editor-only: when a single-* widget's preview-post swap fails
		// because the user hasn't picked a preview post (and no published
		// post of that type exists to fall back to), replace the otherwise
		// empty/broken render with an actionable hint pointing them at
		// Preview Settings.
		add_filter( 'elementor/widget/render_content', [ $this, 'render_preview_hint_if_needed' ], 99, 2 );

		do_action( 'estatesite_elementor_loaded', $this );
	}

	/**
	 * Wrap or replace a widget's rendered content with a "pick a preview
	 * post" hint when the trait flagged a swap failure for this render.
	 * Only runs in the Elementor editor / WP preview — frontend renders
	 * are untouched.
	 *
	 * @param string                  $content Rendered widget HTML.
	 * @param \Elementor\Widget_Base  $widget  The widget being rendered.
	 * @return string
	 */
	public function render_preview_hint_if_needed( $content, $widget ): string {
		$failed_for = Preview_Signal::consume();
		if ( $failed_for === '' ) {
			return $content;
		}

		$labels = [
			'property'      => __( 'a property', 'estatesite-wpelementor' ),
			'houzez_agent'  => __( 'an agent', 'estatesite-wpelementor' ),
			'houzez_agency' => __( 'an agency', 'estatesite-wpelementor' ),
			'post'          => __( 'a post', 'estatesite-wpelementor' ),
		];
		$label = $labels[ $failed_for ] ?? $failed_for;

		$hint = sprintf(
			'<div class="esc-preview-hint" style="border:1px dashed #b04632;background:#fff5f3;color:#621a14;padding:14px 18px;border-radius:4px;font-family:sans-serif;font-size:13px;line-height:1.5;margin:8px 0;">'
			. '<strong style="display:block;margin-bottom:4px;">%1$s</strong>'
			. '%2$s'
			. '</div>',
			esc_html__( 'No preview data available', 'estatesite-wpelementor' ),
			sprintf(
				/* translators: %s: human label for the post type, e.g. "a property". */
				esc_html__( 'Open the document settings panel (gear icon, bottom-left) and pick %s under "Preview Settings". Without one, this widget has nothing to render.', 'estatesite-wpelementor' ),
				esc_html( $label )
			)
		);

		return $hint . $content;
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
