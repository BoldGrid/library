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

		$url = add_query_arg( 'key', Configs::get( 'key' ), $url );

		return $url;
	}
}
