<?php
/**
 * BoldGrid Library Connect Page Class.
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Library\Page
 *
 * @version X.X.X
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Page;

use Boldgrid\Library\Library\Filter;
use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Connect Page Class.
 *
 * Create the BoldGrid Connect Page.
 *
 * @since X.X.X.
 */
class Connect {

	/**
	 * Add all filters for this class.
	 *
	 * @since X.X.X
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Run any needed methods on the Connect Page load.
	 *
	 * @since X.X.X
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
	 * @since X.X.X
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
	 * @since X.X.X
	 */
	public static function setupNotice() {
		Configs::get( 'start' )->setupKeyConnections();
		Configs::get( 'start' )->getKey()->setNotice( true );
	}

	/**
	 * Check if the current screen is the connect screen.
	 *
	 * @since X.X.X
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
	 * @since X.X.X
	 *
	 * @hook Boldgrid\Library\Library\Notice\KeyPrompt_display
	 */
	public function showPrompt( $shouldDisplay ) {
		return $this->isConnectScreen( get_current_screen() ) ? true : $shouldDisplay;
	}

	/**
	 * Enqueue Scripts needed for this page.
	 *
	 * @since X.X.X
	 *
	 * @hook admin_enqueue_scripts
	 */
	public function addScripts() {
		if ( $this->isConnectScreen( get_current_screen() ) ) {
			wp_enqueue_script( 'boldgrid-library-connect',
				Configs::get( 'libraryUrl' ) .  'src/assets/js/connect.js',
				array(), time() );
		}
	}

	/**
	 * Create the BoldGird Connect Page.
	 *
	 * @since X.X.X
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
}
