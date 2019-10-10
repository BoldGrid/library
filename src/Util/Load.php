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
	 * @var object $load {
	 *     An object containing the highest BoldGrid Library version found to load.
	 *
	 *     @type string $product The product in which the library was found.
	 *                           Example: "plugin/plugin.php".
	 *     @type string $version The library version found.
	 *                           Example: "2.8.0.0".
	 * }
	 * @var string $path      The path to the BoldGrid Library to load.
	 *                        Specifically, the path to the parent directory of the vendor folder.
	 *                        Example: /home/user/public_html/wp-content/plugins/plugin
	 */
	private
		$configs,
		$libraries,
		$load,
		$path;

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

		$this->filesystemFixes();

		// Add hooks for registration.
		$this->registration = new $class( $this->configs['file'] );

		// Gets the available Library versions that can be loaded.
		$this->libraries = Option::get( 'library' );

		// Sets the BoldGrid Library version to load.
		$this->setLoad( $this->getLibraries() );

		// Sets the path to the Library files to load for themes/plugins.
		$this->setPath();

		// Add loaded Library path to configs.
		$this->configs['libraryDir'] = trailingslashit( $this->getPath() ) . 'vendor/boldgrid/library/';

		// Add loaded Library URL to configs.
		$this->configs['libraryUrl'] = plugin_dir_url( $this->configs['libraryDir'] ) . 'library/';

		// Initialize BoldGrid Library.
		$this->load( $this->configs['loader'] );
	}

	/**
	 * Determine the path to a product.
	 *
	 * What is the "path"?
	 * The path to a product is the parent directory of the vendor folder. So, the path we return is
	 * assumed to have a vendor folder.
	 *
	 * What is the "product"?
	 * The product is the product identifier, such as "plugin/plugin.php".
	 *
	 * The contents of this method were originall in self::setPath(), but have been moved here for
	 * reusability.
	 *
	 * @since 2.7.3
	 *
	 * @param  string $product The product identifier.
	 *                         Example: plugin/plugin.php
	 * @return string
	 */
	public static function determinePath( $product ) {
		// Loading from must use plugin directory?
		if ( ! is_file( $path = trailingslashit( WPMU_PLUGIN_DIR ) . $product ) ) {

			// Loading from plugin directory?
			if ( ! is_file( $path = trailingslashit( WP_PLUGIN_DIR ) . $product ) ) {

				// Loading from a parent theme directory?
				$path = get_template_directory() . '/inc/boldgrid-theme-framework/includes/theme';
			}
		}

		// Loading from framework path override directory?
		if ( defined( 'BGTFW_PATH' ) ) {
			$dir = ABSPATH . trim( BGTFW_PATH, '/' ) . '/includes';
			if ( is_dir( $dir . '/vendor/boldgrid/library' ) ) {
				$path = $dir . '/theme';
			}
		}

		$path = dirname( $path );

		return $path;
	}

	/**
	 * Avoid fatal errors due to certain filesystem types.
	 *
	 * This fix is only to prevent fatal errors. It is up to the plugins including this library to
	 * test the filesystem and determine whether or not they're compatible.
	 *
	 * ftpext
	 * Fatal error: Call to undefined function wp_generate_password() in wp-admin/includes/file.php
	 *
	 * Including the pluggable.php file caused issues with some other plugins that override functions.
	 * Instead, we defined the wp_generate_password here.
	 *
	 * @todo Remove this method after an appropriate amount of time.  The requirement was removed on 10/10/2019.
	 */
	public function filesystemFixes() {
		// Copied from "wp-includes/pluggable.php".
		if ( ! function_exists( 'wp_rand' ) ) :
			/**
			 * Generates a random number.
			 *
			 * @since 2.6.2
			 * @since 4.4.0 Uses PHP7 random_int() or the random_compat library if available.
			 *
			 * @global string $rnd_value
			 * @staticvar string $seed
			 * @staticvar bool $use_random_int_functionality
			 *
			 * @param int $min Lower limit for the generated number
			 * @param int $max Upper limit for the generated number
			 * @return int A random number between min and max
			 */
			function wp_rand( $min = 0, $max = 0 ) {
				global $rnd_value;

				// Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
				$max_random_number = 3000000000 === 2147483647 ? (float) '4294967295' : 4294967295; // 4294967295 = 0xffffffff.

				// We only handle Ints, floats are truncated to their integer value.
				$min = (int) $min;
				$max = (int) $max;

				// Use PHP's CSPRNG, or a compatible method.
				static $use_random_int_functionality = true;
				if ( $use_random_int_functionality ) {
					try {
						$_max = ( 0 != $max ) ? $max : $max_random_number;
						// wp_rand() can accept arguments in either order, PHP cannot.
						$_max = max( $min, $_max );
						$_min = min( $min, $_max );
						$val  = random_int( $_min, $_max );
						if ( false !== $val ) {
							return absint( $val );
						} else {
							$use_random_int_functionality = false;
						}
					} catch ( Error $e ) {
						$use_random_int_functionality = false;
					} catch ( Exception $e ) {
						$use_random_int_functionality = false;
					}
				}

				/*
				 * Reset $rnd_value after 14 uses.
				 * 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value.
				 */
				if ( strlen( $rnd_value ) < 8 ) {
					if ( defined( 'WP_SETUP_CONFIG' ) ) {
						static $seed = '';
					} else {
						$seed = get_transient( 'random_seed' );
					}
					$rnd_value  = md5( uniqid( microtime() . mt_rand(), true ) . $seed );
					$rnd_value .= sha1( $rnd_value );
					$rnd_value .= sha1( $rnd_value . $seed );
					$seed       = md5( $seed . $rnd_value );
					if ( ! defined( 'WP_SETUP_CONFIG' ) && ! defined( 'WP_INSTALLING' ) ) {
						set_transient( 'random_seed', $seed );
					}
				}

				// Take the first 8 digits for our value.
				$value = substr( $rnd_value, 0, 8 );

				// Strip the first eight, leaving the remainder for the next call to wp_rand().
				$rnd_value = substr( $rnd_value, 8 );

				$value = abs( hexdec( $value ) );

				// Reduce the value to be within the min - max range.
				if ( $max != 0 ) {
					$value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );
				}

				return abs( intval( $value ) );
			}
		endif;

		if ( ! function_exists( 'wp_generate_password' ) ) :
			/**
			 * Generates a random password drawn from the defined set of characters.
			 *
			 * Uses wp_rand() is used to create passwords with far less predictability
			 * than similar native PHP functions like `rand()` or `mt_rand()`.
			 *
			 * @since 2.5.0
			 *
			 * @param int  $length              Optional. The length of password to generate. Default 12.
			 * @param bool $special_chars       Optional. Whether to include standard special characters.
			 *                                  Default true.
			 * @param bool $extra_special_chars Optional. Whether to include other special characters.
			 *                                  Used when generating secret keys and salts. Default false.
			 * @return string The random password.
			 */
			function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
				$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				if ( $special_chars ) {
					$chars .= '!@#$%^&*()';
				}
				if ( $extra_special_chars ) {
					$chars .= '-_ []{}<>~`+=,.;:/?|';
				}

				$password = '';
				for ( $i = 0; $i < $length; $i++ ) {
					$password .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
				}

				/**
				 * Filters the randomly-generated password.
				 *
				 * @since 3.0.0
				 *
				 * @param string $password The generated password.
				 */
				return apply_filters( 'random_password', $password );
			}
		endif;
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
	 * @param  array  $libraries {
	 *     An array of available library versions.
	 *
	 *     The array keys represent a product, and the values represent the library version.
	 *     Example: [plugin/plugin.php] => 2.8.0.0
	 * }
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
	 * @since  1.0.0
	 *
	 * @return string $path Path to the library to load.
	 */
	public function setPath() {
		$found = $this->getLoad();
		$path = false;

		if ( ! empty( $found->product ) ) {
			$path = self::determinePath( $found->product );
		}

		return $this->path = $path;
	}

	/**
	 * Adds the Library paths to the autoloader.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $loader Autoloader instance.
	 *
	 * @return null
	 */
	public function load( $loader ) {
		if ( ! empty( $this->configs['libraryDir'] ) ) {
			$library = $this->configs['libraryDir'] . 'src/Library';

			// Only create a single instance of the BoldGrid Library Start.
			if ( did_action( 'Boldgrid\Library\Library\Start' ) === 0 ) {
				do_action( 'Boldgrid\Library\Library\Start', $library );

				// Check dir and add PSR-4 dir of the BoldGrid Library to autoload.
				if ( is_dir( $library ) ) {
					$loader->addPsr4( 'Boldgrid\\Library\\Library\\', $library );
					$load = new \Boldgrid\Library\Library\Start( $this->configs );
					$load->init();
				}
			}
		}
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
