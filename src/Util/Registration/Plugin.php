<?php
/**
 * BoldGrid Library Plugin Registration Utility
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
 * BoldGrid Library Plugin Registration Class.
 *
 * This class is responsible for handling the registration of
 * a plugin and it's associated dependency version.
 *
 * @since 1.0.0
 */
class Plugin extends Util\Registration {

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $product The path of the product.
	 */
	public function __construct( $product ) {
		parent::init( $product );
		register_activation_hook( $this->getProduct(), array( $this, 'register' ) );
		register_deactivation_hook( $this->getProduct(), array( $this, 'deregister' ) );

		add_filter( 'upgrader_package_options', array( $this, 'preUpgrade' ) );
	}

	/**
	 * Take action before this plugin is upgraded.
	 *
	 * It's nice that WordPress has an "upgrader_process_complete" action you
	 * can hook into to take action after a plugin is upgraded. However, what
	 * about an action BEFORE the upgrade? Such an action does not exist.
	 *
	 * What does exist is the ability to filter our $upgrader options before the
	 * upgrade takes place. So, in order to do an action before the upgrade
	 * runs, we'll hook into the upgrader_package_options filter.
	 *
	 * @since 2.2.1
	 *
	 * @param  array $options See filter declaration in
	 *                        wp-admin/includes/class-wp-upgrader.php
	 * @return array
	 */
	public function preUpgrade( $options ) {
		$plugin = ! empty( $options['hook_extra']['plugin'] ) ? $options['hook_extra']['plugin'] : null;

		if( $plugin === $this->getProduct() ) {

			/*
			 * Before this plugin is upgraded, remove it from the list of
			 * registered libraries.
			 *
			 * We need to remove it because this plugin may be upgraded
			 * (or downgraded) to a version of the plugin where it does not
			 * contain the library.
			 *
			 * Reregistration of this plugin will take place in the constructor
			 * the next time this class is instantiated.
			 */
			$this->deregister();
		}

		return $options;
	}
}
