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

	private $backup, $backup_premium;

	private static $configs;

	/**
	 * Setup.
	 *
	 * @since 1.7.7
	 */
	public function setUp() {

		// Setup our configs.
		update_site_option( 'boldgrid_api_key', 'CONNECT-KEY' );
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

		$this->assertNotContains( 'key=', $this->backup->getDownloadUrl() );

		$this->assertContains( 'key=', $this->backup_premium->getDownloadUrl() );
	}

	/**
	 * Test isWporgPlugin.
	 *
	 * @since 2.7.7
	 */
	public function testIsWporgPlugin() {

		// Reset our configs.
		Configs::set( self::$configs );

		$this->assertTrue( $this->backup->isWporgPlugin() );

		$this->assertNotTrue( $this->backup_premium->isWporgPlugin() );
	}
}