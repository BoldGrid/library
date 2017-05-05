<?php
/**
 * BoldGrid Library Compatibility Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Compat
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Compat;

use Boldgrid\Library\Library\Configs;
use Boldgrid\Library\Util;

/**
 * The BoldGrid Library Compatibility Class.
 *
 * This class is responsible for any of the backwards
 * compatibility changes that need to occur for the
 * library to work properly.
 *
 * @since 1.0.0
 */
class Compat {

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->inspirations();
	}

	/**
	 * Inspirations compatibility code.
	 *
	 * This code is only necessary for backwards compatibility. First we remove
	 * the anonymous object created for pre_add_hooks in the init hook.  This is
	 * at priority 0 to insure removal, then we add our modified non-extendable
	 * child to override the Boldgrid_Inspirations_Inspiration::pre_add_hooks method.
	 * This new method incluldes the hooks that will also be used by the Library
	 * to initialize all functionality in other plugins as well.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function inspirations() {
		if ( class_exists( 'Boldgrid_Inspirations_Inspiration' ) ) {
			add_action( 'init', function() {
				Util\Filter::remove( 'init', 'Boldgrid_Inspirations_Inspiration', 'pre_add_hooks' );
			}, 0 );
			$inspirations = new Inspirations;
			add_action( 'init', array( $inspirations, 'pre_add_hooks' ), 15 );
			add_action( 'pre_set_site_transient_boldgrid_available', function( $value, $transient ) {
				if ( ! empty( $value ) ) {
					$value = Configs::get( 'api' );
				}
				return $value;
			}, 10, 2);
		}

	}
}
