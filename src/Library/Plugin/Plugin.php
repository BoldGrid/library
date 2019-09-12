<?php
/**
 * BoldGrid Library Plugin Plugin.
 *
 * @package Boldgrid\Plugin
 *
 * @since 2.7.7
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library\Configs;

/**
 * Generic plugin class.
 *
 * This class represents a specific plugin.
 *
 * @since 2.7.7
 */
class Plugin {
	/**
	 * An array of children plugins.
	 *
	 * Elements of this array are of this class, Plugin (recursion).
	 *
	 * @since 2.10.0
	 * @var array
	 * @access protected
	 */
	protected $childPlugins = [];

	/**
	 * Plugin file.
	 *
	 * For example: plugin/plugin.php
	 *
	 * @since 2.9.0
	 * @var string
	 * @access protected
	 */
	protected $file;

	/**
	 * Whether or not this plugin is installed.
	 *
	 * @since 2.10.0
	 * @var bool
	 * @access protected
	 */
	protected $isInstalled;

	/**
	 * Path to the plugin.
	 *
	 * For example, ABSPATH/wp-content/plugins/plugin/plugin.php.
	 *
	 * @since 2.10.0
	 */
	protected $path;

	/**
	 * Plugin data, as retrieved from get_plugin_data().
	 *
	 * @since 2.9.0
	 * @var array
	 * @access protected
	 */
	protected $pluginData = [];

	/**
	 * Update plugin data, as retrieved from 'update_plugins' site transient.
	 *
	 * @since 2.9.0
	 * @var array
	 * @access protected
	 */
	protected $updatePlugins;

	/**
	 * Plugin slug.
	 *
	 * Examples: boldgrid-backup, boldgrid-backup-premium, etc.
	 *
	 * @var string
	 * @since 2.7.7
	 * @access protected
	 */
	protected $slug;

	/**
	 * Constructor.
	 *
	 * @since 2.7.7
	 *
	 * @param string $slug For example: "plugin" from plugin/plugin.php
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;

		$this->setFile();

		$this->setPath();

		$this->setIsInstalled();

		$this->setChildPlugins();
	}

	/**
	 * Get the url to activate this plugin.
	 *
	 * @since 2.10.2
	 *
	 * @return string
	 */
	public function getActivateUrl() {
		return wp_nonce_url(
			self_admin_url( 'plugins.php?action=activate&plugin=' . $this->file ),
			'activate-plugin_' . $this->file
		);
	}

	/**
	 * Get an array of the plugin's icons.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function getIcons() {
		$updates = $this->getUpdatePlugins();

		$icons = array();
		$icons = ! empty( $updates->response[ $this->file ]->icons ) ? $updates->response[ $this->file ]->icons : $icons;
		$icons = ! empty( $updates->no_update[ $this->file ]->icons ) ? $updates->no_update[ $this->file ]->icons : $icons;

		return $icons;
	}

	/**
	 * Get the child plugins array.
	 *
	 * @since 2.10.0
	 *
	 * @return array
	 */
	public function getChildPlugins() {
		return $this->childPlugins;
	}

	/**
	 * Get config for this particular plugin.
	 *
	 * Within library.global.php, there is a config option called "plugins". This method loops through
	 * those plugins and returns the configs for this particular plugin.
	 *
	 * @since 2.10.0
	 *
	 * @return array
	 */
	public function getConfig() {
		$config = [];

		$plugins = Configs::get( 'plugins' );

		foreach ( $plugins as $plugin ) {
			if ( $plugin['file'] === $this->file ) {
				$config = $plugin;
			}
		}

		return $config;
	}

	/**
	 * Get data via the get_plugin_data() function.
	 *
	 * If a $key is passed in, return that key, otherwise return all data.
	 *
	 * @since 2.9.0
	 *
	 * @param string $key The data key to return.
	 * @return mixed
	 */
	public function getData( $key = null ) {
		$data = $this->getPluginData();

		return empty( $key ) ? $data : $data[ $key ];
	}

