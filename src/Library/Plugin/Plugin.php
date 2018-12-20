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
	 * Plugin slug.
	 *
	 * Examples: boldgrid-backup, boldgrid-backup-premium, etc.
	 *
	 * @var  string
	 * @since 2.7.7
	 */
	private $slug;

	/**
	 * Constructor.
	 *
	 * @since 2.7.7
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Return the download url for a plugin.
	 *
	 * @since 2.7.7
	 *
	 * @return string
	 */
	public function getDownloadUrl() {
		$url = Configs::get( 'api' ) . '/v1/plugins/' . $this->slug . '/download';

		// Append our key if required.
		if ( ! $this->isWporgPlugin() ) {
			$url = add_query_arg( 'key', Configs::get( 'key' ), $url );
		}

		return $url;
	}

	/**
	 * Determine whether or not a plugin is a wp.org plugin.
	 *
	 * Currently utilizes the pluginInstaller config as set in library.global.php. This may return
	 * false positives if in the future we have a plugin in the repo which is not configured within
	 * pluginInstaller.
	 *
	 * @since 2.7.7
	 *
	 * @return bool
	 */
	public function isWporgPlugin() {
		$pluginInstaller = Configs::get( 'pluginInstaller' );

		return ! empty( $pluginInstaller[ 'wporgPlugins' ][$this->slug] );
	}
}
