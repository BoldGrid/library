<?php
/**
 * Manage javascript, css, and images. Front end assets.
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Library\Asset
 *
 * @version X.X.X
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
* Manage javascript, css, and images. Front end assets.
 *
 * @since X.X.X.
 */
class Asset {

	/**
	 * Add all filters for this class.
	 *
	 * @since X.X.X
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Add styles for all admin pages.
	 *
	 * @since X.X.X
	 *
	 * @hook admin_enqueue_scripts
	 */
	public function addStyles() {
		wp_enqueue_style( 'bglib-admin',
			Configs::get( 'libraryUrl' ) .  'src/assets/css/admin.css',
			array(), time() );
	}
}
 ?>
