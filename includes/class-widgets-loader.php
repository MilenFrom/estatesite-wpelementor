<?php
/**
 * Widgets loader — registers all EstateSite Elementor widgets.
 *
 * Phase 0: empty stub. Phase 4 will populate this with the 66 ported widgets.
 *
 * @package EstateSite\Elementor
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Widgets_Loader {

	/** @var array<string,string> Logical widget name => widget class. */
	private static $widgets = [
		// Phase 4 entries will look like:
		// 'property-card-v1' => Widgets\Property_Card_V1::class,
	];

	public static function register_widgets( $widgets_manager ): void {
		foreach ( self::$widgets as $name => $class ) {
			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}
}
