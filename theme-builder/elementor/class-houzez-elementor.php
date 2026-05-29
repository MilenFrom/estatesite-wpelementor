<?php
namespace HouzezStudio;
use Elementor\Controls_Manager;
/*use Elementor\Core\DynamicTags\Base_Tag;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post;*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'FTS_Elementor' ) ) {
    final class FTS_Elementor {

        const HOUZEZ_GROUP = 'houzez_studio';

        /**
         * Current theme template
         *
         * @var String
         */
        public $template;

        /**
         * Instance of Elemenntor Frontend class.
         *
         * @var \Elementor\Frontend()
         */
        private static $elementor_instance;

        /**
         * The single instance of the class.
         *
         * @var FTS_Elementor
         * @since 1.0
         */
        private static $_instance;

        /**
         * Main FTS_Elementor Instance.
         *
         * Ensures only one instance of FTS_Elementor is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @return FTS_Elementor - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor function.
         * @access  public
         * @since   1.0.0
         * @return  void
         */
        public function __construct() {
            if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
                self::$elementor_instance = \Elementor\Plugin::instance();
                // Scripts and styles.
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                add_shortcode( 'fts_template', array( $this, 'render_shortcode' ) );

                // ----------------------------------------------------------------
                // Preview Settings (3 hooks).
                //
                // (1) Editor sidebar panel + (2) Apply & Preview JS handler:
                add_action( 'elementor/documents/register_controls',     array( $this, 'add_preview_settings_section' ) );
                add_action( 'elementor/editor/after_enqueue_scripts',    array( $this, 'enqueue_editor_scripts' ) );

                // (3) Swap render context to the preview target post via TWO
                // entry points, because Houzez widgets and Elementor stock
                // widgets travel different paths:
                //
                //   - `elementor/dynamic_tags/before_render` fires inside
                //     ajax_render_tags() for widgets that use Elementor's
                //     dynamic-tag system (post-title, post-meta, etc.). Stock
                //     Elementor Pro widgets all go through this.
                //
                //   - `pre_get_posts` inside ajax_render_widget covers
                //     Houzez widgets (houzez-property-title, etc.) which
                //     read get_post_meta(get_the_ID(), 'fave_*') directly,
                //     bypassing dynamic tags entirely. Elementor's
                //     ajax_render_widget calls query_posts(['p' => editor_post_id])
                //     just before render. We intercept that query and rewrite
                //     'p' to the preview target so the loop sees real data.
                //
                // The iframe URL is left untouched (Elementor's default:
                // fts_builder permalink + ?elementor-preview=<id>). Rewriting
                // it causes is_preview_mode() to fail and the editor sticks
                // loading — see commit history if you're tempted to try.
                add_action( 'elementor/dynamic_tags/before_render',      array( $this, 'switch_to_preview_query' ) );
                add_action( 'elementor/dynamic_tags/after_render',       array( $this, 'restore_preview_query' ) );
                add_action( 'pre_get_posts',                             array( $this, 'rewrite_render_widget_query' ), 1 );

                // body_class injection — make the preview iframe's <body> carry
                // the same classes the target post's real frontend page would
                // have (single-property, postid-<target>, etc.) so Houzez CSS
                // scoped to those classes applies during preview.
                add_filter( 'body_class',                                array( $this, 'filter_preview_body_class' ), 100 );
            }
        }

        /**
         * Enqueue styles and scripts.
         */
        public function enqueue_scripts() {

            if (! class_exists( '\Elementor\Plugin' ) ) {
                return;
            }
            
            // Skip on admin pages to prevent timeouts
            if ( is_admin() ) {
                return;
            }
            
            // Skip on non-singular pages for performance
            if ( ! is_singular() && ! is_home() && ! is_front_page() && ! is_archive() ) {
                return;
            }

            // Enqueue Elementor and Elementor Pro styles
            $elementor = \Elementor\Plugin::instance();
            $elementor->frontend->enqueue_styles();

            if ( class_exists( '\ElementorPro\Plugin' ) ) {
                $elementor_pro = \ElementorPro\Plugin::instance();
                if ( method_exists( $elementor_pro, 'enqueue_styles' ) ) {
                    $elementor_pro->enqueue_styles();
                }
            }

            // Only load templates that are needed for the current page type
            $section_ids = [];
            
            // Always load header and footer on frontend
            if ( ! is_admin() ) {
                $header_id = fts_get_header_id();
                if ( $header_id ) {
                    $section_ids['header'] = $header_id;
                }
                
                $footer_id = fts_get_footer_id();
                if ( $footer_id ) {
                    $section_ids['footer'] = $footer_id;
                }
                
                // Only load before/after header/footer if they exist
                if ( $before_header = fts_get_before_header_id() ) {
                    $section_ids['before_header'] = $before_header;
                }
                if ( $after_header = fts_get_after_header_id() ) {
                    $section_ids['after_header'] = $after_header;
                }
                if ( $before_footer = fts_get_before_footer_id() ) {
                    $section_ids['before_footer'] = $before_footer;
                }
                if ( $after_footer = fts_get_after_footer_id() ) {
                    $section_ids['after_footer'] = $after_footer;
                }
            }
            
            // Only load single templates on relevant single pages
            if ( is_singular() ) {
                $post_type = get_post_type();
                
                if ( $post_type === 'property' && ( $single_id = fts_get_single_listing_id() ) ) {
                    $section_ids['single_listing'] = $single_id;
                } elseif ( $post_type === 'houzez_agent' && ( $single_id = fts_get_single_agent_id() ) ) {
                    $section_ids['single_agent'] = $single_id;
                } elseif ( $post_type === 'houzez_agency' && ( $single_id = fts_get_single_agency_id() ) ) {
                    $section_ids['single_agency'] = $single_id;
                } elseif ( $post_type === 'post' && ( $single_id = fts_get_single_post_id() ) ) {
                    $section_ids['single_post'] = $single_id;
                }
            }

            // Enqueue only the needed styles
            foreach ($section_ids as $id) {
                $this->enqueue_section_styles($id);
            }
        }

        /**
         * Enqueue styles for a specific section if the section ID is valid.
         *
         * @param int|false $section_id The ID of the section.
         */
        private function enqueue_section_styles($section_id) {
            if (!$section_id) {
                return;
            }
            
            // Check if the post exists and is published
            $post = get_post($section_id);
            if (!$post || $post->post_status !== 'publish') {
                return;
            }
            
            try {
                // Use transient to cache CSS file generation status
                $cache_key = 'fts_css_generated_' . $section_id;
                $css_generated = get_transient($cache_key);
                
                if ($css_generated === false) {
                    if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                        $css_file = new \Elementor\Core\Files\CSS\Post($section_id);
                    } elseif (class_exists('\Elementor\Post_CSS_File')) {
                        $css_file = new \Elementor\Post_CSS_File($section_id);
                    } else {
                        return; // Elementor CSS classes not available
                    }
                    
                    // Check if CSS file exists before enqueueing
                    if (method_exists($css_file, 'is_css_file_exists') && !$css_file->is_css_file_exists()) {
                        // Try to generate the CSS file with a timeout
                        if (method_exists($css_file, 'update')) {
                            // Temporarily set a short timeout for CSS generation
                            add_filter('http_request_timeout', function() { return 2; }, 999);
                            $css_file->update();
                            remove_filter('http_request_timeout', function() { return 2; }, 999);
                        }
                    }
                    
                    $css_file->enqueue();
                    
                    // Cache that CSS was generated for 1 hour
                    set_transient($cache_key, true, HOUR_IN_SECONDS);
                } else {
                    // CSS already generated, just enqueue it
                    if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                        $css_file = new \Elementor\Core\Files\CSS\Post($section_id);
                    } elseif (class_exists('\Elementor\Post_CSS_File')) {
                        $css_file = new \Elementor\Post_CSS_File($section_id);
                    } else {
                        return;
                    }
                    $css_file->enqueue();
                }
            } catch (Exception $e) {
                // Log error but don't break the page
                error_log('Houzez Studio: Failed to enqueue CSS for section ' . $section_id . ': ' . $e->getMessage());
            }
        }


        /**
         * Renders content for a shortcode.
         *
         * This method handles the shortcode rendering process by enqueuing necessary styles and
         * retrieving the content generated by Elementor based on the provided shortcode attributes.
         *
         * @param array $atts Attributes for the shortcode.
         * @return string The rendered content.
         */
        public function render_shortcode($atts) {
            // Parse and sanitize the shortcode attributes
            $atts = shortcode_atts(['id' => ''], $atts, 'fts_template');
            $id = !empty($atts['id']) ? intval(apply_filters('fts_render_template_id', $atts['id'])) : '';

            // Return early if the ID is empty
            if (empty($id)) {
                return '';
            }

            // Enqueue the CSS file for the Elementor content, if available
            $this->enqueue_elementor_css($id);

            // Return the content rendered by Elementor
            return self::$elementor_instance->frontend->get_builder_content_for_display($id);
        }

        /**
         * Enqueues Elementor CSS file for a given post ID.
         *
         * @param int $id The post ID.
         */
        private function enqueue_elementor_css($id) {
            if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                $css_file = new \Elementor\Core\Files\CSS\Post($id);
            } elseif (class_exists('\Elementor\Post_CSS_File')) {
                $css_file = new \Elementor\Post_CSS_File($id);
            }

            if (isset($css_file)) {
                $css_file->enqueue();
            }
        }


        /**
         * Get Elemetor Content Template.
         *
         * @param boolean $with_css | with css.
         * @return Header Template.
         */
        public static function get_elementor_template( $id = null, $with_css = false ) {

            $id = !empty($id) ? intval(apply_filters('fts_render_template_id', $id)) : '';
            
            return self::$elementor_instance->frontend->get_builder_content_for_display( $id );
        }

        /**
         * Register the "Preview Settings" panel on the editor sidebar for
         * fts_builder documents. The panel lets the user pick which real post
         * to use as the preview target so dynamic tags resolve against real
         * data instead of rendering empty placeholders.
         *
         * Hooked to `elementor/documents/register_controls` (every document
         * gets this filter, so we filter to fts_builder posts only).
         *
         * Behavior by template type:
         *   - single-listing  → forced post type `property`
         *   - single-agent    → forced post type `houzez_agent`
         *   - single-agency   → forced post type `houzez_agency`
         *   - single-post     → forced post type `post`
         *   - tmp_header/footer/etc. (layout) → user picks any public CPT
         *
         * The preview iframe URL is rewritten by filter_preview_url() below
         * to point at the chosen post's permalink + ?elementor-preview={id}.
         */
        public function add_preview_settings_section( \Elementor\Controls_Stack $controls_stack ) {
            $post_id = $controls_stack->get_main_id();
            if ( ! $post_id || get_post_type( $post_id ) !== 'fts_builder' ) {
                return;
            }

            $template_type = houzez_tb_get_template_type( $post_id );
            $forced_pt     = houzez_tb_preview_post_type_for( $template_type );
            if ( $forced_pt === false ) {
                // Unknown template type — skip the panel rather than show a
                // useless picker.
                return;
            }

            // Build the post-type dropdown.
            //   - If template type forces a single post type, that's the only
            //     option (user can't choose anything else).
            //   - Otherwise (layout templates), include `page` + `post` + every
            //     public CPT except infra ones.
            if ( is_string( $forced_pt ) ) {
                $pt_obj = get_post_type_object( $forced_pt );
                $post_types = [
                    $forced_pt => $pt_obj ? $pt_obj->labels->singular_name : $forced_pt,
                ];
                $default_pt = $forced_pt;
            } else {
                $post_types = [
                    'page' => __( 'Page', 'houzez-studio' ),
                    'post' => __( 'Post', 'houzez-studio' ),
                ];
                $cpts = get_post_types( [
                    'public'   => true,
                    '_builtin' => false,
                ], 'objects' );
                foreach ( $cpts as $cpt ) {
                    if ( in_array( $cpt->name, [ 'elementor_library', 'fts_builder', 'e-landing-page' ], true ) ) {
                        continue;
                    }
                    $post_types[ $cpt->name ] = $cpt->labels->singular_name;
                }
                $default_pt = 'page';
            }

            $controls_stack->start_controls_section(
                'houzez_preview_settings',
                [
                    'label' => esc_html__( 'Preview Settings', 'houzez-studio' ),
                    'tab'   => Controls_Manager::TAB_SETTINGS,
                ]
            );

            $controls_stack->add_control(
                'houzez_preview_type',
                [
                    'label'       => esc_html__( 'Preview Dynamic Content as', 'houzez-studio' ),
                    'type'        => Controls_Manager::SELECT,
                    'label_block' => true,
                    'options'     => $post_types,
                    'default'     => $default_pt,
                    'save_always' => true,
                ]
            );

            // One autocomplete control per post type — only the one matching
            // the current `houzez_preview_type` is visible (condition).
            foreach ( array_keys( $post_types ) as $pt_name ) {
                $controls_stack->add_control(
                    'houzez_preview_post_' . $pt_name,
                    [
                        'label'         => esc_html__( 'Preview as', 'houzez-studio' ),
                        'type'          => 'houzez_autocomplete',
                        'make_search'   => 'houzez_get_posts',
                        'render_result' => 'houzez_render_posts_title',
                        'post_type'     => $pt_name,
                        'label_block'   => true,
                        'multiple'      => false,
                        'condition'     => [
                            'houzez_preview_type' => $pt_name,
                        ],
                    ]
                );
            }

            // Apply & Preview button. Event name MUST match the listener in
            // theme-builder/admin/js/editor.js — `HouzezApplyPreview`.
            $controls_stack->add_control(
                'houzez_apply_preview',
                [
                    'type'        => Controls_Manager::BUTTON,
                    'label'       => esc_html__( 'Apply & Preview', 'houzez-studio' ),
                    'label_block' => true,
                    'show_label'  => false,
                    'text'        => esc_html__( 'Apply & Preview', 'houzez-studio' ),
                    'separator'   => 'none',
                    'event'       => 'HouzezApplyPreview',
                ]
            );

            $controls_stack->end_controls_section();
        }

        /**
         * Filter `elementor/document/urls/preview` so the editor iframe loads
         * the chosen target post (not the fts_builder post itself). Returns
         * unchanged URL when:
         *   - Document isn't an fts_builder post, or
         *   - No preview target has been picked yet, or
         *   - Selected target post doesn't exist anymore.
         */

        /**
         * Enqueue the Apply & Preview click handler in the editor.
         * Hooked to `elementor/editor/after_enqueue_scripts`.
         */
        public function enqueue_editor_scripts() {
            wp_enqueue_script(
                'houzez-studio-editor',
                FTS_DIR_URL . 'admin/js/editor.js',
                [ 'jquery', 'elementor-editor' ],
                FTS_VERSION,
                true
            );
        }

        /**
         * Cache for the resolved preview document, to avoid repeating the
         * `get_post()` + meta lookups on every dynamic-tag render.
         */
        private $preview_document_cache = null;

        /**
         * Resolve the fts_builder document being previewed in the current
         * request, if any. Returns the fts_builder post object, or null when
         * we're not inside a preview context.
         *
         * Detection: ?elementor-preview={id}&ver=… on the current request,
         * with the ID resolving to an fts_builder post. This is what
         * Elementor sets when loading the preview iframe.
         */
        private function get_active_preview_document() {
            if ( $this->preview_document_cache !== null ) {
                return $this->preview_document_cache ?: null;
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $preview_id = isset( $_GET['elementor-preview'] ) ? (int) $_GET['elementor-preview'] : 0;
            if ( ! $preview_id ) {
                $this->preview_document_cache = false;
                return null;
            }

            $post = get_post( $preview_id );
            if ( ! $post || $post->post_type !== 'fts_builder' ) {
                $this->preview_document_cache = false;
                return null;
            }

            $this->preview_document_cache = $post;
            return $post;
        }

        /**
         * Hook target: `elementor/dynamic_tags/before_render`.
         *
         * Fires inside Elementor's `ajax_render_tags()`, AFTER it calls
         * switch_to_post($data['post_id']). So at this point get_the_ID() = the
         * fts_builder ID being edited. If that fts_builder has a preview target
         * configured, swap the global query to the target so dynamic tags
         * (price, address, title, etc.) read real data instead of nothing.
         *
         * Mirrors Elementor Pro's Preview_Manager::switch_to_preview_query().
         * db->switch_to_query() uses a separate stack from switch_to_post, so
         * stacking is safe.
         */
        public function switch_to_preview_query() {
            $current_id = get_the_ID();
            if ( ! $current_id || get_post_type( $current_id ) !== 'fts_builder' ) {
                return;
            }

            $page_settings = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
            $model         = $page_settings->get_model( $current_id );
            $preview_type  = $model->get_settings( 'houzez_preview_type' );
            if ( ! $preview_type ) {
                return;
            }

            $target_id = (int) $model->get_settings( 'houzez_preview_post_' . $preview_type );
            if ( ! $target_id ) {
                return;
            }

            $target = get_post( $target_id );
            if ( ! $target || $target->post_status !== 'publish' || $target->post_type !== $preview_type ) {
                return;
            }

            \Elementor\Plugin::$instance->db->switch_to_query( [
                'p'         => $target_id,
                'post_type' => $preview_type,
            ], true );
        }

        /**
         * Hook target: `elementor/dynamic_tags/after_render`.
         * Pops the switch pushed in switch_to_preview_query().
         *
         * Always calls restore (unconditionally) because db->restore_current_query
         * is a no-op when its stack is empty — safer than mirroring the same
         * conditional checks here and risking a stack leak if a check disagrees.
         */
        public function restore_preview_query() {
            \Elementor\Plugin::$instance->db->restore_current_query();
        }

        /**
         * Hook target: `pre_get_posts` (priority 1).
         *
         * Elementor's `ajax_render_widget` (widgets.php line ~489) calls
         * `query_posts(['p' => editor_post_id])` immediately before invoking
         * the widget's render(). The editor_post_id is always the fts_builder
         * being edited, which means widgets calling `get_post_meta(get_the_ID(), ...)`
         * see the fts_builder's metadata (empty for property-* fields).
         *
         * Houzez widgets read meta directly without going through dynamic
         * tags, so the dynamic_tags/before_render swap above doesn't help
         * them. Intercept at pre_get_posts instead: when we detect we're
         * inside an elementor_ajax render_widget request AND the query is
         * for an fts_builder with a configured preview target, rewrite the
         * query to load the target property instead.
         *
         * Scoping: we only act when ALL of these are true to avoid touching
         * unrelated queries:
         *   - WP is processing an AJAX request (wp_doing_ajax)
         *   - The action is elementor_ajax
         *   - The requested action includes 'render_widget' (or 'render_widgets')
         *   - The query is for a single post by ID (`p` set)
         *   - That post is an fts_builder with valid preview settings
         */
        public function rewrite_render_widget_query( $query ) {
            // Detect editor render_widget AJAX. wp_doing_ajax() is the cheap
            // gate — bail out fast on every non-AJAX request.
            if ( ! wp_doing_ajax() ) {
                return;
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $action = $_REQUEST['action'] ?? '';
            if ( $action !== 'elementor_ajax' ) {
                return;
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $actions_json = $_REQUEST['actions'] ?? '';
            if ( ! is_string( $actions_json ) || strpos( $actions_json, 'render_widget' ) === false ) {
                return;
            }

            // The query must be for the fts_builder by ID (this is the shape
            // Elementor passes to query_posts).
            $p = (int) $query->get( 'p' );
            if ( ! $p || get_post_type( $p ) !== 'fts_builder' ) {
                return;
            }

            // Look up the preview target. Prefer the autosave's settings
            // when one exists — Apply & Preview triggers `document/save/auto`
            // which writes to a revision child, not the main post, so the
            // freshest user choice lives there. See the matching logic in
            // Houzez_Preview_Query::fts_resolve_preview_target() for full
            // explanation.
            $settings_doc_id = $p;
            if ( function_exists( 'wp_get_post_autosave' ) ) {
                $autosave = wp_get_post_autosave( $p, get_current_user_id() );
                if ( $autosave ) {
                    $autosave_settings = get_post_meta( $autosave->ID, '_elementor_page_settings', true );
                    if ( is_array( $autosave_settings ) && ! empty( $autosave_settings ) ) {
                        $settings_doc_id = $autosave->ID;
                    }
                }
            }

            $page_settings = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
            $model         = $page_settings->get_model( $settings_doc_id );
            $preview_type  = $model->get_settings( 'houzez_preview_type' );
            if ( ! $preview_type ) {
                return;
            }
            $target_id = (int) $model->get_settings( 'houzez_preview_post_' . $preview_type );
            if ( ! $target_id ) {
                return;
            }
            $target = get_post( $target_id );
            if ( ! $target || $target->post_status !== 'publish' || $target->post_type !== $preview_type ) {
                return;
            }

            // Rewrite the query to the preview target. The widget's render()
            // method will then see get_the_ID() == $target_id and any
            // `get_post_meta(get_the_ID(), ...)` calls will hit real data.
            $query->set( 'p',         $target_id );
            $query->set( 'post_type', $preview_type );

            // Ensure the global $post + loop data are set up after the query
            // runs. Elementor's ajax_render_widget calls query_posts() but
            // never the_post(), so without this hook many widgets read
            // $post->ID === null even though $wp_query->post is set.
            //
            // Capture the target id in closure scope so we don't have to
            // re-read settings inside the later hook (avoids redundant work
            // on every query during the same request).
            $captured_target = $target_id;
            $captured_type   = $preview_type;
            add_filter( 'the_posts', function( $posts, $q ) use ( $captured_target, $captured_type, $query ) {
                // Only setup_postdata for the EXACT query we modified, not
                // any sub-query that happens to have the same vars.
                if ( $q !== $query ) {
                    return $posts;
                }
                if ( ! empty( $posts[0] ) && $posts[0]->ID === $captured_target ) {
                    $GLOBALS['post'] = $posts[0];
                    setup_postdata( $posts[0] );
                }
                return $posts;
            }, 10, 2 );
        }

        /**
         * Hook target: `body_class` (priority 100 — late, after WP defaults).
         *
         * The preview iframe loads at `/houzez-studio/single-property/?elementor-preview=<id>`
         * with the main query on an `fts_builder` post. WP's default
         * body_class therefore adds `single-fts_builder` and `postid-<fts_id>`,
         * NOT the classes the *real* target page (e.g. single-property) would
         * have. Houzez frontend CSS scoped to `.single-property`, `.single-houzez_agent`,
         * etc. doesn't match, so widgets render unstyled.
         *
         * Fix: when we detect editor preview mode for an fts_builder doc that
         * has a configured preview target, REPLACE the fts_builder-specific
         * classes with the target's classes. Other body classes (site theme,
         * header position, etc.) pass through unchanged.
         *
         * Applies regardless of fts_template_type — even for headers/footers,
         * users typically pick a property/agent/etc. as preview target and
         * want the styled chrome to match that context.
         */
        public function filter_preview_body_class( $classes ) {
            // Only run inside editor preview mode (the iframe).
            if ( is_admin() ) {
                return $classes;
            }
            if ( ! \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
                return $classes;
            }

            $doc_id = get_queried_object_id();
            if ( ! $doc_id || get_post_type( $doc_id ) !== 'fts_builder' ) {
                return $classes;
            }

            // Resolve the preview target — autosave first, then main post.
            // Same logic as the trait/render_widget hook.
            $settings_doc_id = $doc_id;
            if ( function_exists( 'wp_get_post_autosave' ) ) {
                $autosave = wp_get_post_autosave( $doc_id, get_current_user_id() );
                if ( $autosave ) {
                    $auto_settings = get_post_meta( $autosave->ID, '_elementor_page_settings', true );
                    if ( is_array( $auto_settings ) && ! empty( $auto_settings ) ) {
                        $settings_doc_id = $autosave->ID;
                    }
                }
            }
            $page_settings = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
            $model         = $page_settings->get_model( $settings_doc_id );
            $preview_type  = $model->get_settings( 'houzez_preview_type' );
            if ( ! $preview_type ) {
                return $classes;
            }
            $target_id = (int) $model->get_settings( 'houzez_preview_post_' . $preview_type );
            if ( ! $target_id ) {
                return $classes;
            }
            $target = get_post( $target_id );
            if ( ! $target || $target->post_status !== 'publish' || $target->post_type !== $preview_type ) {
                return $classes;
            }

            // Strip fts_builder-specific classes that WP added.
            $strip = [
                'single-fts_builder',
                'fts_builder-template-default',
                'postid-' . $doc_id,
            ];
            $classes = array_values( array_diff( $classes, $strip ) );

            // Inject target's classes (matching what get_body_class() would
            // generate on the target's own permalink). Avoid duplicates.
            $inject = [
                'single-' . $preview_type,
                $preview_type . '-template-default',
                'postid-' . $target_id,
            ];
            foreach ( $inject as $class ) {
                if ( ! in_array( $class, $classes, true ) ) {
                    $classes[] = $class;
                }
            }

            return $classes;
        }

    }
}
FTS_Elementor::instance();