<?php
/**
 * BoldGrid Library Configs IMH Central Class
 *
 * This class is used to add filters for IMH Central users
 * in the event that the Boldgrid Connect plugin is not active to
 * run the filters itself.
 *
 * @package Boldgrid\Library
 * @subpackage \Library\Configs
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Configs;

class IMH_Central {
	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! get_option( 'bg_connect_configs' ) ) {
			return;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( \is_plugin_active( 'boldgrid-connect/boldgrid-connect.php' ) ) {
			return;
		}

		self::imh_central_filters();
	}

	/**
	 * Add filters to the IMH Central plugin.
	 *
	 * @since 1.0.0
	 */
	public static function imh_central_filters() {
		$option_configs = get_option( 'bg_connect_configs' );

		add_filter(
			'BoldgridDemo/configs',
			function( $configs ) use ( $option_configs ) {
				$configs['servers']['asset'] = ! empty( $option_configs['asset_server'] ) ? $option_configs['asset_server'] : $configs['servers']['asset'];
				return $configs;
			}
		);

		// Inspirations
		add_filter(
			'boldgrid_inspirations_configs',
			function ( $configs ) use ( $option_configs ) {
				$configs['asset_server'] = ! empty( $option_configs['asset_server'] ) ? $option_configs['asset_server'] : $configs['servers']['asset'];
				return $configs;
			}
		);

		// BoldGrid Library
		add_filter(
			'Boldgrid\Library\Configs\set',
			function( $configs ) use ( $option_configs ) {
				$configs['api'] = ! empty( $option_configs['asset_server'] ) ? $option_configs['asset_server'] : $configs['api'];
				return $configs;
			}
		);

		// BoldGrid Connect Plugin.
		add_filter(
			'boldgrid_connect_config_setup_configs',
			function( $configs ) use ( $option_configs ) {
				$configs['asset_server'] = ! empty( $option_configs['asset_server'] ) ? $option_configs['asset_server'] : $configs['asset_server'];
				$configs['central_url']  = ! empty( $option_configs['central_url'] ) ? $option_configs['central_url'] : $configs['central_url'];
				return $configs;
			}
		);

		/**
		 * Each of these filters represents a different premium url that can be overridden.
		 *
		 * To override these urls using the 'bg_connect_configs' option, add the url to the option
		 * array using the filter name as the key. For example, to override the boldgrid_editor_premium_url,
		 * we would set the following to the bg_connect_configs option:
		 *     $options_configs[ 'boldgrid_editor_premium_url' ] = 'https://example.com/';
		 */
		$premium_url_filters = array(
			'boldgrid_editor_premium_url',
			'boldgrid_editor_new_key_url',
			'boldgrid_editor_premium_download_url',
			'boldgrid_backup_premium_url',
			'bgtfw_premium_url',
			'boldgrid_library_new_key_url',
			'bgtfw_upgrade_url_pro_features',
		);

		foreach ( $premium_url_filters as $premium_url_filter ) {
			add_filter(
				$premium_url_filter,
				function( $url ) use ( $option_configs, $premium_url_filter ) {
					return ! empty( $option_configs[ $premium_url_filter ] ) ? $option_configs[ $premium_url_filter ] : $url;
				}
			);
		}
	}
}

