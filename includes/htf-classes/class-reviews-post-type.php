<?php
/**
 * Auto-ported from Houzez framework/functions/ to EstateSite Core.
 * Direct fave_* meta access has been rewritten to use \EstateSite\Core\Property::get/set.
 *
 * @package EstateSite\Core\Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Post_Type_Reviews {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'definition' ) );
        add_action( 'updated_postmeta', array( __CLASS__, 'recalculate_rating_on_meta_update' ), 10, 4 );
        add_action( 'added_post_meta', array( __CLASS__, 'recalculate_rating_on_meta_update' ), 10, 4 );
        add_action( 'transition_post_status', array( __CLASS__, 'recalculate_rating_on_status_change' ), 10, 3 );
        add_action( 'admin_init', array( __CLASS__, 'maybe_recalculate_all_ratings' ) );
        add_filter( 'manage_edit-houzez_reviews_columns', array( __CLASS__, 'custom_columns' ) );
        add_action( 'manage_houzez_reviews_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
        add_action( 'admin_action_houzez_review_accept', array( __CLASS__, 'review_accept' ) );
        add_action( 'admin_action_houzez_review_reject', array( __CLASS__, 'review_reject' ) );
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name' => __( 'Reviews','houzez-theme-functionality'),
            'singular_name' => __( 'Review','houzez-theme-functionality' ),
            'add_new' => __('Add New','houzez-theme-functionality'),
            'add_new_item' => __('Add New Review','houzez-theme-functionality'),
            'edit_item' => __('Edit Review','houzez-theme-functionality'),
            'new_item' => __('New Review','houzez-theme-functionality'),
            'view_item' => __('View Review','houzez-theme-functionality'),
            'search_items' => __('Search Review','houzez-theme-functionality'),
            'not_found' =>  __('No Review found','houzez-theme-functionality'),
            'not_found_in_trash' => __('No Review found in Trash','houzez-theme-functionality'),
            'parent_item_colon' => ''
        );

        $labels = apply_filters( 'houzez_post_type_review_labels', $labels );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_in_menu'        => false,
            'show_in_admin_bar'   => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'query_var' => false,
            'has_archive' => false,
            'capability_type' => 'post',
            'exclude_from_search' => true,
            'hierarchical' => true,
            'can_export' => true,
            'menu_position' => 15,
            'supports' => array('title','editor','revisions', 'author'),
            'show_in_rest'       => true,
            'rest_base'          => 'houzez_reviews',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite' => array( 'slug' => 'reviews' )
        );

        $args = apply_filters( 'houzez_post_type_review_args', $args );

        register_post_type('houzez_reviews',$args);
    }

    /**
     * Recalculate property rating when review_stars meta is updated.
     * Uses WordPress core hooks that fire AFTER meta is saved to the database.
     *
     * @param int    $meta_id    ID of the metadata entry.
     * @param int    $post_id    Post ID.
     * @param string $meta_key   Metadata key.
     * @param mixed  $meta_value Metadata value.
     */
    public static function recalculate_rating_on_meta_update( $meta_id, $post_id, $meta_key, $meta_value ) {
        // Only trigger on review_stars meta updates for houzez_reviews post type
        if ( 'review_stars' !== $meta_key ) {
            return;
        }

        if ( 'houzez_reviews' !== get_post_type( $post_id ) ) {
            return;
        }

        // Only recalculate for published reviews
        if ( 'publish' !== get_post_status( $post_id ) ) {
            return;
        }

        // Call the existing rating recalculation function
        if ( function_exists( 'houzez_admin_review_meta_on_save' ) ) {
            houzez_admin_review_meta_on_save( $post_id );
        }
    }

    /**
     * Recalculate property rating when review status changes.
     * Handles publish/unpublish transitions that affect the average.
     *
     * @param string  $new_status New post status.
     * @param string  $old_status Old post status.
     * @param WP_Post $post       Post object.
     */
    public static function recalculate_rating_on_status_change( $new_status, $old_status, $post ) {
        // Only process houzez_reviews post type
        if ( 'houzez_reviews' !== $post->post_type ) {
            return;
        }

        // Only recalculate if status changed to/from publish
        if ( $new_status === $old_status ) {
            return;
        }

        if ( 'publish' !== $new_status && 'publish' !== $old_status ) {
            return;
        }

        // Call the existing rating recalculation function
        if ( function_exists( 'houzez_admin_review_meta_on_save' ) ) {
            houzez_admin_review_meta_on_save( $post->ID );
        }
    }

    /**
     * One-time migration to recalculate all property ratings.
     * Runs automatically on plugin update for existing clients.
     * Can also be triggered manually by visiting any admin page with ?update-rating=true
     */
    public static function maybe_recalculate_all_ratings() {
        // Only run for admins
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Check for manual trigger via URL parameter
        $manual_trigger = isset( $_GET['update-rating'] ) && $_GET['update-rating'] === 'true';

        // Check if migration has already run (skip this check if manual trigger)
        $migration_key = 'houzez_ratings_recalculated_v2';
        if ( ! $manual_trigger && get_option( $migration_key ) ) {
            return;
        }

        // Get all published reviews
        $reviews = get_posts( array(
            'post_type'      => 'houzez_reviews',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );

        if ( empty( $reviews ) ) {
            if ( ! $manual_trigger ) {
                update_option( $migration_key, true );
            }
            if ( $manual_trigger ) {
                add_action( 'admin_notices', array( __CLASS__, 'rating_update_notice_empty' ) );
            }
            return;
        }

        // Recalculate rating for each review's associated property/agent/agency
        foreach ( $reviews as $review_id ) {
            if ( function_exists( 'houzez_admin_review_meta_on_save' ) ) {
                houzez_admin_review_meta_on_save( $review_id );
            }
        }

        // Mark migration as complete
        update_option( $migration_key, true );

        // Show admin notice if manually triggered
        if ( $manual_trigger ) {
            set_transient( 'houzez_ratings_updated_count', count( $reviews ), 30 );
            add_action( 'admin_notices', array( __CLASS__, 'rating_update_notice_success' ) );
        }
    }

    /**
     * Admin notice for successful rating recalculation
     */
    public static function rating_update_notice_success() {
        $count = get_transient( 'houzez_ratings_updated_count' );
        delete_transient( 'houzez_ratings_updated_count' );
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php printf( esc_html__( 'All ratings have been recalculated successfully. %d reviews processed.', 'houzez-theme-functionality' ), intval( $count ) ); ?></p>
        </div>
        <?php
    }

    /**
     * Admin notice when no reviews found
     */
    public static function rating_update_notice_empty() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php esc_html_e( 'No published reviews found to recalculate.', 'houzez-theme-functionality' ); ?></p>
        </div>
        <?php
    }

    /**
     * Custom admin columns for post type
     *
     * @access public
     * @return array
     */
    public static function custom_columns() {
        $fields = array(
            'cb'                => '<input type="checkbox" />',
            'title'             => esc_html__( 'Title', 'houzez-theme-functionality' ),
            'ratings'       => esc_html__( 'Stars', 'houzez-theme-functionality' ),
            'post_title'          => esc_html__( 'Review On', 'houzez-theme-functionality' ),
            'review_actions' => __( 'Actions','houzez-theme-functionality' ),
            'date' => esc_html__('Date', 'houzez-theme-functionality')
        );

        return $fields;
    }

    /**
     * Custom admin columns implementation
     *
     * @access public
     * @param string $column
     * @return array
     */
    public static function custom_columns_manage( $column ) {
        global $post;
        switch ( $column ) {
            case 'ratings':
                echo get_post_meta($post->ID, 'review_stars', true);
                break;
            case 'post_title':
                $review_id = $post->ID;
                $review_post_type = get_post_meta($review_id, 'review_post_type', true);
                if($review_post_type == 'property') {
                    $listing_id = get_post_meta($review_id, 'review_property_id', true);
                    $meta_key = 'review_property_id';

                } else if($review_post_type == 'houzez_agent') {
                    $listing_id = get_post_meta($review_id, 'review_agent_id', true);
                    $meta_key = 'review_agent_id';

                } else if($review_post_type == 'houzez_agency') {
                    $listing_id = get_post_meta($review_id, 'review_agency_id', true);
                    $meta_key = 'review_agency_id';

                } else if($review_post_type == 'houzez_author') {
                    $listing_id = get_post_meta($review_id, 'review_author_id', true);
                    $meta_key = 'review_author_id';
                } else {
                    $listing_id = $review_id;
                }

                echo '<a target="_blank" href="'.get_permalink( $listing_id ).'">';
                echo get_the_title($listing_id);
                echo '</a>';
                break;
            case 'review_actions':
                
                echo '<div class="actions">';

                $admin_actions = apply_filters( 'post_row_actions', array(), $post );


                $user = wp_get_current_user();

                if ( in_array( $post->post_status, array( 'pending', 'review_rejected' ) ) && (in_array( 'administrator', (array) $user->roles ) || in_array( 'editor', (array) $user->roles ) || in_array( 'houzez_manager', (array) $user->roles )) ) {
                    $admin_actions['review_accept']   = array(
                        'class'  => 'accept',
                        'name'    => __( 'Approve', 'houzez-theme-functionality' ),
                        'icon'    => 'dashicons dashicons-yes',
                        'url' => add_query_arg( array(
                            'action' => 'houzez_review_accept',
                            'review_id' => $post->ID,
                        ), 'admin.php' )
                    );
                }
                
                if ( in_array( $post->post_status, array( 'pending', 'publish' ) ) && (in_array( 'administrator', (array) $user->roles ) || in_array( 'editor', (array) $user->roles ) || in_array( 'houzez_manager', (array) $user->roles )) ) {
                    $admin_actions['review_reject']   = array(
                        'class'  => 'reject',
                        'name'    => __( 'Unapprove', 'houzez-theme-functionality' ),
                        'icon'    => 'dashicons dashicons-no-alt',
                        'url' => add_query_arg( array(
                            'action' => 'houzez_review_reject',
                            'review_id' => $post->ID,
                        ), 'admin.php' )
                    );
                }

                $admin_actions = apply_filters( 'review_admin_actions', $admin_actions, $post );

                foreach ( $admin_actions as $action ) {
                    if ( is_array( $action ) ) {
                        printf( '<a class="button houzez-button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s"><span class="%4$s"></span></a>', $action['class'], esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_html( $action['icon'] ) );
                    } else {
                        
                    }
                }


                echo '</div>';

                break;

        }
    }

    public static function review_accept() {

        if (! ( isset( $_GET['review_id']) || isset( $_POST['review_id'])  || ( isset($_REQUEST['action']) && 'houzez_review_accept' == $_REQUEST['action'] ) ) ) {
            wp_die('No review exist');
        }
     
        /*
         * get the original listing id
         */
        $review_id = (isset($_GET['review_id']) ? $_GET['review_id'] : $_POST['review_id']);

        $post_id = absint($review_id);
        $agrs = array(
            'ID' => $post_id,
            'post_status' => 'publish'
        );
        wp_update_post($agrs);

        if(!empty($post_id)) {
            houzez_admin_review_meta_on_save($post_id);
        }

        wp_redirect( admin_url( 'edit.php?post_type=houzez_reviews') );
        exit;
    }

    public static function review_reject() {

        if (! ( isset( $_GET['review_id']) || isset( $_POST['review_id'])  || ( isset($_REQUEST['action']) && 'houzez_review_accept' == $_REQUEST['action'] ) ) ) {
            wp_die('No review exist');
        }
     
        /*
         * get the original listing id
         */
        $review_id = (isset($_GET['review_id']) ? $_GET['review_id'] : $_POST['review_id']);

        $post_id = absint($review_id);
        $agrs = array(
            'ID' => $post_id,
            'post_status' => 'review_rejected'
        );
        wp_update_post($agrs);

        if(!empty($post_id)) {
            houzez_admin_review_meta_on_save($post_id);
        }

        wp_redirect( admin_url( 'edit.php?post_type=houzez_reviews') );
        exit;
    }

}
?>