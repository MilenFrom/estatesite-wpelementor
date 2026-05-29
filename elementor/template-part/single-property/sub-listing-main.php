<?php
global $settings, $post, $multi_units_ids, $multi_units_ids;
$multi_units_ids = houzez_get_listing_data('multi_units_ids');


if($multi_units_ids != "") {
	htf_get_template_part('elementor/template-part/single-property/sub', 'listings');
} else {
	get_template_part('property-details/sub-listings-table');
}
?>