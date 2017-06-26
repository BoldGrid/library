<?php
/**
 * BoldGrid Library Plugin Installer Install
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Plugin\Installer
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin\Installer;

use Boldgrid\Library\Library;

use \WP_Ajax_Upgrader_Skin;
use \Plugin_Upgrader;

/**
 * BoldGrid Library Plugin Installer Class.
 *
 * This class is responsible for installing BoldGrid plugins in the WordPress
 * admin's "Plugins > Add New" section.
 *
 * @since 1.0.0
 */
class Install {

	/**
	 * Adds filters.
	 *
	 * @since 1.0.0
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
	 * @hook: wp_ajax_installation
	 *
	 * @return $json
	 */
	public function installation() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			$msg =  __( 'Sorry, you are not allowed to install plugins on this site.', 'boldgrid-library' );
			wp_send_json_error( array( 'message' => $msg ) );
		}

		$nonce = $_POST['nonce'];
		$plugin = $_POST['plugin'];

		// Validate nonce.
		if ( ! wp_verify_nonce( $nonce, 'bglibPluginInstallerNonce' ) ) {
			wp_die( __( 'Error - unable to verify nonce, please try again.', 'boldgrid-library') );
		}

		// Include required WordPress Files.
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

		// Get Plugin API Info.
		$api = plugins_api(
			'plugin_information',
			array(
				'slug' => $plugin,
				'fields' => array(
					'short_description' => false,
					'sections' => false,
					'requires' => false,
					'rating' => false,
					'ratings' => false,
					'downloaded' => false,
					'last_updated' => false,
					'added' => false,
					'tags' => false,
					'compatibility' => false,
					'homepage' => false,
					'donate_link' => false,
				),
			)
		);

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$upgrader->install( $api->download_link );

		if ( $api->name ) {
			$status = 'success';
			$msg = sprintf( __( '%s successfully installed.', 'boldgrid-library' ),  $api->name );
		} else {
			$status = 'failed';
			$msg = sprintf( __( 'There was an error installing %s.', 'boldgrid-library' ), $api->name );
		}

		$json = array(
			'status' => $status,
			'message' => $msg,
		);

		wp_send_json( $json );
	}
}
