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

	/**
	 * Setup.
	 *
	 * @since 1.7.7
	 */
	public function setUp() {
		// Setup our configs.
		update_site_option( 'boldgrid_api_key', $this->key );

		$this->resetConfigs();

		$this->backup         = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup' );
		$this->backup_premium = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup-premium' );
	}

	/**
	 * Reset library configs.
	 *
	 * @since 2.11.0
	 */
	public function resetConfigs() {
		$configsFile = dirname( dirname( dirname( __DIR__ ) ) ) . '/src/library.global.php';

		$defaults = include( $configsFile );
		Configs::set( $defaults );
	}

	/**
	 * Test getDownloadUrl.
	 *
	 * @since 2.7.7
	 */
	public function testGetDownloadUrl() {
		$this->resetConfigs();

		$this->assertEquals( $this->backup->getDownloadUrl(), 'https://api.boldgrid.com/v1/plugins/boldgrid-backup/download?key=' . $this->key );

		$this->assertEquals( $this->backup_premium->getDownloadUrl(), 'https://api.boldgrid.com/v1/plugins/boldgrid-backup-premium/download?key=' . $this->key );
	}

	/**
	 * Test firstVersionCompare.
	 *
	 * @since 2.11.0
	 */
	public function testFirstVersionCompare() {
		// Good data we will be testing against.
		$backupPluginsChecked = [
			'1.11.0' => 123456,
			'1.12.0' => 123456,
		];

		// Make sure good data gets us good data.
		$settings = [
			'plugins_checked' => [
				'other-plugin/other-plugin.php' => [
					'1.0.0' => 12345,
					'1.1.0' => 12346,
				],
				'boldgrid-backup/boldgrid-backup.php' => $backupPluginsChecked,
			],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->assertTrue( $this->backup->firstVersionCompare( '1.10.0', '>=' ) );
		$this->assertTrue( $this->backup->firstVersionCompare( '1.11.0', '==' ) );
		$this->assertTrue( $this->backup->firstVersionCompare( '1.12.0', '<=' ) );
	}

	/**
	 * Test getFirstVersion.
	 *
	 * @since 2.11.0
	 */
	public function testGetFirstVersion() {
		// Good data we will be testing against.
		$backupPluginsChecked = [
			'1.11.0' => 123456,
			'1.12.0' => 123456,
		];

		// Make sure good data gets us good data.
		$settings = [
			'plugins_checked' => [
				'other-plugin/other-plugin.php' => [
					'1.0.0' => 12345,
					'1.1.0' => 12346,
				],
				'boldgrid-backup/boldgrid-backup.php' => $backupPluginsChecked,
			],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->assertEquals( $this->backup->getFirstVersion(), '1.11.0' );
	}

	/**
	 * Test getPluginsChecked.
	 *
	 * @since 2.11.0
	 */
	public function testGetPluginsChecked() {
		// Good data we will be testing against.
		$backupPluginsChecked = [
			'1.11.0' => 123456,
			'1.12.0' => 123456,
		];

		// Make sure wrong data gives us an empty array.
		update_option( 'boldgrid_settings', false );
		$this->assertEquals( $this->backup->getPluginsChecked(), [] );

		// Make sure wrong data gives us an empty array.
		$settings = [
			'plugins_checked' => [],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->assertEquals( $this->backup->getPluginsChecked(), [] );

		// Make sure good data gets us good data.
		$settings = [
			'plugins_checked' => [
				'other-plugin/other-plugin.php' => [
					'1.0.0' => 12345,
					'1.1.0' => 12346,
				],
				'boldgrid-backup/boldgrid-backup.php' => $backupPluginsChecked,
			],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->assertEquals( $this->backup->getPluginsChecked(), $backupPluginsChecked );
	}
}