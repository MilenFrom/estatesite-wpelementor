<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Agency Properties Widget - leverages Houzez's existing property card system
 * @since 1.0
 */
class EstateSite_Agency_Properties_Widget extends Widget_Base {
    
    /**
     * Get widget name.
     */
    public function get_name() {
        return 'estatesite_agency_properties';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__( 'Agency Properties', 'estatesite-houzez' );
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'estatesite-element-icon eicon-gallery-grid';
    }

    /**
     * Get widget categories.
     */
    public function get_categories() {
        return [ 'estatesite-elements' ];
    }

    /**
     * Get widget badge.
     */
    public function get_badge() {
        return 'Estate Site';
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Agency Properties Settings', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Agency Selection
        $this->add_control(
            'agency_source',
            [
                'label'     => esc_html__( 'Agency Source', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'dynamic'  => esc_html__( 'Dynamic (from URL)', 'estatesite-houzez'),
                    'select'   => esc_html__( 'Select Agency', 'estatesite-houzez'),
                ],
                'default' => 'dynamic',
                'description' => esc_html__( 'Dynamic will read agency_id from URL parameter (e.g., ?agency_id=123)', 'estatesite-houzez' ),
            ]
        );

        $this->add_control(
            'selected_agency',
            [
                'label'    => esc_html__('Select Agency', 'estatesite-houzez'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_agencies_list(),
                'condition' => [
                    'agency_source' => 'select'
                ],
                'description' => esc_html__( 'Choose a specific agency', 'estatesite-houzez' ),
            ]
        );

        $this->add_control(
            'preview_agency_id',
            [
                'label'    => esc_html__('Preview Agency ID', 'estatesite-houzez'),
                'type'     => Controls_Manager::NUMBER,
                'default'  => '',
                'description' => esc_html__( 'For testing in editor. Leave empty to use first available agency.', 'estatesite-houzez' ),
            ]
        );

        // Property Card Style Selection
        $this->add_control(
            'card_version',
            [
                'label'     => esc_html__( 'Property Card Style', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'v1'  => esc_html__( 'Property Cards v1', 'estatesite-houzez'),
                    'v2'  => esc_html__( 'Property Cards v2', 'estatesite-houzez'),
                    'v3'  => esc_html__( 'Property Cards v3', 'estatesite-houzez'),
                    'v4'  => esc_html__( 'Property Cards v4', 'estatesite-houzez'),
                    'v5'  => esc_html__( 'Property Cards v5', 'estatesite-houzez'),
                    'v6'  => esc_html__( 'Property Cards v6', 'estatesite-houzez'),
                    'v7'  => esc_html__( 'Property Cards v7', 'estatesite-houzez'),
                    'v8'  => esc_html__( 'Property Cards v8', 'estatesite-houzez'),
                ],
                'default' => 'v2',
                'description' => esc_html__( 'Choose the property card design', 'estatesite-houzez' ),
            ]
        );

        // Layout Selection
        $this->add_control(
            'module_type',
            [
                'label'     => esc_html__( 'Layout', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid_3_cols'  => esc_html__( 'Grid View 3 Columns', 'estatesite-houzez'),
                    'grid_4_cols'  => esc_html__( 'Grid View 4 Columns', 'estatesite-houzez'),
                    'grid_2_cols'  => esc_html__( 'Grid View 2 Columns', 'estatesite-houzez'),
                    'list'         => esc_html__( 'List View', 'estatesite-houzez')
                ],
                'default' => 'grid_3_cols',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'listing_thumb',
                'exclude' => [ 'custom', 'thumbnail', 'houzez-image_masonry', 'houzez-map-info', 'houzez-variable-gallery', 'houzez-gallery' ],
                'include' => [],
                'default' => 'houzez-item-image-1',
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label'     => esc_html__('Number of Properties', 'estatesite-houzez'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 1,
                'max'       => 500,
                'step'      => 1,
                'default'   => 12,
            ]
        );

        $this->add_control(
            'sort_by',
            [
                'label'     => esc_html__( 'Sort By', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => houzez_sorting_array(),
                'default'   => 'a_date',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label'     => esc_html__( 'Pagination', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => houzez_pagination_type(),
                'default'   => 'loadmore',
            ]
        );

        $this->end_controls_section();

        // Agency Info Section
        $this->start_controls_section(
            'agency_info_section',
            [
                'label'     => esc_html__( 'Agency Information Display', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_agency_info',
            [
                'label' => esc_html__( 'Show Agency Info', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'estatesite-houzez' ),
                'label_off' => esc_html__( 'No', 'estatesite-houzez' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'agency_info_style',
            [
                'label'     => esc_html__( 'Agency Info Position', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'top'    => esc_html__( 'Above Properties', 'estatesite-houzez'),
                    'bottom' => esc_html__( 'Below Properties', 'estatesite-houzez'),
                ],
                'default' => 'top',
                'condition' => [
                    'show_agency_info' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();

        // No Results Section
        $this->start_controls_section(
            'no_results_section',
            [
                'label'     => esc_html__( 'No Results Messages', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'no_agency_message',
            [
                'label' => esc_html__( 'No Agency Message', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Please specify an agency to view properties.', 'estatesite-houzez' ),
                'description' => esc_html__( 'Message shown when no agency is specified', 'estatesite-houzez' ),
            ]
        );

        $this->add_control(
            'agency_not_found_message',
            [
                'label' => esc_html__( 'Agency Not Found Message', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Agency not found.', 'estatesite-houzez' ),
            ]
        );



        $this->add_control(
            'no_properties_message',
            [
                'label' => esc_html__( 'No Properties Message', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'No properties found for this agency.', 'estatesite-houzez' ),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get agencies list for the dropdown
     */
    private function get_agencies_list() {
        $agencies = get_posts([
            'post_type' => 'houzez_agency',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        ]);

        $options = ['' => esc_html__('Select Agency', 'estatesite-houzez')];
        foreach ($agencies as $agency) {
            $options[$agency->ID] = $agency->post_title;
        }

        return $options;
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        global $paged, $ele_thumbnail_size, $hide_button, $hide_author_date;

        // Check if we're in Elementor editor mode
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

        // Get agency ID
        $agency_id = '';

        if ($is_edit_mode) {
            // In editor mode, use preview_agency_id or first available agency
            if (!empty($settings['preview_agency_id'])) {
                $agency_id = intval($settings['preview_agency_id']);
            } else {
                // Get first available agency
                $first_agency = get_posts([
                    'post_type' => 'houzez_agency',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                    'fields' => 'ids'
                ]);
                $agency_id = !empty($first_agency) ? $first_agency[0] : '';
            }
        } else {
            // On frontend, use configured source
            if ($settings['agency_source'] === 'dynamic') {
                $agency_id = isset($_GET['agency_id']) ? intval($_GET['agency_id']) : '';
            } else {
                $agency_id = !empty($settings['selected_agency']) ? intval($settings['selected_agency']) : '';
            }
        }

        if (empty($agency_id)) {
            echo '<div class="alert alert-warning">' . esc_html($settings['no_agency_message']) . '</div>';
            return;
        }

        // Get the agency
        $agency = get_post($agency_id);
        if (!$agency || $agency->post_type !== 'houzez_agency') {
            echo '<div class="alert alert-warning">' . esc_html($settings['agency_not_found_message']) . '</div>';
            return;
        }

        // Display agency info if enabled and positioned at top
        if ($settings['show_agency_info'] === 'yes' && $settings['agency_info_style'] === 'top') {
            $this->render_agency_info($agency);
        }

        // Get properties for this agency
        // Handle pagination correctly for both front page and regular pages
        if (is_front_page()) {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        } else {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }

        $properties_ids = $this->get_agency_property_ids($agency_id);

        if (empty($properties_ids)) {
            echo '<div class="alert alert-info">' . esc_html($settings['no_properties_message']) . '</div>';
            return;
        }

        // Set up global variables for Houzez rendering
        $ele_thumbnail_size = isset($settings['thumb_size']) ? $settings['thumb_size'] : '';
        $hide_button = isset($settings['hide_button']) ? $settings['hide_button'] : '';
        $hide_author_date = isset($settings['hide_author_date']) ? $settings['hide_author_date'] : '';

        // Get module type and card version
        $module_type = $settings['module_type'];
        $card_version = isset($settings['card_version']) ? $settings['card_version'] : 'v2';

        // For list view, only v1, v2, v7, v8 are supported by Houzez
        // If user selects list view with other card versions, default to v1
        if ($module_type == 'list' && !in_array($card_version, ['v1', 'v2', 'v7', 'v8'])) {
            $card_version = 'v1';
        }

        // Get layout classes using Houzez helper function
        if (function_exists('houzez_get_layout_classes')) {
            list($cols_class, $item_view, $wrapper_class) = houzez_get_layout_classes($module_type, $card_version);
        } else {
            // Fallback if function doesn't exist
            $cols_class = 'property-cards-module-3-cols';
            $item_view = 'item-' . $card_version;
            $wrapper_class = 'property-cards-module';
        }

        // Prepare query args
        $posts_limit = $settings['posts_limit'] ?? 12;
        $pagination_type = $settings['pagination_type'] ?? 'number';

        // Ensure we have a valid array of property IDs
        // If empty, set to array(-1) to ensure no results (WordPress query convention)
        if (!empty($properties_ids)) {
            $post__in = $properties_ids;
        } else {
            $post__in = array(-1); // No results
        }

        $args = array(
            'post_type' => 'property',
            'posts_per_page' => intval($posts_limit),
            'paged' => $paged,
            'post_status' => 'publish',
            'post__in' => $post__in,
        );

        // Apply sorting
        if (function_exists('houzez_prop_sort')) {
            $args = houzez_prop_sort($args);
        }

        // Run the query
        $the_query = new \WP_Query($args);

        // Prepare attributes array for Houzez rendering
        // IMPORTANT: Pass property_ids as comma-separated string for AJAX load more
        $attributes = array(
            'posts_limit' => $posts_limit,
            'pagination_type' => $pagination_type,
            'property_ids' => implode(',', $properties_ids), // For AJAX load more to maintain agency filter
        );

        // Render properties using Houzez helper function
        if (function_exists('houzez_render_property_cards')) {
            houzez_render_property_cards($the_query, $cols_class, $item_view, $attributes, $wrapper_class, $card_version);
        } else {
            // Fallback rendering if function doesn't exist
            $this->fallback_render_properties($the_query, $item_view, $posts_limit, $pagination_type);
        }

        // Display agency info if enabled and positioned at bottom
        if ($settings['show_agency_info'] === 'yes' && $settings['agency_info_style'] === 'bottom') {
            $this->render_agency_info($agency);
        }
    }

    /**
     * Fallback rendering if Houzez functions aren't available
     */
    private function fallback_render_properties($the_query, $item_view, $posts_limit, $pagination_type) {
        ?>
        <div class="estatesite-agency-listings-wrap">
            <div class="listing-view">
                <?php
                if ($the_query->have_posts()) :
                    while ($the_query->have_posts()) : $the_query->the_post();
                        get_template_part('template-parts/listing/item', $item_view);
                    endwhile;
                    wp_reset_postdata();
                else:
                    get_template_part('template-parts/listing/item', 'none');
                endif;
                ?>
            </div>
            <?php
            if (function_exists('houzez_pagination')) {
                houzez_pagination($the_query->max_num_pages, '', $posts_limit, $pagination_type);
            }
            ?>
        </div>
        <?php
    }

    /**
     * Get property IDs for a specific agency
     * Gets properties synced from EstateAssistant via eas_agency_wp_id meta field
     */
    private function get_agency_property_ids($agency_id) {
        // Get properties synced from EstateAssistant (eas_agency_wp_id meta)
        $properties = get_posts(array(
            'post_type' => 'property',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'eas_agency_wp_id',
                    'value' => $agency_id,
                    'compare' => '='
                )
            )
        ));

        return $properties;
    }

    // All rendering is now handled by Houzez's native houzez_properties() function
    // This provides consistent styling, layout, and functionality

    /**
     * Render agency information
     */
    private function render_agency_info($agency) {
        $agency_email = get_post_meta($agency->ID, 'fave_agency_email', true);
        $agency_phone = get_post_meta($agency->ID, 'fave_agency_mobile', true);
        $agency_website = get_post_meta($agency->ID, 'fave_agency_web', true);
        
        echo '<div class="agency-info-card">';
        echo '<div class="agency-info-header">';
        
        if (has_post_thumbnail($agency->ID)) {
            echo '<div class="agency-logo">';
            echo get_the_post_thumbnail($agency->ID, 'thumbnail');
            echo '</div>';
        }
        
        echo '<div class="agency-details">';
        echo '<h3 class="agency-name">' . esc_html($agency->post_title) . '</h3>';
        
        if (!empty($agency->post_content)) {
            echo '<div class="agency-description">' . wp_kses_post($agency->post_content) . '</div>';
        }
        
        echo '<div class="agency-contact">';
        if (!empty($agency_email)) {
            echo '<span class="agency-email"><i class="fas fa-envelope"></i> ' . esc_html($agency_email) . '</span>';
        }
        if (!empty($agency_phone)) {
            echo '<span class="agency-phone"><i class="fas fa-phone"></i> ' . esc_html($agency_phone) . '</span>';
        }
        if (!empty($agency_website)) {
            echo '<span class="agency-website"><i class="fas fa-globe"></i> <a href="' . esc_url($agency_website) . '" target="_blank">' . esc_html($agency_website) . '</a></span>';
        }
        echo '</div>';
        
        echo '</div>'; // agency-details
        echo '</div>'; // agency-info-header
        echo '</div>'; // agency-info-card
    }
}

// Register the widget
Plugin::instance()->widgets_manager->register( new EstateSite_Agency_Properties_Widget() );