	/**
	 * Return the download url for a plugin.
	 *
	 * @since 2.7.7
	 *
	 * @todo This method only works for plugins hosted at the BoldGrid api server.
	 *
	 * @return string
	 */
	public function getDownloadUrl() {
		$url = Configs::get( 'api' ) . '/v1/plugins/' . $this->slug . '/download';

		$url = add_query_arg( 'key', Configs::get( 'key' ), $url );

		return $url;
	}

	/**
	 * Get the url to install this plugin.
	 *
	 * @since 2.10.2
	 *
	 * @return string
	 */
	public function getInstallUrl() {
		return wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=' . $this->slug ),
			'install-plugin_' . $this->slug
		);
	}

	/**
	 * Get isInstalled.
	 *
	 * @since 2.10.0
	 *
	 * @return bool
	 */
	public function getIsInstalled() {
		return $this->isInstalled;
	}

	/**
	 * Get file.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Get a plugin's slug from its file.
	 *
	 * For example, if the plugin's file is:
	 * boldgrid-backup-premium/boldgrid-backup-premium.php
	 * The plugin's slug is:
	 * boldgrid-backup-premium
	 *
	 * @since 2.10.0
	 *
	 * @param  string $file The plugin's file, as in plugin/plugin.php
	 * @return string
	 */
	public static function getFileSlug( $file ) {
		$slug = explode( '/', $file );

		return $slug[0];
	}

	/**
	 * If a new version of a plugin is available, return the new version.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function getNewVersion() {
		$updates = $this->getUpdatePlugins();

		return ! empty( $updates->response[ $this->file ]->new_version ) ? $updates->response[ $this->file ]->new_version : '0';
	}

	/**
	 * Get plugin data.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function getPluginData() {
		if ( empty( $this->pluginData ) && $this->isInstalled ) {
			$this->pluginData = get_plugin_data( $this->path );
		}

		return $this->pluginData;
	}

	/**
	 * Get slug.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * Set our child plugins.
	 *
	 * @since 2.10.0
	 */
	public function setChildPlugins() {
		$config = $this->getConfig();

		if ( empty( $config['childPlugins'] ) ) {
			return;
		}

		foreach ( $config['childPlugins'] as $file ) {
			$slug = $this->getFileSlug( $file );

			$this->childPlugins[] = new Plugin( $slug );
		}
	}

	/**
	 * Set file.
	 *
	 * @since 2.9.0
	 *
	 * @param string $file A plugin's file.
	 */
	public function setFile( $file = null ) {
		$this->file = ! empty( $file ) ? $file : $this->slug . '/' . $this->slug . '.php';
	}

	/**
	 * Set whether or not the plugin is installed (different from activated).
	 *
	 * @since 2.10.0
	 */
	public function setIsInstalled() {
		$wp_filesystem = \Boldgrid\Library\Util\Version::getWpFilesystem();

		$this->isInstalled = $wp_filesystem->exists( $this->path );
	}

	/**
	 * Set the plugin's path.
	 *
	 * @since 2.10.0
	 */
	public function setPath() {
		$this->path = ABSPATH . 'wp-content/plugins/' . $this->file;
	}

	/**
	 * Get updatePlugins data.
	 *
	 * @since 2.9.0
	 *
	 * @return object
	 */
	public function getUpdatePlugins() {
		if ( empty( $this->updatePlugins ) ) {
			$this->updatePlugins = get_site_transient( 'update_plugins' );
		}

		return $this->updatePlugins;
	}

	/**
	 * Whether or not a plugin has an update available.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function hasUpdate() {
		$updates = $this->getUpdatePlugins();

		return ! empty( $updates->response[ $this->file ] );
	}

	/**
	 * Whether or not this plugin is active.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function isActive() {
		return is_plugin_active( $this->file );
	}
}
