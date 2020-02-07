<?php
/**
 * BoldGrid Plugin Notices.
 *
 * @package Boldgrid\Library
 *
 * @version SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library;
use Boldgrid\Library\Library\Filter;

/**
 * BoldGrid Library Plugin Notices Class.
 *
 * @since SINCEVERSION
 */
class Notices {
	/**
	 * Whether or not we've already added the filters in the constructor.
	 *
	 * Filters only need to be added once.
	 *
	 * @since SINCEVERSION
	 * @access private
	 * @var bool
	 */
	private static $filtersAdded = false;

	/**
	 * Construct.
	 *
	 * @since SINCEVERSION
	 */
	public function __construct() {
		// Only add the filters once.
		if ( ! self::$filtersAdded ) {
			Filter::add( $this );
			self::$filtersAdded = true;
		}
	}

	/**
	 * Admin enqueue scripts.
	 *
	 * @since SINCEVERSION
	 */
	public static function admin_enqueue_scripts() {
		$handle = 'bglib-plugin-notices';

		wp_register_script(
			$handle,
			Library\Configs::get( 'libraryUrl' ) . 'src/assets/js/plugin-notices.js',
			'jquery',
			Library\Configs::get( 'libraryVersion' )
		);

		$translation = [
			'counts' => [],
		];

		/**
		 * Allow other plugins to add notice counts.
		 *
		 * @since SINCEVERSION
		 *
		 * @param array $translation An array of translation data.
		 */
		$translation = apply_filters( 'Boldgrid\Library\Plugin\Notices\admin_enqueue_scripts', $translation );

		wp_localize_script( $handle, 'BglibPluginNotices', $translation );

		wp_enqueue_script( $handle );
	}
}
