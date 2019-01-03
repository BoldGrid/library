<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Plugintest
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Library Plugin Plugin Test class.
 *
 * @since 2.7.7
 */
class Test_BoldGrid_Library_Library_Plugin_Plugin extends WP_UnitTestCase {

	private
		$backup,
		$backup_premium,
		$key = 'CONNECT-KEY';

	private static $configs;

	/**
	 * Setup.
	 *
	 * @since 1.7.7
	 */
	public function setUp() {

		// Setup our configs.
		update_site_option( 'boldgrid_api_key', $this->key );
		// Initialization needed so the constructor will include library.global.php.
		new \Boldgrid\Library\Library\Configs();
		// Configs are a real problem in this test. If we can get configs, save them.
		$configs = Configs::get();
		if ( ! empty( $configs ) ) {
			self::$configs = $configs;
		}

		$this->backup         = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup' );
		$this->backup_premium = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup-premium' );
	}

	/**
	 * Test getDownloadUrl.
	 *
	 * @since 2.7.7
	 */
	public function testGetDownloadUrl() {

		// Reset our configs.
		Configs::set( self::$configs );

		$this->assertEquals( $this->backup->getDownloadUrl(), 'https://api.boldgrid.com/v1/plugins/boldgrid-backup/download?key=' . $this->key );

		$this->assertEquals( $this->backup_premium->getDownloadUrl(), 'https://api.boldgrid.com/v1/plugins/boldgrid-backup-premium/download?key=' . $this->key );
	}
}