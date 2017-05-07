<?php
/**
 * BoldGrid Library Notice
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Notice Class.
 *
 * This class is responsible for adding any admin notices that are
 * displayed in the WordPress dashboard.
 *
 * @since 1.0.0
 */
class Notice {

	/**
	 * @access private
	 *
	 * @var string $name  Name of notice to create.
	 * @var array  $args  Optional arguments if required by class.
	 * @var mixed  $class Instantiated class object or add filters.
	 */
	private
		$name,
		$args,
		$class;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  Name of notice to create.
	 * @param array  $args  Optional arguments if required by class being instantiated.
	 */
	public function __construct( $name, $args = null ) {
		$this->name = $name;
		$this->args = $args;
		$this->setClass( $name, $args );
	}

	/**
	 * Sets the class class property.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $name Name of the notice class to initialize.
	 * @param  array  $args Optional arguments to pass to the class initialization.
	 *
	 * @return string $class The class class property.
	 */
	private function setClass( $name, $args ) {

		// Build the class string dynamically.
		$class = __NAMESPACE__ . '\\Notice\\' . ucfirst( $name );

		// Create a new instance or add our filters.
		return $this->class = class_exists( $class ) ? new $class( $args ) : Filter::add( $this );
	}

	/**
	 * Displays the license notice in the WordPress admin.
	 *
	 * @since 1.0.0
	 *
	 * @hook: admin_notices
	 */
	public function add( $name = null ) {
		$name = $name ? $name : $this->getName();
		$path = __DIR__;
		$name = ucfirst( $name );
		include  "{$path}/Views/{$name}.php";
	}

	/**
	 * Get the name class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string $name The name class property.
	 */
	protected function getName() {
		return $this->name;
	}

	/**
	 * Get the args class property.
	 *
	 * @since  1.0.0
	 *
	 * @return string $name The args class property.
	 */
	protected function getArgs() {
		return $this->args;
	}
}
