<?php
/**
 * Property Types Widget
 * Displays property types filtered by property status
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Estatesite_Property_Types extends Widget_Base {

    public function get_name() {
        return 'estatesite-property-types';
    }

    public function get_title() {
        return __( 'ES Property Types List', 'estatesite-houzez' );
    }

    public function get_icon() {
        return 'estatesite-element-icon eicon-bullet-list';
    }

    public function get_categories() {
        return [ 'estatesite-elements' ];
    }

    public function get_badge() {
        return 'Estate Site';
    }

    protected function register_controls() {
        
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'list_title',
            [
                'label' => __( 'List Title', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Property Types', 'estatesite-houzez' ),
            ]
        );

        $this->add_control(
            'property_status',
            [
                'label' => __( 'Choose Property Status', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_property_status_options(),
                'multiple' => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'property_type',
            [
                'label' => __( 'Choose Property Types', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_property_type_options(),
                'multiple' => true,
                'label_block' => true,
                'description' => __('Leave empty to display all types', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_child',
            [
                'label' => __( 'Show Child', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'no',
                'options' => [
                    'yes' => __( 'Yes', 'estatesite-houzez' ),
                    'no' => __( 'No', 'estatesite-houzez' ),
                ],
            ]
        );

        $this->add_control(
            'hide_empty',
            [
                'label' => __( 'Hide Empty', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => __( 'Yes', 'estatesite-houzez' ),
                    'no' => __( 'No', 'estatesite-houzez' ),
                ],
            ]
        );

        $this->add_control(
            'hide_count',
            [
                'label' => __( 'Hide Count', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'no',
                'options' => [
                    'yes' => __( 'Yes', 'estatesite-houzez' ),
                    'no' => __( 'No', 'estatesite-houzez' ),
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __( 'Order By', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'name',
                'options' => [
                    'name' => __( 'Name', 'estatesite-houzez' ),
                    'count' => __( 'Count', 'estatesite-houzez' ),
                    'id' => __( 'ID', 'estatesite-houzez' ),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __( 'Order', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => [
                    'ASC' => __( 'Ascending', 'estatesite-houzez' ),
                    'DESC' => __( 'Descending', 'estatesite-houzez' ),
                ],
            ]
        );

        $this->add_control(
            'num_items',
            [
                'label' => __( 'Number of Items to Show', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'description' => __('0 for all', 'estatesite-houzez'),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Style', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __( 'Title Typography', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .taxonomy-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'list_typography',
                'label' => __( 'List Typography', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .taxonomy-list li a',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'list_color',
            [
                'label' => __( 'List Item Color', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => __( 'Count Color', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li .count' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'list_style_section',
            [
                'label' => __( 'List', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
                'label' => __( 'Space Between', 'estatesite-houzez' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_alignment',
            [
                'label' => __( 'Alignment', 'estatesite-houzez' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'estatesite-houzez' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'estatesite-houzez' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'estatesite-houzez' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'show_divider',
            [
                'label' => __( 'Divider', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'estatesite-houzez' ),
                'label_off' => __( 'Off', 'estatesite-houzez' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'divider_style',
            [
                'label' => __( 'Style', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'solid' => __( 'Solid', 'estatesite-houzez' ),
                    'dashed' => __( 'Dashed', 'estatesite-houzez' ),
                    'dotted' => __( 'Dotted', 'estatesite-houzez' ),
                ],
                'default' => 'solid',
                'condition' => [
                    'show_divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li:not(:last-child)' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'divider_color',
            [
                'label' => __( 'Color', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#e5e5e5',
                'condition' => [
                    'show_divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_weight',
            [
                'label' => __( 'Weight', 'estatesite-houzez' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'condition' => [
                    'show_divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_spacing',
            [
                'label' => __( 'Spacing', 'estatesite-houzez' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'condition' => [
                    'show_divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-list li:not(:last-child)' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_property_status_options() {
        $options = [];
        $terms = get_terms([
            'taxonomy' => 'property_status',
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }
        return $options;
    }

    private function get_property_type_options() {
        $options = [];
        $terms = get_terms([
            'taxonomy' => 'property_type',
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }
        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $property_status = $settings['property_status'];
        $property_types = $settings['property_type'];
        $show_child = $settings['show_child'];
        $hide_empty = $settings['hide_empty'] === 'yes' ? true : false;
        $hide_count = $settings['hide_count'];
        $orderby = $settings['orderby'];
        $order = $settings['order'];
        $num_items = $settings['num_items'];

        $list_title = $settings['list_title'];

        // Get property types
        $args = [
            'taxonomy' => 'property_type',
            'hide_empty' => $hide_empty,
            'orderby' => $orderby,
            'order' => $order,
            'number' => $num_items > 0 ? $num_items : '',
            'parent' => $show_child === 'yes' ? '' : 0,
        ];

        // Filter by specific property types if selected
        if (!empty($property_types)) {
            $args['include'] = $property_types;
        }

        $property_types_terms = get_terms($args);

        if (!empty($property_types_terms) && !is_wp_error($property_types_terms)) {
            echo '<div class="taxonomy-wrap">';
            
            if (!empty($list_title)) {
                echo '<h3 class="taxonomy-title">' . esc_html($list_title) . '</h3>';
            }

            // Add class for divider if enabled
            $list_class = 'taxonomy-list';
            if ($settings['show_divider'] === 'yes') {
                $list_class .= ' taxonomy-list-divider';
            }

            echo '<ul class="' . esc_attr($list_class) . '">';

            foreach ($property_types_terms as $term) {
                // If property status filter is active, check if this type has properties with that status
                $show_term = true;
                
                if (!empty($property_status)) {
                    $show_term = false;
                    
                    // Query properties with this type and the selected status
                    $property_args = [
                        'post_type' => 'property',
                        'post_status' => 'publish',
                        'posts_per_page' => 1, // We just need to know if any exist
                        'tax_query' => [
                            'relation' => 'AND',
                            [
                                'taxonomy' => 'property_type',
                                'field' => 'term_id',
                                'terms' => $term->term_id,
                            ],
                            [
                                'taxonomy' => 'property_status',
                                'field' => 'term_id',
                                'terms' => $property_status,
                            ],
                        ],
                    ];
                    
                    $property_query = new \WP_Query($property_args);
                    if ($property_query->have_posts()) {
                        $show_term = true;
                    }
                }
                
                if ($show_term) {
                    $term_link = get_term_link($term);
                    
                    // Get count of properties with this type and status
                    $count = $term->count;
                    if (!empty($property_status)) {
                        $count_args = [
                            'post_type' => 'property',
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                            'tax_query' => [
                                'relation' => 'AND',
                                [
                                    'taxonomy' => 'property_type',
                                    'field' => 'term_id',
                                    'terms' => $term->term_id,
                                ],
                                [
                                    'taxonomy' => 'property_status',
                                    'field' => 'term_id',
                                    'terms' => $property_status,
                                ],
                            ],
                        ];
                        
                        $count_query = new \WP_Query($count_args);
                        $count = $count_query->found_posts;
                        
                        // Add property status as parameters to the term link
                        if (!is_wp_error($term_link)) {
                            // For single status
                            if (count($property_status) === 1) {
                                $status_term = get_term($property_status[0], 'property_status');
                                if ($status_term && !is_wp_error($status_term)) {
                                    $term_link = add_query_arg('property_status', $status_term->slug, $term_link);
                                }
                            } 
                            // For multiple statuses
                            else {
                                $status_slugs = [];
                                foreach ($property_status as $status_id) {
                                    $status_term = get_term($status_id, 'property_status');
                                    if ($status_term && !is_wp_error($status_term)) {
                                        $status_slugs[] = $status_term->slug;
                                    }
                                }
                                if (!empty($status_slugs)) {
                                    $term_link = add_query_arg('property_status', implode(',', $status_slugs), $term_link);
                                }
                            }
                        }
                    }
                    
                    echo '<li style="list-style-type:none;">';
                    echo '<a href="' . esc_url($term_link) . '">' . esc_html($term->name);
                    
                    if ($hide_count !== 'yes') {
                        echo '<span class="count">(' . esc_html($count) . ')</span>';
                    }
                    
                    echo '</a>';
                    echo '</li>';

                }
            }

            echo '</ul>';
            echo '</div>';
        }
    }

    protected function content_template() {}
}

Plugin::instance()->widgets_manager->register_widget_type( new Estatesite_Property_Types() ); 