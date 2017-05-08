<?php
/**
 * BoldGrid Library Version Utility
 *
 * @package Boldgrid\Library
 * @subpackage \Util
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Util;

/**
 * BoldGrid Library Version Utility Class.
 *
 * This class is responsible for determining current version installed of a
 * dependency. This will check the installed composer packages for the version
 * that was used for a particular dependency.
 *
 * @since 1.0.0
 */
class Version {

	/**
	 * @access private
	 *
	 * @since 1.0.0
	 *
	 * @var string $dependency The dependency to get the normalized version of.
	 * @var mixed  $version    Normalized version number if found, or null.
	 */
	private
		$dependency,
		$version;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dependency The dependency to check the version data of.
	 */
	public function __construct( $dependency ) {
		$this->dependency = $dependency;
		$this->version = $this->setVersion();
	}

	/**
	 * Sets the version of dependency.
	 *
	 * @since  1.0.0
	 *
	 * @global $wp_filesystem The WordPress Filesystem API object.
	 *
	 * @return mixed $version Normalized version number if found or null.
	 */
	public function setVersion() {
		global $wp_filesystem;

		// Ensure that the WP Filesystem API is loaded.
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		// Get installed composer package data.
		$vendor = wp_normalize_path( realpath( __DIR__ . '/../../../../' ) );
		$file = $wp_filesystem->get_contents(  $vendor . '/composer/installed.json' );
		$installed = json_decode( $file, true );

		// Check for dep's installed version.
		$version = null;
		if ( $installed ) {
			foreach( $installed as $key => $value ) {
				if ( $value['name'] === $this->getDependency() ) {
					$version = $value['version_normalized'];
					break;
				}
			}
		}

		return $version;
	}

	/**
	 * Gets the dependency class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string Name of the dependency.
	 */
	public function getDependency() {
		return $this->dependency;
	}

	/**
	 * Gets the version class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string Normalized version of dependency.
	 */
	public function getVersion() {
		return $this->version;
	}
}
