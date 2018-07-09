<?php
namespace Boldgrid\Library\Library;

// use Boldgrid\Library\Util\Version;

class Asset {

	/**
	 * Add all filters for this class.
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * @hook admin_enqueue_scripts
	 */
	public function addStyles() {
		wp_enqueue_style( 'boldgrid-library-admin',
			Configs::get( 'libraryUrl' ) .  'src/assets/css/admin.css',
			array(), time() );
	}
}
 ?>
