<?php
/**
 * BoldGrid Library Configs IMH Central Class
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
	 * @access private
	 *
	 * @var bool $connect_plugin_active If Connect Plugin is active.
	 */
	private $connect_plugin_active = false;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! get_option( 'bg_connect_configs' ) ) {
			return;
		}

		if ( is_plugin_active( 'boldgrid-connect/boldgrid-connect.php' ) ) {
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
		$option_configs = get_option( 'bg_connect_configs', [] );

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
		 * Filters the bg_connect_configs option to merge option with static configs provided.
		 *
		 * @param mixed       $value   Value of the option.
		 * @param string      $option  Option name.
		 * @return false|mixed (Maybe) filtered option value.
		 */
		add_filter(
			'option_bg_connect_configs',
			function( $value, $option ) {
				$value = is_array( $value ) ? $value : array();
				$conf  = \Boldgrid_Connect_Service::get( 'configs' );
				$confs = array_merge( $conf, $value );

				// Brand being passed in from install loop through and update option with correct provider.
				if ( ! empty( $confs['brand'] ) ) {
					foreach ( $confs['branding'] as $brand => $opts ) {
						if ( strpos( strtolower( $brand ), $confs['brand'] ) !== false ) {
							update_site_option( 'boldgrid_connect_provider', $brand );
						}
					}
				}

				return $confs;
			},
			10,
			2
		);
	}
}

