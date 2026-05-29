<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor ESTE Agent Listings V1 Widget.
 */
class ESTE_Agent_Listings_V1 extends Widget_Base {

    public function get_name() {
        return 'este_agent_listings_v1';
    }

    public function get_title() {
        return __( 'ESTE Agent Listings V1', 'estatesite-houzez' );
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'estatesite-houzez' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'module_type',
            [
                'label' => __( 'Layout', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'grid_2_cols'  => __( 'Grid View 2 Columns', 'estatesite-houzez' ),
                    'grid_3_cols'  => __( 'Grid View 3 Columns', 'estatesite-houzez' ),
                    'grid_4_cols'  => __( 'Grid View 4 Columns', 'estatesite-houzez' ),
                    'list'  => __( 'List View', 'estatesite-houzez' ),
                ],
                'default' => 'grid_3_cols',
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label' => __( 'Number of properties', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 500,
                'step' => 1,
                'default' => 9,
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __( 'Pagination', 'estatesite-houzez' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'loadmore' => __( 'Load More', 'estatesite-houzez' ),
                    'pagination' => __( 'Pagination', 'estatesite-houzez' ),
                    'none' => __( 'None', 'estatesite-houzez' ),
                ],
                'default' => 'loadmore',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $post;

        // Correct class and path for Houzez_Query class
        if ( ! class_exists( '\Houzez_Query' ) ) {
            include_once get_template_directory() . '/inc/class-houzez-query.php';
        }

        // Assume these variables are retrieved or set appropriately
        $settings = $this->get_settings_for_display();

        $args = [
            'posts_per_page' => $settings['posts_limit'],
            'pagination' => $settings['pagination_type']
        ];

        // Use the query that gets agent properties
        $the_query = \Houzez_Query::loop_agent_properties($args);

        // Adjust layout classes based on module type
        $wrap_class = 'listing-v1';
        $view_class = 'grid-view';
        $item_layout = 'v1';
        $card_deck = 'card-deck';

        switch ($settings['module_type']) {
            case 'grid_2_cols':
                $view_class = 'grid-view grid_2_cols';
                break;
            case 'grid_3_cols':
                $view_class = 'grid-view grid_3_cols';
                break;
            case 'grid_4_cols':
                $view_class = 'grid-view grid_4_cols';
                break;
            case 'list':
                $view_class = 'list-view';
                break;
        }

        ?>
        <section class="listing-wrap <?php echo esc_attr($wrap_class); ?>">
            <div class="listing-view <?php echo esc_attr($view_class) . ' ' . esc_attr($card_deck); ?>">
                <?php
                if ( $the_query->have_posts() ) :
                    while ( $the_query->have_posts() ) : $the_query->the_post();
                        get_template_part('template-parts/listing/item', $item_layout);
                    endwhile;
                    wp_reset_postdata();
                else:
                    get_template_part('template-parts/listing/item', 'none');
                endif;
                ?>
            </div><!-- listing-view -->
            <?php
            if ($settings['pagination_type'] == 'pagination') {
                houzez_pagination($the_query->max_num_pages);
            } elseif ($settings['pagination_type'] == 'loadmore') {
                echo '<button class="btn-load-more">' . __('Load More', 'estatesite-houzez') . '</button>';
            }
            ?>
        </section><!-- listing-wrap -->
        <?php
    }

    protected function _content_template() {
        ?>
        <#
        var wrapClass = 'listing-v1';
        var viewClass = 'grid-view';
        var cardDeck = 'card-deck';

        switch ( settings.module_type ) {
            case 'grid_2_cols':
                viewClass = 'grid-view grid_2_cols';
                break;
            case 'grid_3_cols':
                viewClass = 'grid-view grid_3_cols';
                break;
            case 'grid_4_cols':
                viewClass = 'grid-view grid_4_cols';
                break;
            case 'list':
                viewClass = 'list-view';
                break;
        }

        #>
        <section class="listing-wrap {{ wrapClass }}">
            <div class="listing-view {{ viewClass }} {{ cardDeck }}">
                <# // Dynamic content rendering is handled by PHP #>
            </div>
        </section>
        <?php
    }
}

Plugin::instance()->widgets_manager->register( new ESTE_Agent_Listings_V1 );