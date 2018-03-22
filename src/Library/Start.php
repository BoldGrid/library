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
	 * @uses \Boldgrid\Library\Library\Configs()
	 * @uses \Boldgrid\Library\Library\ReleaseChannel()
	 * @uses \Boldgrid\Library\Library\Key()
	 * @uses \Boldgrid\Library\Plugin\Installer()
	 * @uses \Boldgrid\Library\Library\Start::getReleaseChannel()
	 *
	 * @param array $configs Plugin configuration array.
	 */
	public function __construct( $configs = null ) {
		$configs = $this->filterConfigs( $configs );

		$this->configs = new Configs( $configs );
		$this->releaseChannel = new ReleaseChannel;

		if ( Configs::get( 'keyValidate' ) ) {
			$this->key = new Key( $this->getReleaseChannel() );
		}

		add_action( 'admin_init' , array( $this, 'loadPluginInstaller' ) );
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
	 *
	 * @uses \Boldgrid\Library\Library\Plugin\Checker::run()
	 */
	public function init() {

		// Registration class runs Filter::add($this) in __construct.
		$registration = new \Boldgrid\Library\Library\Registration();

		// Update class runs Filter::add($this) in __construct.
		$update = new \Boldgrid\Library\Library\Update();

		$pluginChecker = new \Boldgrid\Library\Library\Plugin\Checker();
		$pluginChecker->run();
	}

	/**
	 * Load the Plugin\Installer class, if exists.
	 *
	 * @since 1.1.7
	 */
	public function loadPluginInstaller() {
		if ( ! did_action( 'Boldgrid\Library\Library\Start::loadPluginInstaller' ) ) {
			do_action( 'Boldgrid\Library\Library\Start::loadPluginInstaller' );

			if ( class_exists( '\Boldgrid\Library\Plugin\Installer' ) ) {
				$this->pluginInstaller = new \Boldgrid\Library\Plugin\Installer(
					Configs::get( 'pluginInstaller' ),
					$this->getReleaseChannel()
				);
			}
		}
	}

	/**
	 * Filter the configuration array.
	 *
	 * @since 2.2.1
	 *
	 * @param  array $configs Configuration array.
	 * @return array
	 */
	public function filterConfigs( $configs ) {
		if ( ! empty( $configs['libraryDir'] ) ) {
			$configs['libraryUrl'] = str_replace(
				ABSPATH,
				get_site_url() . '/',
				$configs['libraryDir']
			);
		}

		return $configs;
	}
}
