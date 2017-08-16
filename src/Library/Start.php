<?php
/**
 * BoldGrid Library Start.
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Start Class.
 *
 * This class is responsible for setting up the BoldGrid Library.
 *
 * @since 1.0.0
 */
class Start {

	/**
	 * @var object $configs         Library Configuration Object.
	 * @var object $releaseChannel  Library ReleaseChannel Object.
	 * @var object $pluginInstaller Library Plugin Installer Object.
	 * @var object $key             Library Key Object.
	 */
	private
		$configs,
		$releaseChannel,
		$pluginInstaller,
		$key;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $configs Plugin configuration array.
	 */
	public function __construct( $configs = null ) {
		$this->configs = new Configs( $configs );
		if ( Configs::get( 'keyValidate' ) ) {
			$this->key = new Key();
		}
		$this->releaseChannel = new ReleaseChannel;
		$this->pluginInstaller = new Plugin\Installer( Configs::get( 'pluginInstaller' ), $this->getReleaseChannel() );

		if ( is_admin() ) {
			/** This action is documented in this file, in method checkPluginsUpdated() */
			add_action( 'boldgrid_plugins_updated',
				array( 'Boldgrid\Library\Util\Option', 'deletePluginTransients' ), 10, 0
			);

			$this->checkPluginsUpdated();
		}
	}

	/**
	 * Get releaseChannel class property.
	 *
	 * @since 1.0.0
	 *
	 * @return object $releaseChannel Library\ReleaseChannel object.
	 */
	public function getReleaseChannel() {
		return $this->releaseChannel;
	}

	/**
	 * Check for post-update actions for BoldGrid plugins.
	 *
	 * If there is a new BoldGrid plugin or version, then clear the plugin transients.
	 *
	 * @since 1.1.4
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_plugins/
	 * @see get_plugins()
	 */
	public function checkPluginsUpdated() {
		$purge = false;

		$boldgridSettings = get_site_option( 'boldgrid_settings' );

		foreach ( get_plugins() as $slug => $data ) {
			if ( false !== strpos( $slug, 'boldgrid-' ) ) {
				if ( empty( $boldgridSettings['plugins_checked'][ $slug ][ $data['Version'] ] ) ) {
					$purge = true;

					/**
					 * Action triggered when a BoldGrid plugin is found to be updated or new.
					 *
					 * @since 1.1.4
					 *
					 * @param string $tag  Tag name.
					 * @param string $slug Plugin slug (folder/file).
					 * @param array $data {
					 *     Plugin data from the get_plugins() call.
					 *
					 *     @type string $Name        Plugin name.
					 *     @type string $PluginURI   Plugin URI.
					 *     @type string $Version     Version number/string.
					 *     @type string $Description Description.
					 *     @type string $Author      Author name.
					 *     @type string $AuthorURI   Author URI.
					 *     @type string $TextDomain  Text domain.
					 *     @type string $DomainPath  Domain path.
					 *     @type string $Network     Network.
					 *     @type string $Title       Plugin title.
					 *     @type string $AuthorName  Author name.
					 * }
					 */
					do_action( 'plugin_updated_' . $slug, $slug, $data );
				}

				$boldgridSettings['plugins_checked'][ $slug ][ $data['Version'] ] = time();
			}
		}

		if ( $purge ) {
			/**
			 * When one or more BoldGrid plugins are updated or new, then delete plugin transients.
			 *
			 * @since 1.1.4
			 */
			do_action( 'boldgrid_plugins_updated' );
		}

		update_site_option( 'boldgrid_settings', $boldgridSettings );
	}
}
