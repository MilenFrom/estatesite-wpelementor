<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enhanced Property Cards Widget with Advanced Query Filters
 * Based on Houzez Property Cards v1 but with advanced location filtering capabilities
 * 
 * @since 1.0.0
 */
class EstateSite_Property_Cards_Advanced extends Widget_Base {
    use Houzez_Filters_Traits;
    use Houzez_Property_Cards_Traits;

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'estatesite_property_cards_advanced';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__( 'Property Cards Advanced', 'estatesite-houzez' );
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
                'label'     => esc_html__( 'Properties', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'module_type',
            [
                'label'     => esc_html__( 'Layout', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid_3_cols'  => esc_html__( 'Grid View 3 Columns', 'estatesite-houzez'),
                    'grid_4_cols'  => esc_html__( 'Grid View 4 Columns', 'estatesite-houzez'),
                    'grid_2_cols'    => esc_html__( 'Grid View 2 Columns', 'estatesite-houzez'),
                    'list'    => esc_html__( 'List View', 'estatesite-houzez')
                ],
                'description' => '',
                'default' => 'grid_3_cols',
            ]
        );

        #$this->listings_cards_thumb_size_control();
        
        $this->listings_cards_general_filters();

        $this->add_control(
            'pagination_type',
            [
                'label'     => esc_html__( 'Pagination', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => houzez_pagination_type(),
                'description' => '',
                'default' => 'loadmore',
            ]
        );

        $this->add_control(
            'warning_panel_notice',
            [
                'type' => \Elementor\Controls_Manager::NOTICE,
                'notice_type' => 'warning',
                'dismissible' => false,
                'heading' => esc_html__( 'Warning', 'estatesite-houzez' ),
                'content' => esc_html__( 'Infinite Scroll works with only one widget per page; enabling it for multiple widgets on the same page will cause issues.', 'estatesite-houzez' ),
                'condition' => [
                    'pagination_type' => 'infinite_scroll'
                ]
            ]
        );
        
        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Advanced Location Filters
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'advanced_filters_section',
            [
                'label'     => esc_html__( 'Advanced Location Filters', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'advanced_filter_notice',
            [
                'type' => \Elementor\Controls_Manager::NOTICE,
                'notice_type' => 'info',
                'dismissible' => false,
                'heading' => esc_html__( 'Advanced Filtering', 'estatesite-houzez' ),
                'content' => esc_html__( 'Use this section to create complex location queries. For example: Show properties from Bulgaria (Burgas city only) AND Greece (all cities). Add multiple location groups below.', 'estatesite-houzez' ),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'group_name',
            [
                'label' => esc_html__( 'Group Name', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Location Group', 'estatesite-houzez' ),
                'placeholder' => esc_html__( 'e.g., Bulgaria Properties', 'estatesite-houzez' ),
            ]
        );

        $repeater->add_control(
            'country',
            [
                'label'         => esc_html__('Country', 'estatesite-houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_country'),
            ]
        );

        $repeater->add_control(
            'state',
            [
                'label'         => esc_html__('State', 'estatesite-houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_state'),
            ]
        );

        $repeater->add_control(
            'city',
            [
                'label'         => esc_html__('City', 'estatesite-houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_city'),
            ]
        );

        $repeater->add_control(
            'area',
            [
                'label'         => esc_html__('Area', 'estatesite-houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_area'),
            ]
        );

        $this->add_control(
            'location_groups',
            [
                'label' => esc_html__( 'Location Groups', 'estatesite-houzez' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'group_name' => esc_html__( 'Primary Location', 'estatesite-houzez' ),
                    ],
                ],
                'title_field' => '{{{ group_name }}}',
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Standard Filters
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'filters_section',
            [
                'label'     => esc_html__( 'Standard Filters', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        // Add standard filters but exclude location ones since we have advanced ones
        $this->add_standard_filters_without_location();

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Show/Hide 
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hide_show_section',
            [
                'label'     => esc_html__( 'Show/Hide Data', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->Property_Cards_Show_Hide_Traits();

        $this->add_control(
            'hide_excerpt',
            [
                'label' => esc_html__( 'Hide Excerpt', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'estatesite-houzez' ),
                'label_off' => esc_html__( 'No', 'estatesite-houzez' ),
                'return_value' => 'none',
                'default' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .item-short-description' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_button',
            [
                'label' => esc_html__( 'Hide Details Button', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'estatesite-houzez' ),
                'label_off' => esc_html__( 'No', 'estatesite-houzez' ),
                'return_value' => 'none',
                'default' => '',
            ]
        );

        $this->add_control(
            'hide_author_date',
            [
                'label' => esc_html__( 'Hide Date & Agent', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'estatesite-houzez' ),
                'label_off' => esc_html__( 'No', 'estatesite-houzez' ),
                'return_value' => 'none',
                'default' => '',
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Typography
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'typography_section',
            [
                'label'     => esc_html__( 'Typography', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_property_title',
                'label'    => esc_html__( 'Property Title', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .item-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_prop_address',
                'label'    => esc_html__( 'Address', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} address.item-address',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_prop_excerpt',
                'label'    => esc_html__( 'Excerpt', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .item-short-description',
                'condition' => [
                    'hide_excerpt' => ''
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_meta_labels',
                'label'    => esc_html__( 'Meta Labels', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .item-amenities-text',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_meta_figure',
                'label'    => esc_html__( 'Meta Figure', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .hz-figure',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_price',
                'label'    => esc_html__( 'Price', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .item-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_subprice',
                'label'    => esc_html__( 'Sub Price', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .item-sub-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_types',
                'label'    => esc_html__( 'Property Type', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .h-type span',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_area-postfix',
                'label'    => esc_html__( 'Area Postfix', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .area_postfix',
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Colors
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_grid_colors',
            [
                'label' => esc_html__( 'Colors', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->Property_Cards_Colors_Traits();

        $this->add_control(
            'excerpt_color',
            [
                'label'     => esc_html__( 'Excerpt Color', 'estatesite-houzez' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-short-description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'hide_excerpt' => ''
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label'     => esc_html__( 'Agent & Date', 'estatesite-houzez' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-author a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-author' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-date' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Image 
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_grid_images_styles',
            [
                'label' => esc_html__( 'Image Radius', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->Property_Cards_Image_Traits();

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Button & Pagination
        * -------------------------------------------------------------------------------*/
        $this->Property_Cards_btn_Traits();
        $this->Property_Cards_Pagination_Traits();
    }

    /**
     * Add standard filters without location fields (since we have advanced location filters)
     */
    protected function add_standard_filters_without_location() {
        $page_filters = houzez_option('houzez_page_filters');
        $hide_filters = !empty($page_filters) ? $page_filters : array();

        $listing_types = array();
        $listing_status = array();
        $listing_labels = array();
        
        houzez_get_terms_array( 'property_status', $listing_status );
        houzez_get_terms_array( 'property_type', $listing_types );
        houzez_get_terms_array( 'property_label', $listing_labels );

        if( isset($hide_filters) && ! in_array('property_type', $hide_filters) ) {
            $this->add_control(
                'property_type',
                [
                    'label'    => esc_html__('Type', 'estatesite-houzez'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_types,
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_status', $hide_filters) ) {
            $this->add_control(
                'property_status',
                [
                    'label'    => esc_html__('Status', 'estatesite-houzez'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_status,
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_label', $hide_filters) ) {
            $this->add_control(
                'property_label',
                [
                    'label'    => esc_html__('Labels', 'estatesite-houzez'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_labels,
                ]
            );
        }

        // Add agents and agencies filters
        $this->add_control(
            'properties_by_agents',
            [
                'label'         => esc_html__('Properties by Agents', 'estatesite-houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_agents',
                'render_result' => 'houzez_render_agents',
            ]
        );

        $this->add_control(
            'properties_by_agencies',
            [
                'label'         => esc_html__('Properties by Agencies', 'estatesite-houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_agencies',
                'render_result' => 'houzez_render_agencies',
            ]
        );

        // Add price filters
        $this->add_control(
            'min_price',
            [
                'label' => esc_html__( 'Minimum Price', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1000,
            ]
        );

        $this->add_control(
            'max_price',
            [
                'label' => esc_html__( 'Maximum Price', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1000,
            ]
        );

        // Add beds/baths filters
        $this->add_control(
            'min_beds',
            [
                'label' => esc_html__( 'Minimum Beds', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 20,
            ]
        );

        $this->add_control(
            'max_beds',
            [
                'label' => esc_html__( 'Maximum Beds', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 20,
            ]
        );

        $this->add_control(
            'min_baths',
            [
                'label' => esc_html__( 'Minimum Baths', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 20,
            ]
        );

        $this->add_control(
            'max_baths',
            [
                'label' => esc_html__( 'Maximum Baths', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 20,
            ]
        );

        // Add property IDs filter
        $this->add_control(
            'property_ids',
            [
                'label' => esc_html__( 'Properties IDs', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXTAREA,
                'description' => esc_html__( 'Enter properties ids comma separated. Ex 12,34,56', 'estatesite-houzez' ),
            ]
        );
    }

    /**
     * Build advanced query arguments
     */
    protected function build_advanced_query_args($settings) {
        $args = $this->listings_cards_args($settings);
        
        // Clear standard location filters since we're using advanced ones
        $args['property_country'] = '';
        $args['property_state'] = '';
        $args['property_city'] = '';
        $args['property_area'] = '';
        
        // Add advanced location filtering
        if (!empty($settings['location_groups'])) {
            $args['advanced_location_groups'] = $settings['location_groups'];
        }
        
        return $args;
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Build advanced query arguments
        $args = $this->build_advanced_query_args($settings);
        
        // Use custom rendering function that handles advanced location filtering
        echo $this->render_advanced_property_cards($args, $settings['module_type']);
    }

    /**
     * Custom rendering function with advanced location filtering
     */
    protected function render_advanced_property_cards($attributes, $module_type = 'grid_3_cols') {
        // Start output buffering
        ob_start();
        
        // Set up global variables
        global $paged, $ele_thumbnail_size, $hide_button, $hide_author_date, $hide_author, $hide_date;
        $ele_thumbnail_size = isset($attributes['thumb_size']) ? $attributes['thumb_size'] : '';
        $hide_button = isset($attributes['hide_button']) ? $attributes['hide_button'] : '';
        $hide_author_date = isset($attributes['hide_author_date']) ? $attributes['hide_author_date'] : '';
        $hide_author = isset($attributes['hide_author']) ? $attributes['hide_author'] : '';
        $hide_date = isset($attributes['hide_date']) ? $attributes['hide_date'] : '';
        
        // Handle pagination for front page
        if (is_front_page()) {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        }

        // Process any tab parameters from URL
        $attributes = houzez_process_tab_parameters($attributes);
        
        // Get layout classes based on module type
        list($cols_class, $item_view, $wrapper_class) = houzez_get_layout_classes($module_type, 'v1');

        // Get the query with advanced location filtering
        $the_query = $this->get_advanced_wp_query($attributes, $paged);
        
        // Render the property cards
        houzez_render_property_cards($the_query, $cols_class, $item_view, $attributes, $wrapper_class, 'v1');
        
        // Get the buffered content and return it
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    /**
     * Get WP_Query with advanced location filtering
     */
    protected function get_advanced_wp_query($attributes, $paged = 1) {
        // Start with standard query args
        $query_args = \Houzez_Data_Source::shortcode_to_args($attributes, $paged);
        
        // Handle advanced location groups if present
        if (!empty($attributes['advanced_location_groups'])) {
            $location_tax_query = $this->build_advanced_location_tax_query($attributes['advanced_location_groups']);
            
            if (!empty($location_tax_query)) {
                // Merge with existing tax_query if present
                if (isset($query_args['tax_query'])) {
                    $query_args['tax_query'] = array_merge($query_args['tax_query'], $location_tax_query);
                } else {
                    $query_args['tax_query'] = $location_tax_query;
                }
                
                // Ensure we use OR relation between location groups
                if (count($location_tax_query) > 1) {
                    $query_args['tax_query']['relation'] = 'OR';
                }
            }
        }
        
        return new \WP_Query($query_args);
    }

    /**
     * Build advanced location taxonomy query
     */
    protected function build_advanced_location_tax_query($location_groups) {
        $location_tax_query = array();
        
        foreach ($location_groups as $group) {
            $group_tax_query = array();
            $group_tax_query['relation'] = 'AND'; // Within a group, all conditions must match
            
            // Add country filter
            if (!empty($group['country'])) {
                $countries = is_array($group['country']) ? $group['country'] : explode(',', $group['country']);
                $group_tax_query[] = array(
                    'taxonomy' => 'property_country',
                    'field'    => 'slug',
                    'terms'    => $countries,
                    'operator' => 'IN'
                );
            }
            
            // Add state filter
            if (!empty($group['state'])) {
                $states = is_array($group['state']) ? $group['state'] : explode(',', $group['state']);
                $group_tax_query[] = array(
                    'taxonomy' => 'property_state',
                    'field'    => 'slug',
                    'terms'    => $states,
                    'operator' => 'IN'
                );
            }
            
            // Add city filter
            if (!empty($group['city'])) {
                $cities = is_array($group['city']) ? $group['city'] : explode(',', $group['city']);
                $group_tax_query[] = array(
                    'taxonomy' => 'property_city',
                    'field'    => 'slug',
                    'terms'    => $cities,
                    'operator' => 'IN'
                );
            }
            
            // Add area filter
            if (!empty($group['area'])) {
                $areas = is_array($group['area']) ? $group['area'] : explode(',', $group['area']);
                $group_tax_query[] = array(
                    'taxonomy' => 'property_area',
                    'field'    => 'slug',
                    'terms'    => $areas,
                    'operator' => 'IN'
                );
            }
            
            // Only add this group if it has at least one location filter
            if (count($group_tax_query) > 1) { // > 1 because we always have 'relation'
                $location_tax_query[] = $group_tax_query;
            }
        }
        
        return $location_tax_query;
    }
}

// Register the widget
Plugin::instance()->widgets_manager->register( new EstateSite_Property_Cards_Advanced() );