<?php
/**
 * BoldGrid Library Registration Utility
 *
 * @package Boldgrid\Library
 * @subpackage \Util\Registration
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Util\Registration;

use Boldgrid\Library\Util;

/**
 * BoldGrid Library Abstract Registration Class.
 *
 * This class is responsible for handling the registration of
 * a product and it's associated dependency version.
 *
 * @since 1.0.0
 */
abstract class AbstractRegistration implements RegistrationInterface {

	/**
	 * @access protected
	 *
	 * @var string $product    The product identifier.
	 * @var string $dependency The dependency to get the version of.
	 */
	protected
		$product,
		$dependency;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $product The path of the product.
	 */
	abstract public function __construct( $product, $dependency );

	/**
	 * Register the product in WordPress options.
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	public function register() {
		// Check the dependency version.
		$version = new Util\Version( $this->getDependency() );
		Util\Option::set( $this->getProduct(), $version->getVersion() );
	}

	/**
	 * Deregister the product in WordPress options.
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	public function deregister() {
		Util\Option::delete( $this->getProduct() );
	}

	/**
	 * Get the product class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string $product The product class property.
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * Get the dependency class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string $dependency The dependency class property.
	 */
	public function getDependency() {
		return $this->dependency;
	}
}
