<?php
/**
 * BoldGrid Library Connect Page Class.
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Library\Page
 *
 * @version 2.4.0
 * @author BoldGrid <support@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Page;

use Boldgrid\Library\Library\Filter;
use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Connect Page Class.
 *
 * Create the BoldGrid Connect Page.
 *
 * @since 2.4.0.
 */
class Connect {

	/**
	 * Add all filters for this class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Run any needed methods on the Connect Page load.
	 *
	 * @since 2.4.0
	 *
	 * @hook current_screen
	 */
	public function onLoad( $screen ) {
		if ( $this->isConnectScreen( $screen ) ) {
			self::setupNotice();
		}
	}

	/**
	 * If we are performing an ajax call, setup the notice.
	 *
	 * Standard ajax handler is bound when the notice is created. Notice is not
	 * created during ajax call.
	 *
	 * @since 2.4.0
	 *
	 * @hook admin_init
	 */
	public function ajax() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( ! empty( $_POST['action'] ) && 'addKey' === sanitize_text_field( $_POST['action'] ) ) {
				self::setupNotice();
			}
		}
	}

	/**
	 * Setup the connect key notice.
	 *
	 * @since 2.4.0
	 */
	public static function setupNotice() {
		Configs::get( 'start' )->setupKeyConnections();
		Configs::get( 'start' )->getKey()->setNotice( true );
	}

	/**
	 * Check if the current screen is the connect screen.
	 *
	 * @since 2.4.0
	 *
	 * @param  object  $screen Current Screen.
	 * @return boolean         Is the screen the connect screen?
	 */
	public function isConnectScreen( $screen ) {
		$base = ! empty( $screen->base ) ? $screen->base : null;
		return 'settings_page_boldgrid-connect' === $base;
	}

	/**
	 * Fiter the show prompt display to true.
	 *
	 * @since 2.4.0
	 *
	 * @hook Boldgrid\Library\Library\Notice\KeyPrompt_display
	 */
	public function showPrompt( $shouldDisplay ) {
		return $this->isConnectScreen( get_current_screen() ) ? true : $shouldDisplay;
	}

	/**
	 * Enqueue scripts needed for this page.
	 *
	 * @since 2.4.0
	 *
	 * @hook admin_enqueue_scripts
	 */
	public function addScripts() {
		if ( $this->isConnectScreen( get_current_screen() ) ) {
			// Enqueue boldgrid-library-connect js.
			$handle = 'boldgrid-library-connect';

			wp_register_script(
				$handle,
				Configs::get( 'libraryUrl' ) .  'src/assets/js/connect.js' ,
				array( 'jquery' ),
				date( 'Ymd' ),
				false
			);

			$translation = array(
				'settingsSaved' => __( 'Settings saved.' ),
				'unknownError'  => __( 'Unknown error.', 'boldgrid-connect' ),
				'ajaxError'     => __( 'Could not reach the AJAX URL address. HTTP error: ', 'boldgrid-connect' ),
			);

			wp_localize_script( $handle, 'BoldGridLibraryConnect', $translation );

			wp_enqueue_script( $handle );

			// Enqueue jquery-toggles js.
			wp_enqueue_script(
				'jquery-toggles',
				Configs::get( 'libraryUrl' ) .  'build/toggles.min.js',
				array( 'jquery' ),
				date( 'Ymd' ),
				true
			);

			// Enqueue jquery-toggles css.
			wp_enqueue_style( 'jquery-toggles-full',
				Configs::get( 'libraryUrl' ) .  'build/toggles-full.css', array(), date( 'Ymd' ) );

			/**
			 * Add additional scripts to Connect page.
			 *
			 * @since 2.4.0
			 */
			do_action( 'Boldgrid\Library\Library\Page\Connect\addScripts' );
		}
	}

	/**
	 * Create the BoldGird Connect Page.
	 *
	 * @since 2.4.0
	 *
	 * @hook admin_menu
	 */
	public function addPage() {
		add_submenu_page(
			'options-general.php',
			__( 'BoldGrid Connect' ),
			__( 'BoldGrid Connect' ),
			'manage_options',
			'boldgrid-connect.php',
			function () {
				include __DIR__ . '/../Views/Connect.php';
			}
		);
	}

	/**
	 * AJAX callback for the Connect Settings page.
	 *
	 * @since 2.5.0
	 *
	 * @uses $_POST['autoupdate'] Auto-update settings for plugins and themes.
	 *
	 * @hook wp_ajax_boldgrid_library_connect_settings_save
	 */
	public function saveSettings() {
		// Check user permissions.
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error( array(
				'error' => __( 'User access violation!', 'boldgrid-connect' ),
			) );
		}

		// Check security nonce and referer.
		if ( ! check_admin_referer( 'boldgrid_library_connect_settings_save' ) ) {
			wp_send_json_error( array(
				'error' => __( 'Security violation! Please try again.', 'boldgrid-connect' ),
			) );
		}

		// Read auto-update settings.
		$autoupdate = ! empty( $_POST['autoupdate'] ) ? (array) $_POST['autoupdate'] : array();

		// Merge settings.
		$settings = array(
			'autoupdate' => $autoupdate,
		);

		$boldgrid_settings = get_option( 'boldgrid_settings' );

		$boldgrid_settings['connect_settings'] = $this->sanitizeSettings( $settings );

		if ( update_option( 'boldgrid_settings', $boldgrid_settings, false ) ) {
 			wp_send_json_success( $settings );
		} else {
			wp_send_json_error( array(
				'error' => esc_html__( 'Could not save settings to database.', 'boldgrid-connect' ),
			) );
		}
	}

	/**
	 * Sanitize settings.
	 *
	 * @since 2.5.0
	 *
	 * @access protected
	 *
	 * @param  array $settings {
	 *     Settings.
	 *
	 *     @type array $autoupdate {
	 *         Auto-update settings.
	 *
	 *         @type array $plugins {
	 *             Plugin auto-update settings.
	 *
	 *             @type string $$slug Plugin auto-update setting (1=Enabled, 2=Disabled).
	 *         }
	 *         @type array $themes {
	 *             Theme auto-update settings.
	 *
	 *             @type string $$stylesheet Theme auto-update setting (1=Enabled, 2=Disabled).
	 *         }
	 *     }
	 * }
	 * @return array
	 */
	protected function sanitizeSettings( array $settings ) {
		$result = array();

		foreach ( $settings as $category => $group ) {
			$category = sanitize_key( $category );

			foreach ( $group as $groupName => $itemSetting ) {
				$groupName = sanitize_key( $groupName );

				foreach ( $itemSetting as $id => $val ) {
					$id = sanitize_text_field( $id );
					$val = (bool) $val;

					$result[ $category ][ $groupName ][ $id ] = $val;
				}
			}
		}

		return $result;
	}
}
