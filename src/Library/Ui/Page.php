<?php
/**
 * BoldGrid Library Ui Page.
 *
 * @package Boldgrid\Plugin
 *
 * @since SINCEVERSION
 *
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Ui;

use Boldgrid\Library\Library;

/**
 * Generic page class.
 *
 * @since SINCEVERSION
 */
class Page {
	/**
	 * Enqueue scripts.
	 *
	 * @since SINCEVERSION
	 */
	public static function enqueueScripts() {
		$handle = 'bglib-page';

		wp_register_style(
			$handle,
			Library\Configs::get( 'libraryUrl' ) . 'src/assets/css/page.css',
			[],
			Library\Configs::get( 'libraryVersion' )
		);

		wp_enqueue_style( $handle );
	}
}
