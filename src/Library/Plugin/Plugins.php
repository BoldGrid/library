<?php
/**
 * BoldGrid Library Plugin Plugins.
 *
 * @package Boldgrid\Plugin
 *
 * @since 2.10.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library\Configs;

/**
 * Generic plugins class.
 *
 * Unlike the Plugin class, Plugins is a generic class for "plugins" related methods.
 *
 * @since 2.10.0
 */
class Plugins {
	/**
	 * Get our active plugins.
	 *
	 * @since 2.10.0
	 *
	 * @return array
	 */
	public static function getActive() {
		// Get an array of plugins that should be within the widget.
		$plugins = Configs::getPlugins( array(
			'inNotificationsWidget' => true,
		) );

		// Then filter that array of plugins to get our active plugins.
		$activePlugins = array();
		foreach ( $plugins as $plugin ) {
			if ( $plugin->isActive() ) {
				$activePlugins[] = $plugin;
			}
		}
		unset( $plugins );

		return $activePlugins;
	}

	public static function getAllActivePlugins() {
		$active_plugins_list = get_option('active_plugins');

		$active_plugins = [];
		foreach( $active_plugins_list as $active_plugin ) {
			$active_plugins[] = new Plugin( null, null, $active_plugin );
		}

		return $active_plugins;
	}

	public static function getActivePluginBySlug( $active_plugins, $slug ) {
		foreach ( $active_plugins as $plugin ) {
			if ( $plugin->getSlug() === $slug ) {
				return $plugin;
			}
		}
	}
}
