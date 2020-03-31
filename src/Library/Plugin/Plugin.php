<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Plugin Plugin.
 *
 * Library package uses different naming convention.
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
 * @package Boldgrid\Plugin
 *
 * @since 2.7.7
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library\Configs;
use Boldgrid\Library\Library\Settings;
use Boldgrid\Library\Library\Plugin\Page;

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
	protected $childPlugins = array();

	/**
	 * Plugin file.
	 *
	 * For example: plugin/plugin.php.
	 *
	 * @since 2.9.0
	 * @var string
	 * @access protected
	 */
	protected $file;

	/**
	 * Plugin specific config array.
	 *
	 * @since 2.12.0
	 *
	 * @var array
	 */
	public $pluginConfig = array();

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
	 * @var string
	 */
	protected $path;

	/**
	 * Plugin pages.
	 *
	 * This is an array of Boldgrid\Library\Library\Plugin\Page.
	 * objects based on the 'pages' list in the plugin Config.
	 * If no plugin config is passed during instantiation,
	 * this will be an empty array.
	 *
	 * @since 2.12.0
	 * @var array
	 * @access protected
	 */
	protected $pages = array();

	/**
	 * Plugin data, as retrieved from get_plugin_data().
	 *
	 * @since 2.9.0
	 * @var array
	 * @access protected
	 */
	public $pluginData = array();

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
	 * @since 2.7.7
	 *
	 * @var string
	 * @access protected
	 */
	protected $slug;

	/**
	 * Plugin Update Data.
	 *
	 * @since SINCEVERSION
	 *
	 * @var Response
	 */
	public $updateData;

	/**
	 * Constructor.
	 *
	 * @since 2.7.7
	 *
	 * @param string $pluginName This can be either a slug or plugin file depending on how this was called.
	 * @param array  $pluginConfig An array of plugin config data.
	 */
	public function __construct( $pluginName, $pluginConfig = null ) {

		$this->determineSlug( $pluginName );

		$this->determineFile( $pluginName );

		$this->setPath();

		$this->setIsInstalled();

		$this->setChildPlugins();

		$this->pluginConfig = ! empty( $pluginConfig ) ? $pluginConfig : array();

		if ( $this->getSlug() ) {
			$this->updateData = new UpdateData( $this );
		}

		$this->setPages();
	}

	/**
	 * Determine the Slug of this plugin.
	 *
	 * Depending on how this class constructor is called.
	 * It may be passed a file such as 'plugin/plugin.php'.
	 * Or it may be passed the plugin slug such as 'plugin'.
	 * This method determines which it was, and then derives the correct.
	 * property.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $pluginName The string passed to constructor.
	 */
	public function determineSlug( $pluginName ) {
		if ( false === strpos( $pluginName, '.' ) ) {
			// If the $pluginName does not contain a '.' then this is a slug, not a file.
			$this->slug = $pluginName;
		} else {
			// If the $pluginName does contain a '.' then it is a file, so the slug must be derived from that.
			$this->slugFromFile( $pluginName );
		}
	}

	/**
	 * Determine the File of this plugin.
	 *
	 * Depending on how this class constructor is called.
	 * It may be passed a file such as 'plugin/plugin.php'.
	 * Or it may be passed the plugin slug such as 'plugin'.
	 * This method determines which it was, and then derives the correct.
	 * property.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $pluginName The string passed to constructor.
	 */
	public function determineFile( $pluginName ) {
		if ( false === strpos( $pluginName, '.' ) ) {
			// If the $pluginName does not contain a '.' then this is a slug, not a file.
			$this->fileFromSlug( $pluginName );
		} else {
			// If the $pluginName does contain a '.' then it is a file, so the slug must be derived from that.
			$this->file = $pluginName;
		}
	}

	/**
	 * Gets the plugin's slug from the file name passed in construction.
	 *
	 * If a filename was passed, not a slug, then this will find the slug for us.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $file Filename passed in construction.
	 */
	public function slugFromFile( $file ) {
		if ( false !== strpos( $file, '/' ) ) {
			// If the filename has a '/' in it, the slug should be the first part of the string.
			$this->slug = explode( '/', $file )[0];
		} else {
			/*
			 * If the filename does not have a '/' then the slug will ahve to be pulled from the plugin's
			 * file contents. This is because the plugins with just a name, such as hello.php, do not
			 * always match their slug.
			 */
			$file_contents = file_get_contents( WP_PLUGIN_DIR . '/' . $file );
			$lines         = explode( "\n", $file_contents );
			foreach ( $lines as $line ) {
				if ( false !== strpos( $line, '@package' ) ) {
					$package    = strtolower( explode( ' ', $line )[3] );
					$this->slug = str_replace( '_', '-', $package );
				}
			}
		}
	}

	/**
	 * Gets the plugin's file from the slug passed in construction.
	 *
	 * If a slug was passed, not a file, then this will find the file for us.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $slug Slug passed in construction.
	 */
	public function fileFromSlug( $slug ) {
		if ( file_exists( WP_PLUGIN_DIR . '/' . $slug . '/' . $slug . '.php' ) ) {
			$this->file = $slug . '/' . $slug . '.php';
		} elseif ( file_exists( WP_PLUGIN_DIR . '/' . $slug . '.php' ) ) {
			$this->file = $slug . '.php';
		}
	}

	/**
	 * Version compare for the first version of this plugin.
	 *
	 * @since 2.11.0
	 *
	 * @param  string $version2 The second version number.
	 * @param  string $operator The relationship to test for.
	 * @return bool
	 */
	public function firstVersionCompare( $version2, $operator ) {
		// The first version of this plugin.
		$firstVersion = $this->getFirstVersion();

		return version_compare( $firstVersion, $version2, $operator );
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
	 * Within library.global.php, there is a config option called "plugins". This method loops through.
	 * those plugins and returns the configs for this particular plugin.
	 *
	 * @since 2.10.0
	 *
	 * @return array
	 */
	public function getConfig() {
		$config = array();

		$plugins = Configs::get( 'plugins' );
		if ( $plugins ) {
			foreach ( $plugins as $plugin ) {
				if ( $plugin['file'] === $this->file ) {
					$config = $plugin;
				}
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
	 * Get plugin specific config array.
	 *
	 * @return array
	 * @since 2.12.0
	 */
	public function getPluginConfig() {
		return $this->pluginConfig;
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
	 * Get the plugins_checked data for this plugin.
	 *
	 * @since 2.11.0
	 *
	 * @return array
	 */
	public function getPluginsChecked() {
		$pluginsChecked = Settings::getKey( 'plugins_checked', array() );

		$pluginsChecked = ! empty( $pluginsChecked[ $this->file ] ) ? $pluginsChecked[ $this->file ] : array();

		return $pluginsChecked;
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
	 * Create page objects based on $this->config parameter.
	 *
	 * @since 2.12.0
	 * @access private
	 */
	public function setPages() {
		$pages = array();
		if ( isset( $this->pluginConfig['pages'] ) ) {
			foreach ( $this->pluginConfig['pages'] as $page ) {
				$pages[] = new Page( $this, $page );
			}
		}
		$this->pages = $pages;
	}

	/**
	 * Set all plugin notices as read.
	 *
	 * @since 2.12.0
	 *
	 * @param bool $setToUnread if set to true, marks all items unread instead.
	 */
	public function setAllNoticesRead( $setToUnread = false ) {
		foreach ( $this->getPages() as $page ) {
			$page->setAllNoticesRead( $setToUnread );
		}
	}

	/**
	 * Get Plugin Pages.
	 *
	 * @since 2.12.0
	 *
	 * @return array
	 * @access public
	 */
	public function getPages() {
		return $this->pages;
	}

	/**
	 * Get Page by Slug.
	 *
	 * @since 2.12.0
	 *
	 * @param string $slug The Plugin slug.
	 * @return Page
	 */
	public function getPageBySlug( $slug ) {
		foreach ( $this->getPages() as $page ) {
			if ( $page->getSlug() === $slug ) {
				return $page;
			}
		}
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
			$slug = $this->slugFromFile( $file );

			$this->childPlugins[] = new Plugin( $slug );
		}
	}

	/**
	 * Set whether or not the plugin is installed (different from activated).
	 *
	 * @since 2.10.0
	 */
	public function setIsInstalled() {
		$wp_filesystem     = \Boldgrid\Library\Util\Version::getWpFilesystem();
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

	/**
	 * Get the first version of this plugin installed.
	 *
	 * @since 2.11.0
	 *
	 * @return string
	 */
	public function getFirstVersion() {
		$pluginsChecked = $this->getPluginsChecked();

		if ( ! empty( $pluginsChecked ) ) {
			/*
			 * Get the first version of the plugin, which is the first key from plugins_checked.
			 *
			 * Ideal method is to use array_key_first, but that requires php 7+. Alternative is to use
			 * this polyfill.
			 *
			 * @link https://www.php.net/manual/en/function.array-key-first.php
			 */
			foreach ( $pluginsChecked as $key => $unused ) {
				$firstVersion = $key;
				break;
			}
		} else {
			// If the data is missing for some reason, return the plugin's current version.
			$firstVersion = $this->getData( 'Version' );
		}
		return $firstVersion;
	}

	/**
	 * Get Unread Count.
	 *
	 * Get unread count integer.
	 *
	 * @since 2.12.0
	 *
	 * @return int
	 */
	public function getUnreadCount() {
		$unreadCount = 0;
		foreach ( $this->getPages() as $page ) {
			$unreadCount += $page->getUnreadCount();
		}
		return $unreadCount;
	}

	/**
	 * Get Unread markup.
	 *
	 * Get unread count with html markup.
	 *
	 * @since 2.12.0
	 *
	 * @return string
	 */
	public function getUnreadMarkup() {
		$count = $this->getUnreadCount();
		if ( $count > 0 ) {
			return '<span class="bglib-unread-notice-count">' . esc_html( $count ) . '</span>';
		} else {
			return '<span class="bglib-unread-notice-count hidden"></span>';
		}
	}
}
