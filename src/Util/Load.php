<?php
/**
 * BoldGrid Library Load Utility
 *
 * @package Boldgrid\Library
 * @subpackage \Util
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Util;

/**
 * BoldGrid Library Load Utiliy Class.
 *
 * This class is responsible for determining the highest version of
 * available libraries to load and then loading it.
 *
 * @since 1.0.0
 */
class Load {

	/**
	 * @access private
	 *
	 * @since 1.0.0
	 *
	 * @var array  $configs   BoldGrid Library configurations.
	 * @var array  $libraries Available BoldGrid Library versions.
	 * @var object $load      Highest BoldGrid Library version found to load.
	 * @var string $path      The path to the BoldGrid Library to load.
	 */
	private
		$configs,
		$libraries,
		$load,
		$path;

	/**
	 * @access public
	 *
	 * @since 1.0.0
	 *
	 * @var bool $success Was library successfully loaded?
	 */
	public static $success;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configs BoldGrid Library configurations.
	 */
	public function __construct( array $configs = array() ) {

		// Set the configuration array.
		$this->configs = $configs;

		// Build the registration class.
		$class = __NAMESPACE__ . '\\Registration\\' . ucfirst( $this->configs['type'] );

		// Add hooks for registration.
		$this->registration = new $class( $this->configs['file'] );

		// Gets the available Library versions that can be loaded.
		$this->libraries = Option::get( 'library' );

		// Sets the BoldGrid Library version to load.
		$this->setLoad( $this->getLibraries() );

		// Sets the path to the Library files to load for themes/plugins.
		$this->setPath();

		// Initialize BoldGrid Library.
		$this->load( $this->configs['loader'] );
	}

	/**
	 * Sets the product and version of library to load.
	 *
	 * This check will loop through the available libraries and load the
	 * highest version found.  If an active plugin or theme contains a
	 * git branch instead of a tagged version, then that library will be
	 * loaded instead.
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $libraries Registered library versions from options.
	 *
	 * @return object $load      The product and version to load.
	 */
	public function setLoad( $libraries ) {
		$load = false;
		$product = false;
		foreach( $libraries as $name => $version ) {

			// Check for branch versions normalized (dev/master).
			if ( strpos( $version, 'dev' ) !== false ) {
				$load = $version;
				$product = $name;
				break;
			}

			// Check for highest loaded version.
			if ( version_compare( $load, $version ) === -1 ) {
				$load = $version;
				$product = $name;
			}
		}

		return $this->load = ( object ) array( 'product' => $product, 'version' => $load );
	}

	/**
	 * Sets the path class property.
	 *
	 * This will determine the path to the found product's library to load.
	 *
	 * @since  1.0.0
	 *
	 * @return string $path Path to the library to load.
	 */
	public function setPath() {
		$found = $this->getLoad();

		// Loading from a theme or a plugin?
		if ( strpos( get_stylesheet_directory(), $found->product ) !== false ) {
			$path = get_stylesheet_directory() . '/inc/boldgrid-theme-framework/theme';
		} else {
			if ( ! is_file( $path = trailingslashit( WPMU_PLUGIN_DIR ) . $found->product ) ) {
				if ( ! is_file( $path = trailingslashit( WP_PLUGIN_DIR ) . $found->product ) ) {
					$path = null;
				}
			}
		}

		return $this->path = dirname( $path );
	}

	/**
	 * Adds the Library paths to the autoloader.
	 *
	 * @since 1.0.0
	 *
	 * @param  object $loader Autoloader instance.
	 *
	 * @return bool           Has library been successfully loaded?
	 */
	public function load( $loader ) {
		if ( $this->getPath() ) {
			$library = $this->getPath() . '/vendor/boldgrid/library/src/Library';

			// Check dir and add PSR-4 dir of the BoldGrid Library to autoload.
			if ( is_dir( $library ) ) {
				$loader->addPsr4( 'Boldgrid\\Library\\Library\\', $library );
				$load = new \Boldgrid\Library\Library\Start( $this->configs );
				return self::$success = $load;
			}
		}

		return self::$success = false;
	}

	/**
	 * Get the libraries class property.
	 *
	 * @since  1.0.0
	 *
	 * @return array $libraries Available libraries.
	 */
	public function getLibraries() {
		return $this->libraries;
	}

	/**
	 * Get the load class property.
	 *
	 * @since  1.0.0
	 *
	 * @return object $load Highest library version found to load.
	 */
	public function getLoad() {
		return $this->load;
	}

	/**
	 * Get the path class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string $path path to the library to load.
	 */
	public function getPath() {
		return $this->path;
	}
}
