<?php
/**
 * Admin page — "EstateSite Elementor → Templates".
 *
 * Thin-client model: the Elementor template library is no longer bundled
 * inside the plugin zip or downloaded as a tarball. Instead, the library
 * is served live from dev.estatesite.eu and accessed at runtime via the
 * Templates class. This page is purely informational — it shows the
 * configured library URL, current local status, and a "Test connection"
 * button that pings the remote manifest to verify reachability.
 *
 * @package EstateSite\Elementor\Admin
 */

namespace EstateSite\Elementor\Admin;

use EstateSite\Elementor\Templates;

defined( 'ABSPATH' ) || exit;

final class Templates_Page {

	private const PAGE_SLUG    = 'estatesite-wpelementor-templates';
	private const NONCE_ACTION = 'estatesite_wpelementor_test_library';

	public function __construct() {
		add_action( 'admin_menu',                                            [ $this, 'register_menu' ], 25 );
		add_action( 'wp_ajax_estatesite_wpelementor_test_library', [ $this, 'ajax_test_library' ] );
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

		$present     = Templates::library_present();
		$stats       = $present ? Templates::stats() : [];
		$library_url = Templates::library_url();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'EstateSite Elementor — Templates', 'estatesite-wpelementor' ); ?></h1>

			<div class="card" style="max-width:none;">
				<h2><?php esc_html_e( 'Library Status', 'estatesite-wpelementor' ); ?></h2>
				<table class="widefat striped" style="max-width:600px;">
					<tr>
						<th><?php esc_html_e( 'Local templates present', 'estatesite-wpelementor' ); ?></th>
						<td><?php echo $present ? '&#10003; ' . esc_html__( 'Yes', 'estatesite-wpelementor' ) : '&mdash; ' . esc_html__( 'No (thin-client mode — served remotely)', 'estatesite-wpelementor' ); ?></td>
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
				</table>
			</div>

			<div class="card" style="max-width:none;">
				<h2><?php esc_html_e( 'Remote Library', 'estatesite-wpelementor' ); ?></h2>
				<p>
					<?php esc_html_e( 'The Elementor template library is served live from the EstateSite update server. No local download or extraction is required.', 'estatesite-wpelementor' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'Library URL:', 'estatesite-wpelementor' ); ?></strong>
					<code><?php echo esc_html( $library_url ); ?></code>
				</p>
				<p>
					<button type="button" class="button button-primary" id="esele-test-library">
						<?php esc_html_e( 'Test connection', 'estatesite-wpelementor' ); ?>
					</button>
					<span class="esele-test-status" style="margin-left:1em;"></span>
				</p>
				<p class="description">
					<?php esc_html_e( 'Pings the manifest endpoint and reports the HTTP status and how many template entries it advertises.', 'estatesite-wpelementor' ); ?>
				</p>
			</div>
		</div>

		<script>
		(function () {
			var $btn    = document.getElementById('esele-test-library');
			var $status = document.querySelector('.esele-test-status');
			if (!$btn) return;

			$btn.addEventListener('click', function () {
				$btn.disabled = true;
				$status.textContent = '<?php echo esc_js( __( 'Testing…', 'estatesite-wpelementor' ) ); ?>';

				var body = new URLSearchParams();
				body.append('action', 'estatesite_wpelementor_test_library');
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
						$status.innerHTML = '<span style="color:#b04632;">&#10007; ' +
							(j.data && j.data.message ? j.data.message : 'Error') + '</span>';
						return;
					}
					$status.innerHTML = '<span style="color:#2c7a3a;">&#10003; HTTP ' +
						j.data.http_code + ' — ' + j.data.count + ' template(s) advertised.</span>';
				})
				.catch(function (e) {
					$btn.disabled = false;
					$status.innerHTML = '<span style="color:#b04632;">&#10007; ' + e.message + '</span>';
				});
			});
		})();
		</script>
		<?php
	}

	/**
	 * AJAX: ping the remote manifest URL and report reachability.
	 *
	 * Returns success on HTTP 200 with a parseable JSON list, error otherwise.
	 */
	public function ajax_test_library(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'No permission.', 'estatesite-wpelementor' ) ] );
		}
		check_ajax_referer( self::NONCE_ACTION );

		$url  = Templates::manifest_url();
		$resp = wp_safe_remote_get( $url, [ 'timeout' => 15 ] );

		if ( is_wp_error( $resp ) ) {
			wp_send_json_error( [
				'message' => sprintf(
					/* translators: %s: error message from WP_Error. */
					__( 'Cannot reach library: %s', 'estatesite-wpelementor' ),
					$resp->get_error_message()
				),
			] );
		}

		$code = (int) wp_remote_retrieve_response_code( $resp );
		$body = wp_remote_retrieve_body( $resp );
		$data = json_decode( $body, true );

		// The manifest can be either a plain list or an object with a list under
		// a conventional key — count whichever shape we get.
		$count = 0;
		if ( is_array( $data ) ) {
			if ( isset( $data['templates'] ) && is_array( $data['templates'] ) ) {
				$count = count( $data['templates'] );
			} elseif ( isset( $data['items'] ) && is_array( $data['items'] ) ) {
				$count = count( $data['items'] );
			} else {
				$count = count( $data );
			}
		}

		if ( $code !== 200 ) {
			wp_send_json_error( [
				'message'   => sprintf(
					/* translators: %d: HTTP status code. */
					__( 'Library returned HTTP %d.', 'estatesite-wpelementor' ),
					$code
				),
				'http_code' => $code,
				'count'     => $count,
			] );
		}

		wp_send_json_success( [
			'http_code' => $code,
			'count'     => $count,
			'url'       => $url,
		] );
	}
}
