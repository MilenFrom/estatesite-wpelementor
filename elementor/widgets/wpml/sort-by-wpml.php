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

class Houzez_Sort_By_Translate {
    
    public function __construct() {
       add_filter( 'wpml_elementor_widgets_to_translate', [
            $this,
            'houzez_sortby_to_translate'
        ] );
    }

    public function houzez_sortby_to_translate( $widgets ) {

        $widgets['houzez_elementor_sort_by'] = [
            'conditions' => [ 'widgetType' => 'houzez_elementor_sort_by' ],
            'fields'     => [
                [
                    'field'       => 'sortby_title',
                    'type'        => esc_html__( 'Sort By: Title', 'houzez-theme-functionality' ),
                    'editor_type' => 'LINE'
                ],

            ],
        ];

        return $widgets;

    }
}

new Houzez_Sort_By_Translate();