<?php
/**
 * Auto-ported from Houzez framework/functions/ to EstateSite Core.
 * Direct fave_* meta access has been rewritten to use \EstateSite\Core\Property::get/set.
 *
 * @package EstateSite\Core\Functions
 */

defined( 'ABSPATH' ) || exit;

Class Property_Title_Tag extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'houzez-property-title-tag';
	}

	public function get_title() {
		return __( 'Property Title', 'houzez-theme-functionality' );
	}

	public function get_group() {
		return Houzez_Elementor_Extensions::HOUZEZ_GROUP;
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	public function render() {
		echo wp_kses_post( get_the_title() );
	}
}
