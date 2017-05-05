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

/**
 * BoldGrid Library Configs Class.
 *
 * This class is responsible for setting and getting configuration
 * options that are set for the library.
 *
 * @since 1.0.0
 */
class Configs {

	/**
	 * @access private
	 *
	 * @var array $configs Configuration options.
	 */
	private static $configs;


	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configs Plugin configuration array.
	 */
	public function __construct( $configs = null ) {
		$defaults = include_once dirname( __DIR__ ) . '/library.config.php';
		self::set( $configs, $defaults );
	}

	/**
	 * [set description]
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $configs  [description]
	 * @param [type] $defaults [description]
	 *
	 * @return object $this self.
	 */
	public static function set( $configs, $defaults = null ) {
		$defaults = $defaults ? $defaults : self::get();

		return self::$configs = wp_parse_args( $configs, $defaults );
	}

	/**
	 * Get configs or config by key.
	 *
	 * @since 1.0.0
	 *
	 * @param  [type] $key [description]
	 *
	 * @return [type]      [description]
	 */
	public static function get( $key = null ) {
		$configs = self::$configs;
		if ( $key ) {
			$configs = ! empty( self::$configs[ $key ] ) ? self::$configs[ $key ] : null;
		} else {
			$configs = self::$configs;
		}

		return $configs;
	}
}
