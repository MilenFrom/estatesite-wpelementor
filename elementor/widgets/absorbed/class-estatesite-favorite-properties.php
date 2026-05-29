<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * EstateSite Favorite Properties Widget
 * Based on Houzez Property by IDs but shows user favorites
 */
class EstateSite_Elementor_Favorite_Properties extends Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'estatesite_favorite_properties';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__( 'Favorite Properties', 'estatesite-houzez' );
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'estatesite-element-icon eicon-heart';
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
                'label'     => esc_html__( 'Content', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'display_format',
            [
                'label'     => esc_html__( 'Display Format', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'cards'  => esc_html__( 'Property Cards', 'estatesite-houzez'),
                    'table'    => esc_html__( 'Table (Original Houzez)', 'estatesite-houzez')
                ],
                'default' => 'cards',
                'description' => esc_html__('Table format uses original Houzez favorites display with immediate remove functionality', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'prop_grid_style',
            [
                'label'     => esc_html__( 'Grid Style', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'v_1'  => esc_html__( 'Property Card v1', 'estatesite-houzez'),
                    'v_2'    => esc_html__( 'Property Card v2', 'estatesite-houzez'),
                    'v_3'    => esc_html__( 'Property Card v3', 'estatesite-houzez'),
                    'v_5'    => esc_html__( 'Property Card v5', 'estatesite-houzez'),
                    'v_6'    => esc_html__( 'Property Card v6', 'estatesite-houzez'),
                    'v_7'    => esc_html__( 'Property Card v7', 'estatesite-houzez'),
                    'v_8'    => esc_html__( 'Property Card v8', 'estatesite-houzez'),
                ],
                'description' => esc_html__('Choose grid style, default will be property card v1', 'estatesite-houzez'),
                'default' => 'v_1',
                'condition' => [
                    'display_format' => 'cards'
                ]
            ]
        );

        $this->add_control(
            'columns',
            [
                'label'     => esc_html__( 'Columns', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '3cols'  => esc_html__( '3 Columns', 'estatesite-houzez'),
                    '2cols'    => esc_html__( '2 Columns', 'estatesite-houzez'),
                    '4cols'    => esc_html__( '4 Columns', 'estatesite-houzez')
                ],
                'default' => '3cols',
                'condition' => [
                    'display_format' => 'cards',
                    'prop_grid_style!' => 'v_8'
                ]
            ]
        );

        $this->add_control(
            'show_remove_button',
            [
                'label' => esc_html__( 'Show Remove Button', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'estatesite-houzez' ),
                'label_off' => esc_html__( 'Hide', 'estatesite-houzez' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'no_favorites_message',
            [
                'label' => esc_html__( 'No Favorites Message', 'estatesite-houzez' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'You haven\'t added any properties to your favorites yet.', 'estatesite-houzez' ),
                'placeholder' => esc_html__( 'Enter message to show when no favorites found', 'estatesite-houzez' ),
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label' => esc_html__( 'Number of Properties', 'estatesite-houzez' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 12,
            ]
        );
        
        $this->end_controls_section();

        // Style section for remove button
        $this->start_controls_section(
            'remove_button_style',
            [
                'label' => esc_html__( 'Remove Button', 'estatesite-houzez' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_remove_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'remove_button_color',
            [
                'label' => esc_html__( 'Button Color', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .btn-remove-favorite' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'remove_button_hover_color',
            [
                'label' => esc_html__( 'Button Hover Color', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .btn-remove-favorite:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'remove_button_bg_color',
            [
                'label' => esc_html__( 'Button Background', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(255, 255, 255, 0.9)',
                'selectors' => [
                    '{{WRAPPER}} .btn-remove-favorite' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'remove_button_bg_hover_color',
            [
                'label' => esc_html__( 'Button Background Hover', 'estatesite-houzez' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .btn-remove-favorite:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Get user favorites
        $user_id = get_current_user_id();
        $fav_ids = array();
        
        if (is_user_logged_in() && $user_id) {
            $fav_ids = get_user_meta($user_id, 'houzez_favorites', true);
            if (empty($fav_ids) || !is_array($fav_ids)) {
                $fav_ids = array();
            }
        } else {
            // For non-logged-in users, use cookie
            $fav_ids = isset($_COOKIE['houzez_favorite_listings']) ? explode(',', $_COOKIE['houzez_favorite_listings']) : array();
            if (empty($fav_ids[0])) {
                $fav_ids = array();
            }
        }
        
        // Sanitize favorite IDs
        $fav_ids = array_map('absint', $fav_ids);
        $fav_ids = array_filter($fav_ids);
        
        if (empty($fav_ids)) {
            // Show no favorites message
            echo '<div class="no-favorites-message text-center py-4">';
            echo '<p>' . esc_html($settings['no_favorites_message']) . '</p>';
            echo '</div>';
            return;
        }
        
        // Limit the number of properties
        if ($settings['posts_limit'] > 0) {
            $fav_ids = array_slice($fav_ids, 0, $settings['posts_limit']);
        }
        
        // Add a unique wrapper ID for this widget instance
        $widget_id = 'estatesite-favorites-' . $this->get_id();
        echo '<div id="' . esc_attr($widget_id) . '" class="estatesite-favorites-widget">';
        
        // Check display format
        if ($settings['display_format'] === 'table') {
            // Use original Houzez table format
            $this->render_table_format($fav_ids, $settings);
        } else {
            // Use property cards format
            $this->render_cards_format($fav_ids, $settings);
        }
        
        echo '</div>'; // Close widget wrapper
        
        // Add custom JavaScript to handle immediate removal from favorites listing
        if ($settings['show_remove_button'] === 'yes' && $settings['display_format'] === 'cards') {
            $this->add_immediate_removal_functionality($widget_id);
        } elseif ($settings['display_format'] === 'table') {
            $this->add_table_removal_functionality($widget_id);
        }
    }
    
    /**
     * Render cards format
     */
    private function render_cards_format($fav_ids, $settings) {
        // Prepare arguments exactly like the Houzez Property by IDs widget
        $args = array();
        $args['prop_grid_style'] = $settings['prop_grid_style'];
        $args['property_ids'] = implode(',', $fav_ids);
        $args['columns'] = $settings['columns'];
        
        $module_type = $settings['columns'];
        $card_version = $settings['prop_grid_style'];
        
        // Try multiple Houzez functions in order of preference
        if (function_exists('houzez_get_property_cards')) {
            echo houzez_get_property_cards($args, $module_type, $card_version);
        } elseif (function_exists('houzez_property_card_v1')) {
            echo houzez_property_card_v1($args);
        } elseif (function_exists('houzez_property_card')) {
            echo houzez_property_card($args);
        } else {
            // Fallback: render properties manually
            $this->render_properties_manually($fav_ids, $settings);
        }
    }
    
    /**
     * Render table format with inline template (no dropdown) - WITHOUT DATE COLUMN
     */
    private function render_table_format($fav_ids, $settings) {
        $args = array(
            'post_type' => 'property',
            'post__in' => $fav_ids,
            'posts_per_page' => $settings['posts_limit'],
            'post_status' => 'publish'
        );
        
        $favorite_properties_query = new \WP_Query($args);
        
        if ($favorite_properties_query->have_posts()) { ?>
            <div class="houzez-data-content mt-4"> 
                <div class="houzez-data-table">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0 estatesite-responsive-table">
                            <thead class="d-none d-lg-table-header-group">
                                <tr>
                                <th class="col-thumbnail"><?php echo esc_html__('Thumbnail', 'houzez'); ?></th>
                                <th class="col-title"><?php echo esc_html__('Title', 'houzez'); ?></th>
                                <th class="col-status d-none d-xl-table-cell"><?php echo esc_html__('Status', 'houzez'); ?></th>
                                <th class="col-id d-none d-xl-table-cell"><?php echo esc_html__('ID', 'houzez'); ?></th>
                                <th class="col-price"><?php echo esc_html__('Price', 'houzez'); ?></th>
                                <th class="col-type d-none d-xl-table-cell"><?php echo esc_html__('Type', 'houzez'); ?></th>
                                <th class="col-actions"><?php echo esc_html__('Actions', 'houzez'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while ($favorite_properties_query->have_posts()) : $favorite_properties_query->the_post();
                                
                                // Inline the favorite-item.php template with our changes
                                $this->render_inline_favorite_item();
                                
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div> 
        <?php
        } else { ?>
            <div class="stats-box">
                <?php echo esc_html($settings['no_favorites_message']); ?>
            </div>
        <?php
        }
    }

    /**
     * Render inline favorite item (based on Houzez template but with direct button) - NO DATE COLUMN - RESPONSIVE
     */
    private function render_inline_favorite_item() {
        global $post;
        $post_id = get_the_ID();
        $listings_page = get_option('fave_listings_page');
        ?>
        <!-- Desktop Table Row (Hidden on Mobile) -->
        <tr class="d-none d-lg-table-row desktop-row">
            <td class="col-thumbnail">
                <div class="image-holder">
                    <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                    <?php
                    $thumbnail_size = 'thumbnail';
                    if( has_post_thumbnail() && get_the_post_thumbnail(get_the_ID()) != '') {
                        the_post_thumbnail($thumbnail_size, array('class' => 'img-fluid rounded'));
                    } else {
                        if (function_exists('houzez_image_placeholder')) {
                            houzez_image_placeholder( $thumbnail_size );
                        } else {
                            echo '<img src="' . get_template_directory_uri() . '/img/property-placeholder.png" class="img-fluid rounded" alt="' . get_the_title() . '">';
                        }
                    }
                    ?>
                    </a>
                </div>
            </td>
            <td class="col-title">
                <div class="text-box">
                    <a class="fw-bold text-decoration-none" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php the_title(); ?></a><br>
                    <address class="mb-0 text-muted small"><?php echo function_exists('houzez_get_listing_data') ? houzez_get_listing_data('property_map_address') : get_post_meta($post_id, 'fave_property_address', true); ?></address>
                </div>
            </td>
            <td class="col-status d-none d-xl-table-cell">
                <span class="badge" style="color: var(--e-global-color-accent, var(--e-global-color-primary, #007cba)); background-color: white; border: 1px solid var(--e-global-color-accent, var(--e-global-color-primary, #007cba));">
                <?php 
                if (function_exists('houzez_taxonomy_simple')) {
                    echo houzez_taxonomy_simple('property_status');
                } else {
                    $terms = wp_get_post_terms($post_id, 'property_status', array('fields' => 'names'));
                    echo !empty($terms) ? esc_html($terms[0]) : '';
                }
                ?>
                </span>
            </td>
            <td class="col-id d-none d-xl-table-cell">
                <span class="text-muted">
                <?php 
                $property_id_display = function_exists('houzez_get_listing_data') ? houzez_get_listing_data('property_id') : $post_id;
                echo !empty($property_id_display) && $property_id_display != '0' ? $property_id_display : '-';
                ?>
                </span>
            </td>
            <td class="col-price">
                <strong style="color: var(--e-global-color-accent, var(--e-global-color-primary, #007cba));">
                <?php 
                if (function_exists('houzez_property_price_admin')) {
                    houzez_property_price_admin();
                } elseif (function_exists('houzez_get_listing_price')) {
                    echo houzez_get_listing_price();
                } else {
                    $price = get_post_meta($post_id, 'fave_property_price', true);
                    echo $price ? esc_html($price) : esc_html__('Contact for Price', 'houzez');
                }
                ?>
                </strong>
            </td>
            <td class="col-type d-none d-xl-table-cell">
                <span class="badge bg-secondary">
                <?php 
                if (function_exists('houzez_taxonomy_simple')) {
                    echo houzez_taxonomy_simple('property_type');
                } else {
                    $terms = wp_get_post_terms($post_id, 'property_type', array('fields' => 'names'));
                    echo !empty($terms) ? esc_html($terms[0]) : '';
                }
                ?>
                </span>
            </td>
            <td class="col-actions">
                <a class="btn btn-outline-danger btn-sm remove_fav" href="#" data-listid="<?php echo intval(get_the_ID()); ?>">
                    <i class="houzez-icon icon-bin me-1"></i>
                </a>
            </td>
        </tr>

        <!-- Mobile Card Row (Hidden on Desktop) -->
        <tr class="d-table-row d-lg-none mobile-card">
            <td colspan="7" class="p-0">
                <div class="mobile-property-card border rounded mb-3 p-3">
                    <div class="row g-3">
                        <!-- Property Image -->
                        <div class="col-4">
                            <div class="mobile-image-holder">
                                <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                                <?php
                                if( has_post_thumbnail() && get_the_post_thumbnail(get_the_ID()) != '') {
                                    the_post_thumbnail('medium', array('class' => 'img-fluid rounded w-100'));
                                } else {
                                    if (function_exists('houzez_image_placeholder')) {
                                        houzez_image_placeholder('medium');
                                    } else {
                                        echo '<img src="' . get_template_directory_uri() . '/img/property-placeholder.png" class="img-fluid rounded w-100" alt="' . get_the_title() . '">';
                                    }
                                }
                                ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Property Details -->
                        <div class="col-8">
                            <div class="mobile-property-info">
                                <!-- Title -->
                                <h6 class="mb-1">
                                    <a class="text-decoration-none fw-bold" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h6>
                                
                                <!-- Address -->
                                <address class="mb-2 text-muted small">
                                    <?php echo function_exists('houzez_get_listing_data') ? houzez_get_listing_data('property_map_address') : get_post_meta($post_id, 'fave_property_address', true); ?>
                                </address>
                                
                                <!-- Price -->
                                <div class="mb-2">
                                    <strong class="h6 mb-0" style="color: var(--e-global-color-accent, var(--e-global-color-primary, #007cba));">
                                    <?php 
                                    if (function_exists('houzez_property_price_admin')) {
                                        houzez_property_price_admin();
                                    } elseif (function_exists('houzez_get_listing_price')) {
                                        echo houzez_get_listing_price();
                                    } else {
                                        $price = get_post_meta($post_id, 'fave_property_price', true);
                                        echo $price ? esc_html($price) : esc_html__('Contact for Price', 'houzez');
                                    }
                                    ?>
                                    </strong>
                                </div>
                                
                                <!-- Meta Info -->
                                <div class="mobile-meta d-flex gap-2 mb-2">
                                    <!-- Status -->
                                    <span class="badge small" style="color: var(--e-global-color-accent, var(--e-global-color-primary, #007cba)); background-color: white; border: 1px solid var(--e-global-color-accent, var(--e-global-color-primary, #007cba));">
                                    <?php 
                                    if (function_exists('houzez_taxonomy_simple')) {
                                        echo houzez_taxonomy_simple('property_status');
                                    } else {
                                        $terms = wp_get_post_terms($post_id, 'property_status', array('fields' => 'names'));
                                        echo !empty($terms) ? esc_html($terms[0]) : '';
                                    }
                                    ?>
                                    </span>
                                    
                                    <!-- Type -->
                                    <span class="badge bg-secondary small">
                                    <?php 
                                    if (function_exists('houzez_taxonomy_simple')) {
                                        echo houzez_taxonomy_simple('property_type');
                                    } else {
                                        $terms = wp_get_post_terms($post_id, 'property_type', array('fields' => 'names'));
                                        echo !empty($terms) ? esc_html($terms[0]) : '';
                                    }
                                    ?>
                                    </span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="mobile-actions">
                                    <a class="btn btn-outline-danger btn-sm remove_fav" href="#" data-listid="<?php echo intval(get_the_ID()); ?>">
                                        <i class="houzez-icon icon-bin me-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }

    /**
     * Manual fallback property rendering with Houzez-style markup
     */
    private function render_properties_manually($fav_ids, $settings) {
        $args = array(
            'post_type' => 'property',
            'post__in' => $fav_ids,
            'posts_per_page' => $settings['posts_limit'],
            'post_status' => 'publish'
        );
        
        $query = new \WP_Query($args);
        
        if (!$query->have_posts()) {
            echo '<p>No properties found.</p>';
            return;
        }
        
        $columns_class = 'col-md-6 col-lg-4'; // Default 3 columns
        if ($settings['columns'] == '2cols') {
            $columns_class = 'col-md-6 col-lg-6';
        } elseif ($settings['columns'] == '4cols') {
            $columns_class = 'col-md-6 col-lg-3';
        }
        
        echo '<div class="listing-view grid-view card-deck">';
        echo '<div class="row">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $property_id = get_the_ID();
            ?>
            <div class="<?php echo esc_attr($columns_class); ?> item-listing-wrap" data-hz-id="hz-<?php echo esc_attr($property_id); ?>">
                <div class="item-wrap item-wrap-v1 item-wrap-no-frame h-100">
                    <div class="d-flex h-100 flex-column">
                        <div class="item-header">
                            <div class="labels-wrap d-flex align-items-center gap-1" role="group">
                                <!-- Add property status labels if any -->
                                <?php
                                $prop_status = get_the_terms($property_id, 'property_status');
                                if ($prop_status && !is_wp_error($prop_status)) {
                                    foreach ($prop_status as $status) {
                                        echo '<span class="label-status label label-primary">' . esc_html($status->name) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <!-- Item Tools - Using Houzez structure -->
                            <ul class="item-tools d-flex align-items-center justify-content-center">
                                <li class="item-tool item-preview">
                                    <span class="hz-show-lightbox-js" data-listid="<?php echo esc_attr($property_id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="<?php esc_attr_e('Preview', 'houzez'); ?>">
                                        <i class="houzez-icon icon-expand-3 mr-2"></i>
                                    </span>
                                </li>
                                
                                <!-- Houzez Favorite Button -->
                                <li class="item-tool item-favorite">
                                    <?php if (function_exists('houzez_add_to_favorite')) : ?>
                                        <?php echo houzez_add_to_favorite($property_id); ?>
                                    <?php else : ?>
                                        <span class="add-favorite-js item-tool-favorite text-center remove-favorite" 
                                              data-bs-toggle="tooltip" 
                                              data-bs-placement="top" 
                                              data-listid="<?php echo esc_attr($property_id); ?>" 
                                              data-bs-original-title="<?php esc_attr_e('Remove from Favorites', 'houzez'); ?>">
                                            <i class="houzez-icon icon-love-it mr-2"></i>
                                        </span>
                                    <?php endif; ?>
                                </li>
                                
                                <li class="item-tool item-compare">
                                    <span class="houzez_compare compare-<?php echo esc_attr($property_id); ?>" 
                                          data-listing_id="<?php echo esc_attr($property_id); ?>" 
                                          data-bs-toggle="tooltip" 
                                          data-bs-placement="top" 
                                          data-bs-original-title="<?php esc_attr_e('Add to Compare', 'houzez'); ?>">
                                        <i class="houzez-icon icon-move-1 mr-2"></i>
                                    </span>
                                </li>
                            </ul>
                            
                            <div class="listing-image-wrap">
                                <div class="listing-thumb">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('houzez-item-image-1', array('class' => 'img-fluid')); ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo get_template_directory_uri(); ?>/img/property-placeholder.jpg" class="img-fluid" alt="<?php the_title(); ?>">
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="item-body flex-grow-1 d-flex flex-column">
                            <h2 class="item-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <address class="item-address">
                                <?php echo get_post_meta($property_id, 'fave_property_address', true); ?>
                            </address>
                            
                            <?php
                            // Add property meta (bedrooms, bathrooms, size)
                            $bedrooms = get_post_meta($property_id, 'fave_property_bedrooms', true);
                            $bathrooms = get_post_meta($property_id, 'fave_property_bathrooms', true);
                            $size = get_post_meta($property_id, 'fave_property_size', true);
                            
                            if ($bedrooms || $bathrooms || $size) : ?>
                                <ul class="item-amenities item-amenities-with-icons">
                                    <?php if ($bedrooms) : ?>
                                        <li><i class="houzez-icon icon-hotel-double-bed-1 mr-1"></i> <span class="hz-figure"><?php echo esc_html($bedrooms); ?></span></li>
                                    <?php endif; ?>
                                    <?php if ($bathrooms) : ?>
                                        <li><i class="houzez-icon icon-bathroom-shower-1 mr-1"></i> <span class="hz-figure"><?php echo esc_html($bathrooms); ?></span></li>
                                    <?php endif; ?>
                                    <?php if ($size) : ?>
                                        <li><i class="houzez-icon icon-ruler-triangle mr-1"></i> <span class="hz-figure"><?php echo esc_html($size); ?> <?php echo get_post_meta($property_id, 'fave_property_size_prefix', true); ?></span></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <div class="item-price-wrap mt-auto">
                                <div class="item-price">
                                    <?php
                                    if (function_exists('houzez_get_listing_price')) {
                                        echo houzez_get_listing_price();
                                    } else {
                                        $price = get_post_meta($property_id, 'fave_property_price', true);
                                        $currency = houzez_option('currency_symbol');
                                        if ($price) {
                                            echo '<span class="item-price">' . $currency . number_format($price) . '</span>';
                                        } else {
                                            echo '<span class="item-price">' . esc_html__('Contact for Price', 'houzez') . '</span>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        
        echo '</div>';
        echo '</div>';
        
        wp_reset_postdata();
    }
    
    /**
     * Enhanced table functionality with responsive CSS
     */
    private function add_table_removal_functionality($widget_id) {
        ?>
        <style>
        /* Enhanced Responsive Table Styles with Elementor CSS Variables */
        #<?php echo esc_attr($widget_id); ?> .estatesite-responsive-table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        /* Elementor CSS Variables Integration */
        #<?php echo esc_attr($widget_id); ?> .elementor-accent-color {
            color: var(--e-global-color-accent, var(--e-global-color-primary, #007cba)) !important;
        }
        
        #<?php echo esc_attr($widget_id); ?> .elementor-accent-badge {
            color: var(--e-global-color-accent, var(--e-global-color-primary, #007cba)) !important;
            background-color: white !important;
            border: 1px solid var(--e-global-color-accent, var(--e-global-color-primary, #007cba)) !important;
        }
        
        /* Desktop Table Styling */
        #<?php echo esc_attr($widget_id); ?> .desktop-row td {
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem;
        }
        
        #<?php echo esc_attr($widget_id); ?> .col-thumbnail {
            width: 80px;
            min-width: 80px;
        }
        
        #<?php echo esc_attr($widget_id); ?> .col-title {
            min-width: 200px;
        }
        
        #<?php echo esc_attr($widget_id); ?> .col-status,
        #<?php echo esc_attr($widget_id); ?> .col-type {
            width: 120px;
        }
        
        #<?php echo esc_attr($widget_id); ?> .col-id {
            width: 80px;
        }
        
        #<?php echo esc_attr($widget_id); ?> .col-price {
            width: 130px;
        }
        
        #<?php echo esc_attr($widget_id); ?> .col-actions {
            width: 100px;
        }
        
        /* Image Styling */
        #<?php echo esc_attr($widget_id); ?> .image-holder img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        /* Mobile Card Styling */
        #<?php echo esc_attr($widget_id); ?> .mobile-property-card {
            background: #fff;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef !important;
        }
        
        #<?php echo esc_attr($widget_id); ?> .mobile-property-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        #<?php echo esc_attr($widget_id); ?> .mobile-image-holder img {
            aspect-ratio: 1;
            object-fit: cover;
            height: 80px;
        }
        
        #<?php echo esc_attr($widget_id); ?> .mobile-property-info h6 {
            font-size: 0.9rem;
            line-height: 1.3;
        }
        
        #<?php echo esc_attr($widget_id); ?> .mobile-meta .badge {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
        }
        
        /* Button Styling */
        #<?php echo esc_attr($widget_id); ?> .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        
        #<?php echo esc_attr($widget_id); ?> .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
            transform: translateY(-1px);
        }
        
        /* Badge Styling */
        #<?php echo esc_attr($widget_id); ?> .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 1199.98px) {
            #<?php echo esc_attr($widget_id); ?> .col-id,
            #<?php echo esc_attr($widget_id); ?> .col-status,
            #<?php echo esc_attr($widget_id); ?> .col-type {
                display: none !important;
            }
        }
        
        @media (max-width: 991.98px) {
            #<?php echo esc_attr($widget_id); ?> .desktop-row {
                display: none !important;
            }
            
            #<?php echo esc_attr($widget_id); ?> .mobile-card {
                display: table-row !important;
            }
            
            #<?php echo esc_attr($widget_id); ?> thead {
                display: none !important;
            }
        }
        
        @media (min-width: 992px) {
            #<?php echo esc_attr($widget_id); ?> .mobile-card {
                display: none !important;
            }
        }
        
        /* Loading and Transition States */
        #<?php echo esc_attr($widget_id); ?> .removing-item {
            opacity: 0.5;
            transform: scale(0.98);
            transition: all 0.3s ease;
        }
        
        /* Improved Accessibility */
        #<?php echo esc_attr($widget_id); ?> .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        /* Empty State */
        #<?php echo esc_attr($widget_id); ?> .stats-box {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }
        </style>
        <?php
    }

    /**
     * Add immediate removal functionality for visual feedback - Cards format
     */
    private function add_immediate_removal_functionality($widget_id) {
        ?>
        <style>
        .estatesite-removing {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }
        
        .estatesite-success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            var widgetId = '<?php echo esc_js($widget_id); ?>';
            
            // Listen for Houzez favorite button clicks within our widget
            $('#' + widgetId).on('click', '.item-tool-favorite', function(e) {
                var $button = $(this);
                var $card = $button.closest('.item-listing-wrap, .property-item, .item-wrap');
                var propertyId = $button.data('listid') || $button.closest('[data-hz-id]').data('hz-id');
                
                // Clean property ID
                if (propertyId && typeof propertyId === 'string') {
                    propertyId = propertyId.replace('hz-', '');
                }
                
                // Check if this is a removal (button should have 'remove-favorite' class for favorites)
                if ($button.hasClass('remove-favorite') || $button.hasClass('added-favorite')) {
                    // Add visual feedback immediately
                    $card.addClass('estatesite-removing');
                    
                    // Wait for Houzez AJAX to complete, then remove the item
                    setTimeout(function() {
                        // Check if the favorite was actually removed by checking button state
                        if (!$button.hasClass('remove-favorite') && !$button.hasClass('added-favorite')) {
                            // Item was removed from favorites, fade it out
                            $card.fadeOut(400, function() {
                                $(this).remove();
                                
                                // Check if no more properties
                                if ($('#' + widgetId + ' .item-listing-wrap, #' + widgetId + ' .property-item, #' + widgetId + ' .item-wrap').length === 0) {
                                    $('#' + widgetId).html(
                                        '<div class="no-favorites-message text-center py-5">' +
                                        '<p><?php esc_html_e('You have no more favorite properties.', 'estatesite-houzez'); ?></p>' +
                                        '</div>'
                                    );
                                }
                            });
                            
                            // Show success message
                            if ($('.estatesite-success-message').length === 0) {
                                $('<div class="estatesite-success-message">' +
                                  '<?php esc_html_e('Property removed from favorites', 'estatesite-houzez'); ?>' +
                                  '</div>')
                                .appendTo('body')
                                .delay(3000)
                                .fadeOut();
                            }
                        } else {
                            // Removal failed or was cancelled, restore opacity
                            $card.removeClass('estatesite-removing');
                        }
                    }, 500); // Wait 500ms for Houzez AJAX to complete
                }
            });
        });
        </script>
        <?php
    }
}

// Register the widget - matching your existing pattern
Plugin::instance()->widgets_manager->register( new EstateSite_Elementor_Favorite_Properties() );

// Add AJAX handler for removing favorites
add_action('wp_ajax_estatesite_remove_favorite', 'estatesite_remove_favorite_ajax_handler');
add_action('wp_ajax_nopriv_estatesite_remove_favorite', 'estatesite_remove_favorite_ajax_handler');

function estatesite_remove_favorite_ajax_handler() {
    check_ajax_referer('estatesite_favorites_nonce', 'nonce');
    
    $property_id = intval($_POST['property_id']);
    $user_id = get_current_user_id();
    
    if ($user_id && $property_id) {
        $favorites = get_user_meta($user_id, 'houzez_favorites', true);
        if (!is_array($favorites)) {
            $favorites = array();
        }
        
        $favorites = array_diff($favorites, array($property_id));
        update_user_meta($user_id, 'houzez_favorites', $favorites);
        
        wp_send_json_success(array('message' => 'Property removed from favorites'));
    }
    
    wp_send_json_error(array('message' => 'Failed to remove property'));
}