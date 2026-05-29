<?php
/**
 * Admin page — "EstateSite Elementor → Templates".
 *
 * Hosts the "Fetch templates" button that triggers Template_Fetcher to
 * download the ~140 MB template tarball from our update server and extract
 * it into wp-content/uploads/estatesite-wpelementor/templates/.
 *
 * Templates aren't bundled in the plugin zip — see Template_Fetcher for the
 * reasoning. This page is the customer-facing way to install them after
 * the lightweight plugin zip is activated.
 *
 * @package EstateSite\Elementor\Admin
 */

namespace EstateSite\Elementor\Admin;

use EstateSite\Elementor\Templates;
use EstateSite\Elementor\Template_Fetcher;

defined( 'ABSPATH' ) || exit;

final class Templates_Page {

	private const PAGE_SLUG    = 'estatesite-wpelementor-templates';
	private const NONCE_ACTION = 'estatesite_wpelementor_fetch_templates';

	public function __construct() {
		add_action( 'admin_menu',                                              [ $this, 'register_menu' ], 25 );
		add_action( 'wp_ajax_estatesite_wpelementor_fetch_templates', [ $this, 'ajax_fetch' ] );
	}

	public function register_menu(): void {
		// Sit under the EstateSite top-level menu if it exists (Core registers it),
		// otherwise fall back to the Tools menu.
		global $admin_page_hooks;
		$parent = isset( $admin_page_hooks['estatesite'] ) ? 'estatesite' : 'tools.php';

		add_submenu_page(
			$parent,
			__( 'Templates', 'estatesite-wpelementor' ),
			__( 'EE Templates', 'estatesite-wpelementor' ),
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render' ]
		);
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'estatesite-wpelementor' ) );
		}

		$present    = Templates::library_present();
		$stats      = $present ? Templates::stats() : [];
		$last_fetch = Template_Fetcher::last_fetch();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'EstateSite Elementor — Templates', 'estatesite-wpelementor' ); ?></h1>

			<div class="card" style="max-width:none;">
				<h2><?php esc_html_e( 'Library Status', 'estatesite-wpelementor' ); ?></h2>
				<table class="widefat striped" style="max-width:600px;">
					<tr>
						<th><?php esc_html_e( 'Templates installed', 'estatesite-wpelementor' ); ?></th>
						<td><?php echo $present ? '✓ ' . esc_html__( 'Yes', 'estatesite-wpelementor' ) : '— ' . esc_html__( 'Not yet — click Fetch below', 'estatesite-wpelementor' ); ?></td>
					</tr>
					<?php if ( $present ) : ?>
					<tr>
						<th><?php esc_html_e( 'Manifest entries', 'estatesite-wpelementor' ); ?></th>
						<td><?php echo (int) ( $stats['manifest_entries'] ?? 0 ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Content files', 'estatesite-wpelementor' ); ?></th>
						<td><?php echo (int) ( $stats['content_files'] ?? 0 ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Image files', 'estatesite-wpelementor' ); ?></th>
						<td><?php echo (int) ( $stats['image_files'] ?? 0 ); ?></td>
					</tr>
					<?php endif; ?>
					<?php if ( $last_fetch ) : ?>
					<tr>
						<th><?php esc_html_e( 'Last fetched', 'estatesite-wpelementor' ); ?></th>
						<td>
							v<?php echo esc_html( $last_fetch['version'] ); ?>
							— <?php echo esc_html( gmdate( 'Y-m-d H:i', $last_fetch['at'] ) ); ?> UTC
							(<?php echo esc_html( size_format( $last_fetch['size_bytes'] ?? 0 ) ); ?>)
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</div>

			<div class="card" style="max-width:none;">
				<h2><?php esc_html_e( 'Fetch / Update Templates', 'estatesite-wpelementor' ); ?></h2>
				<p>
					<?php esc_html_e( 'Downloads the Houzez template library (~140 MB) from the EstateSite update server and installs it into wp-content/uploads/. Safe to re-run — replaces the current library atomically.', 'estatesite-wpelementor' ); ?>
				</p>
				<p>
					<button type="button" class="button button-primary" id="esele-fetch-templates">
						<?php echo $present
							? esc_html__( 'Re-fetch templates', 'estatesite-wpelementor' )
							: esc_html__( 'Fetch templates', 'estatesite-wpelementor' ); ?>
					</button>
					<span class="esele-fetch-status" style="margin-left:1em;"></span>
				</p>
				<p class="description">
					<?php esc_html_e( 'Tip: the fetch may take 30–120 seconds on slow connections. Keep this tab open until you see "✓ Done".', 'estatesite-wpelementor' ); ?>
				</p>
			</div>
		</div>

		<script>
		(function () {
			var $btn    = document.getElementById('esele-fetch-templates');
			var $status = document.querySelector('.esele-fetch-status');
			if (!$btn) return;

			$btn.addEventListener('click', function () {
				$btn.disabled = true;
				$status.textContent = '<?php echo esc_js( __( 'Downloading… (this can take a minute)', 'estatesite-wpelementor' ) ); ?>';

				var body = new URLSearchParams();
				body.append('action', 'estatesite_wpelementor_fetch_templates');
				body.append('_wpnonce', '<?php echo esc_js( wp_create_nonce( self::NONCE_ACTION ) ); ?>');

				fetch(ajaxurl, {
					method: 'POST',
					credentials: 'same-origin',
					body: body
				})
				.then(function (r) { return r.json(); })
				.then(function (j) {
					$btn.disabled = false;
					if (!j.success) {
						$status.innerHTML = '<span style="color:#b04632;">✗ ' +
							(j.data && j.data.message ? j.data.message : 'Error') + '</span>';
						return;
					}
					$status.innerHTML = '<span style="color:#2c7a3a;">✓ ' +
						(j.data.message || 'Done') + ' Reloading…</span>';
					setTimeout(function () { window.location.reload(); }, 1500);
				})
				.catch(function (e) {
					$btn.disabled = false;
					$status.innerHTML = '<span style="color:#b04632;">✗ ' + e.message + '</span>';
				});
			});
		})();
		</script>
		<?php
	}

	public function ajax_fetch(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'No permission.', 'estatesite-wpelementor' ) ] );
		}
		check_ajax_referer( self::NONCE_ACTION );

		// The fetch involves a 140 MB download + extract — give PHP room.
		@set_time_limit( 600 );
		@ini_set( 'memory_limit', '512M' );

		$r = Template_Fetcher::fetch();
		if ( $r['ok'] ) {
			wp_send_json_success( $r );
		}
		wp_send_json_error( $r );
	}
}
