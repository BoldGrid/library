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
		$plugins = Configs::getPlugins(
			array(
				'inNotificationsWidget' => true,
			)
		);

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

	/**
	 * Get All Active Plugins.
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public static function getAllActivePlugins() {
		$active_plugins_list = get_option( 'active_plugins' );
		$active_plugins      = array();
		foreach ( $active_plugins_list as $active_plugin ) {
			$active_plugins[] = new Plugin( $active_plugin, null );
		}
		return $active_plugins;
	}

	/**
	 * Get All Plugins.
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public static function getAllPlugins() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins     = get_plugins();
		$all_plugins = array();
		foreach ( $plugins as $file => $plugin_data ) {
			$all_plugins[] = new Plugin( plugin_basename( $file ), null, $file );
		}
		return $all_plugins;
	}

	/**
	 * Get Active Plugin by Slug
	 *
	 * @since SINCEVERSION
	 *
	 * @param array $active_plugins. List of Plugin objects.
	 * @param string $slug.
	 *
	 * @return Plugin
	 */
	public static function getActivePluginBySlug( array $active_plugins, $slug ) {
		$active_plugins = self::getAllPlugins();
		foreach ( $active_plugins as $plugin ) {
			if ( $plugin->getSlug() === $slug ) {
				return $plugin;
			}
		}
		return new \WP_Error( 'boldgrid_plugin_not_found', sprintf( 'No plugin could be found with the slug %s.', $slug ) );
	}
}
