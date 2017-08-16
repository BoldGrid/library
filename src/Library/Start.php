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
			$this->checkPostUpdates();
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
	 */
	public function checkPostUpdates() {
		$purge = false;

		$boldgridSettings = get_site_option( 'boldgrid_settings' );

		foreach ( get_plugins() as $slug => $data ) {
			if ( false !== strpos( $slug, 'boldgrid-' ) ) {
				if ( empty( $boldgridSettings['plugins_checked'][ $slug ][ $data['Version'] ] ) ) {
					$purge = true;
				}

				$boldgridSettings['plugins_checked'][ $slug ][ $data['Version'] ] = time();
			}
		}

		if ( $purge ) {
			Util\Option::deletePluginTransients();
		}

		update_site_option( 'boldgrid_settings', $boldgridSettings );
	}
}
