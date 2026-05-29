<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ESTE_Property_Card_V1 extends Widget_Base {

    public function get_name() {
        return 'este_property_card_v1';
    }

    public function get_title() {
        return esc_html__( 'ESTE Property Card V1', 'estatesite-houzez' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'estatesite-elements' ];
    }

    protected function register_controls() {
        
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'module_type',
            [
                'label'     => esc_html__( 'Layout', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid_3_cols' => esc_html__( 'Grid View 3 Columns', 'estatesite-houzez'),
                    'grid_4_cols' => esc_html__( 'Grid View 4 Columns', 'estatesite-houzez'),
                    'grid_2_cols' => esc_html__( 'Grid View 2 Columns', 'estatesite-houzez'),
                    'list'        => esc_html__( 'List View', 'estatesite-houzez')
                ],
                'default' => 'grid_3_cols',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'exclude' => [ 'custom' ],
                'include' => [],
                'default' => 'large',
            ]
        );

        $this->add_control(
            'property_type',
            [
                'label'    => esc_html__( 'Property Type', 'estatesite-houzez' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => $this->get_property_types(),
            ]
        );

        $this->add_control(
            'property_status',
            [
                'label'    => esc_html__( 'Property Status', 'estatesite-houzez' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => $this->get_property_statuses(),
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label'   => esc_html__( 'Number of Properties', 'estatesite-houzez' ),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 50,
                'step'    => 1,
                'default' => 9,
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'   => esc_html__( 'Offset', 'estatesite-houzez' ),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 0,
                'max'     => 50,
                'step'    => 1,
                'default' => 0,
            ]
        );

        $this->add_control(
            'sort_by',
            [
                'label'   => esc_html__( 'Sort By', 'estatesite-houzez' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'date_desc'  => esc_html__( 'Date Desc', 'estatesite-houzez' ),
                    'date_asc'   => esc_html__( 'Date Asc', 'estatesite-houzez' ),
                    'price_desc' => esc_html__( 'Price Desc', 'estatesite-houzez' ),
                    'price_asc'  => esc_html__( 'Price Asc', 'estatesite-houzez' ),
                ],
                'default' => 'date_desc',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label'   => esc_html__( 'Pagination', 'estatesite-houzez' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'none'     => esc_html__( 'None', 'estatesite-houzez' ),
                    'number'   => esc_html__( 'Number', 'estatesite-houzez' ),
                    'loadmore' => esc_html__( 'Load More', 'estatesite-houzez' ),
                ],
                'default' => 'none',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__( 'Style', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label'     => esc_html__( 'Card Background', 'estatesite-houzez' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => esc_html__( 'Border', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .property-item',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'estatesite-houzez' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .property-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .property-item',
            ]
        );

        $this->add_control(
            'card_padding',
            [
                'label' => esc_html__( 'Padding', 'estatesite-houzez' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .property-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Title Typography', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .property-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__( 'Title Color', 'estatesite-houzez' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type'      => 'property',
            'posts_per_page' => $settings['posts_limit'],
            'offset'         => $settings['offset'],
            'paged'          => ( get_query_var('paged') ) ? get_query_var('paged') : 1,
        ];

        if ( ! empty( $settings['property_type'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'property_type',
                'field'    => 'term_id',
                'terms'    => $settings['property_type'],
            ];
        }

        if ( ! empty( $settings['property_status'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'property_status',
                'field'    => 'term_id',
                'terms'    => $settings['property_status'],
            ];
        }

        switch ( $settings['sort_by'] ) {
            case 'price_asc':
                $args['meta_key'] = 'fave_property_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price_desc':
                $args['meta_key'] = 'fave_property_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'date_asc':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
            case 'date_desc':
            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
        }

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) :
            $column_class = $this->get_column_class($settings['module_type']);
            echo '<div class="este-property-card-v1 ' . esc_attr($settings['module_type']) . '">';
            echo '<div class="row">';
            while ( $query->have_posts() ) : $query->the_post();
                echo '<div class="' . esc_attr($column_class) . '">';
                $this->render_property_card( get_the_ID(), $settings );
                echo '</div>';
            endwhile;
            echo '</div>'; // Close .row
            echo '</div>'; // Close .este-property-card-v1

            if ( $settings['pagination_type'] !== 'none' ) {
                $this->render_pagination( $query, $settings );
            }
        endif;

        wp_reset_postdata();
    }

    protected function render_property_card( $post_id, $settings ) {
        $thumbnail = get_the_post_thumbnail_url( $post_id, $settings['thumbnail_size'] );
        $title = get_the_title( $post_id );
        $permalink = get_permalink( $post_id );
        $price = get_post_meta( $post_id, 'fave_property_price', true );
        $address = get_post_meta( $post_id, 'fave_property_address', true );

        echo '<div class="property-item">';
        if ( $thumbnail ) {
            echo '<div class="property-thumbnail"><img src="' . esc_url( $thumbnail ) . '" alt="' . esc_attr( $title ) . '"></div>';
        }
        echo '<h3 class="property-title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h3>';
        if ( $price ) {
            echo '<div class="property-price">' . esc_html( $price ) . '</div>';
        }
        if ( $address ) {
            echo '<div class="property-address">' . esc_html( $address ) . '</div>';
        }
        echo '</div>';
    }

    protected function render_pagination( $query, $settings ) {
        if ( $settings['pagination_type'] === 'number' ) {
            echo '<div class="este-pagination">';
            echo paginate_links( array(
                'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'total'        => $query->max_num_pages,
                'current'      => max( 1, get_query_var( 'paged' ) ),
                'format'       => '?paged=%#%',
                'show_all'     => false,
                'type'         => 'plain',
                'end_size'     => 2,
                'mid_size'     => 1,
                'prev_next'    => true,
                'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
                'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
                'add_args'     => false,
                'add_fragment' => '',
            ) );
            echo '</div>';
        } elseif ( $settings['pagination_type'] === 'loadmore' ) {
            echo '<div class="este-load-more-wrapper">';
            echo '<button class="este-load-more" data-page="1" data-max="' . $query->max_num_pages . '">' . esc_html__( 'Load More', 'estatesite-houzez' ) . '</button>';
            echo '</div>';
        }
    }

    private function get_column_class( $module_type ) {
        switch ( $module_type ) {
            case 'grid_2_cols':
                return 'col-md-6';
            case 'grid_3_cols':
                return 'col-md-4';
            case 'grid_4_cols':
                return 'col-md-3';
            case 'list':
                return 'col-md-12';
            default:
                return 'col-md-4';
        }
    }

    private function get_property_types() {
        $types = get_terms( [
            'taxonomy'   => 'property_type',
            'hide_empty' => false,
        ] );

        $options = [];
        foreach ( $types as $type ) {
            $options[ $type->term_id ] = $type->name;
        }

        return $options;
    }

    private function get_property_statuses() {
        $statuses = get_terms( [
            'taxonomy'   => 'property_status',
            'hide_empty' => false,
        ] );

        $options = [];
        foreach ( $statuses as $status ) {
            $options[ $status->term_id ] = $status->name;
        }

        return $options;
    }
}