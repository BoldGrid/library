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
		$this->releaseChannel = new ReleaseChannel;

		if ( Configs::get( 'keyValidate' ) ) {
			$this->key = new Key( $this->getReleaseChannel() );
		}

		$this->pluginInstaller = new Plugin\Installer( Configs::get( 'pluginInstaller' ), $this->getReleaseChannel() );
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
	 * Initialization.
	 *
	 * @since 1.1.4
	 */
	public function init() {
		$pluginChecker = new Plugin\Checker();
		$pluginChecker->run();
	}
}
