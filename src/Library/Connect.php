<?php
/**
 * BoldGrid Library Configs Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Library\Filter;

/**
 * BoldGrid Library Configs Class.
 *
 * This class is responsible for setting and getting configuration
 * options that are set for the library.
 *
 * @since 1.0.0
 */
class Connect {

	/**
	 * Add all filters for this class.
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * @hook Boldgrid\Library\Library\Notice\KeyPrompt_display
	 */
	public function show_prompt( $shouldDisplay ) {
		$screen = get_current_screen();
		$base = ! empty( $screen->base ) ? $screen->base : null;
		return 'settings_page_boldgrid-connect' === $base ? true : $shouldDisplay;
	}

	/**
	 * @hook admin_menu
	 */
	public function add_page() {
		add_filter(  'Boldgrid\Library\Library\Notice\KeyPrompt_display', '__return_true' );

		add_submenu_page(
			'options-general.php',
			'BoldGrid Connect',
			'BoldGrid Connect',
			'manage_options',
			'boldgrid-connect.php',
			function () {
				include __DIR__ . './Views/Connect.php';
			}
		);
	}
}
