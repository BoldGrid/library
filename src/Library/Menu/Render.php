<?php
/**
 * Create the BoldGrid Menu in the upper left of the admin screen.
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Menu
 *
 * @version X.X.X
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Menu;

/**
* Create the BoldGrid Menu in the upper left of the admin screen.
 *
 * @since X.X.X
 */
class Render {

	/**
	 * Given a configuration of menu items to be added on
	 *
	 * @since X.X.X
	 *
	 * @param WP_Admin_Bar $wpAdminBar Admin Bar.
	 * @param array        $configs    Configurations for the menu.
	 */
	public static function adminBarNode( $wpAdminBar, $configs ) {
		$wpAdminBar->add_node( $configs['topLevel'] );
		foreach ( $configs['items'] as $item ) {
			$wpAdminBar->add_menu( $item );
		}
	}
}
