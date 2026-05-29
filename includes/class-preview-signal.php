<?php
/**
 * Per-request signal that the Houzez_Preview_Query trait writes when a
 * widget's preview-post swap fails (no target picked, no fallback post).
 * The editor render-content filter reads it back to decide whether to
 * prepend an actionable hint to the widget's output.
 *
 * Lives outside the trait because trait `static` properties are per-using-class,
 * which would silo signals from different widget families.
 *
 * @package EstateSite\Elementor
 */

namespace EstateSite\Elementor;

defined( 'ABSPATH' ) || exit;

final class Preview_Signal {

	/** @var string Post type the most recent swap failed for, or '' when none. */
	private static $failed_for = '';

	public static function set_failure( string $post_type ): void {
		self::$failed_for = $post_type;
	}

	public static function clear(): void {
		self::$failed_for = '';
	}

	/** Read-and-clear: returns the post type, then resets state. */
	public static function consume(): string {
		$type             = self::$failed_for;
		self::$failed_for = '';
		return $type;
	}
}
