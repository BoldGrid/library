<?php
/**
 * BoldGrid Library Plugin Installer Upgrade
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Plugin
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin\Installer;

use Boldgrid\Library\Library;

use \Automatic_Upgrader_Skin;
use \Plugin_Upgrader;

/**
 * BoldGrid Library Plugin Installer Upgrade Class.
 *
 * This class is responsible for installing BoldGrid plugins in the WordPress
 * admin's "Plugins > Add New" section.
 *
 * @since 1.0.0
 */
class Upgrade {

	/**
	 * Adds filters.
	 *
	 * @since 1.0.0
	 *
	 * @nohook
	 */
	public function init() {
		Library\Filter::add( $this );
	}

	/**
	 * Called via Ajax for installing a WordPress plugin.
	 *
	 * This is what handles the installation of outside sources for plugins.  We
	 * use this to allow our premium plugins/extensions to be installed, which
	 * aren't hosted on the WordPress.org repository.
	 *
	 * @since 1.0.0
	 *
	 * @hook: wp_ajax_upgrade
	 *
	 * @return $jsonwp_ajax_update_plugin();
	 */
	public function upgrade() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'Sorry, you are not allowed to upgrade plugins on this site!', 'boldgrid-library' ) );
		}

		$nonce = $_POST['nonce'];
		$plugin = $_POST['plugin'];
		$slug = $_POST['slug'];
		$title = $_POST['title'];

		// Validate nonce.
		if ( ! wp_verify_nonce( $nonce, 'bglibPluginInstallerNonce' ) ) {
			wp_die( __( 'Error - unable to verify nonce, please try again.', 'boldgrid-library') );
		}

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$plugin = urldecode( $_POST['plugin'] );
		$title = esc_attr( $_POST['title'] );

		$status = array(
			'message'   => sprintf( __( '%s successfully updated!' ), $title ),
			'update'    => 'plugin',
			'plugin'    => $plugin,
			'slug'      => $_POST['slug'],
		);

		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result = $upgrader->upgrade( $plugin );

		if ( is_wp_error( $result ) ) {
			$status['error'] = $result->get_error_message();
			wp_send_json_error( $status );
		}

		wp_send_json_success( $status );
	}
}
