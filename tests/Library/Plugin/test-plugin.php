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
		$key         = 'CONNECT-KEY',
		$config,
		$plugin_data = [
			'Name'        => 'Total Upkeep',
			'PluginURI'   => 'https://www.boldgrid.com/boldgrid-backup/',
			'Version'     => '1.12.16',
			'Description' => 'Automated backups, remote backup to Amazon S3 and Google Drive, stop website crashes before they happen and more. Total Upkeep is the backup solution you need. By BoldGrid.',
			'Author'      => 'BoldGrid',
			'AuthorURI'   => 'https://www.boldgrid.com/',
			'TextDomain'  => 'boldgrid-backup',
			'DomainPath'  => '/languages',
		];

	/**
	 * Setup.
	 *
	 * @since 1.7.7
	 */
	public function setUp() {
		// Setup our configs.
		update_site_option( 'boldgrid_api_key', $this->key );

		$this->resetConfigs();
		$this->config         = $this->getPluginConfig();
		$this->backup         = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup', $this->config, $this->plugin_data );
		$this->backup_premium = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup-premium' );
	}

	/**
	 * Reset library configs.
	 *
	 * @since 2.11.0
	 */
	public function resetConfigs() {
		$configsFile = dirname( dirname( dirname( __DIR__ ) ) ) . '/src/library.global.php';

		$defaults = include $configsFile;
		Configs::set( $defaults );
	}

	public function getPluginConfig( array $add_pages = [], array $add_notices = [] ) {
		$config = [
			'pages'        => [
				'boldgrid-backup-premium-features',
			],
			'page_notices' => [
				[
					'id'      => 'bgbkup_database_encryption',
					'page'    => 'boldgrid-backup-premium-features',
					'version' => '1.12.16',
				],
			],
		];
		foreach ( $add_pages as $page ) {
			$config['pages'][] = $page;
		}
		foreach ( $add_notices as $notice ) {
			$config['page_notices'][] = $notice;
		}
		return $config;
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
				'other-plugin/other-plugin.php'       => [
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
				'other-plugin/other-plugin.php'       => [
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
				'other-plugin/other-plugin.php'       => [
					'1.0.0' => 12345,
					'1.1.0' => 12346,
				],
				'boldgrid-backup/boldgrid-backup.php' => $backupPluginsChecked,
			],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->assertEquals( $this->backup->getPluginsChecked(), $backupPluginsChecked );
	}
	// Make sure that the Plugin::getPages and Plugin::setPages are working from passed $config array
	public function testGetSetPages() {
		$page_count = count( $this->config['pages'] );
		$this->assertEquals( count( $this->backup->getPages() ), $page_count );
	}
	// Ensure that the initial getUnreadCount is correct
	public function testGetUnreadCount() {
		$expected_count = count( $this->config['page_notices'] );

		$this->backup->pluginData = $this->plugin_data;
		$this->assertEquals( $this->backup->getUnreadCount(), $expected_count );
	}
	// Ensure that the getUnreadMarkup is correct
	public function testGetUnreadMarkup() {
		$expected_markup = '<span class="bglib-unread-notice-count">' . count( $this->config['page_notices'] ) . '</span>';

		$this->backup->pluginData = $this->plugin_data;
		$this->assertEquals( $this->backup->getUnreadMarkup(), $expected_markup );
	}

	public function testNoNoticesMarkup() {
		$expected_markup = '<span class="bglib-unread-notice-count hidden"></span>';

		$this->backup->pluginData = $this->plugin_data;
		$this->backup->setAllNoticesRead();
		$this->assertEquals( $this->backup->getUnreadMarkup(), $expected_markup );
		$this->backup->setAllNoticesRead( true );
	}

	public function testTwoPagesWithNotices() {
		$two_page_config = $this->getPluginConfig(
			[ 'boldgrid-backup-settings' ],
			[
				[
					'id'      => 'bgbkup_backup_settings_test',
					'page'    => 'boldgrid-backup-settings',
					'version' => '1.12.16',
				],
			]
		);

		$expected_markup    = '<span class="bglib-unread-notice-count">' . count( $two_page_config['page_notices'] ) . '</span>';
		$plugin             = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup', $two_page_config, $this->plugin_data );
		$plugin->pluginData = $this->plugin_data;
		$this->assertEquals( $plugin->getUnreadMarkup(), $expected_markup );
	}

	public function testGetPageBySlug() {
		$two_page_config = $this->getPluginConfig(
			[ 'boldgrid-backup-settings' ],
			[
				[
					'id'      => 'bgbkup_backup_settings_test',
					'page'    => 'boldgrid-backup-settings',
					'version' => '1.12.16',
				],
			]
		);

		$plugin             = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup', $two_page_config, $this->plugin_data );
		$plugin->pluginData = $this->plugin_data;
		$success_count      = 0;
		foreach ( $two_page_config['pages'] as $config_page ) {
			$pageSlug = $plugin->getPageBySlug( $config_page );
			if ( $pageSlug ) {
				$success_count++;
			}
		}
		$this->assertEquals( $success_count, count( $two_page_config['pages'] ) );

		$this->assertTrue( empty( $plugin->getPageBySlug( 'not_a_real_page_slug' ) ) );
	}
}
