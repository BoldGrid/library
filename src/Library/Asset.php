<?php
/**
 * Manage javascript, css, and images. Front end assets.
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Library\Asset
 *
 * @version 2.4.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
* Manage javascript, css, and images. Front end assets.
 *
 * @since 2.4.0.
 */
class Asset {

	/**
	 * Add all filters for this class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Add styles for all admin pages.
	 *
	 * @since 2.4.0
	 *
	 * @hook admin_enqueue_scripts
	 */
	public function addStyles() {
		wp_enqueue_style( 'bglib-admin',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/admin.css' );
	}
}
 ?>
