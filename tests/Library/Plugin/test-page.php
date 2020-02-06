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
 * BoldGrid Library Library Plugin Page Test class.
 *
 * @since 2.7.7
 */
class Test_BoldGrid_Library_Library_Plugin_Page extends WP_UnitTestCase {

	private
		$plugin,
		$page,
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
		$this->config = $this->getPluginConfig();
		$this->plugin = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup', $this->config, $this->plugin_data );
		$this->page   = $this->plugin->getPageBySlug( $this->config['pages'][0] );
		$this->getFirstVersion();
		$this->getPluginsChecked();
	}

	public function getPluginConfig( array $add_pages = [], array $add_notices = [] ) {
		$config = [
			'pages'        => [
				'boldgrid-backup-premium-features',
				'boldgrid-backup-settings',
			],
			'page_notices' => [
				[
					'id'      => 'bgbkup_database_encryption',
					'page'    => 'boldgrid-backup-premium-features',
					'version' => '1.12.16',
				],
				[
					'id'      => 'bgbkup_google_drive',
					'page'    => 'boldgrid-backup-premium-features',
					'version' => '1.12.16',
				],
				[
					'id'      => 'bgbkup_new_settings_feature',
					'page'    => 'boldgrid-backup-settings',
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
	 * Test getFirstVersion.
	 *
	 * @since 2.11.0
	 */
	public function getFirstVersion() {
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
		$this->plugin->getFirstVersion();
	}

	/**
	 * Test getPluginsChecked.
	 *
	 * @since 2.11.0
	 */
	public function getPluginsChecked() {
		// Good data we will be testing against.
		$backupPluginsChecked = [
			'1.11.0' => 123456,
			'1.12.0' => 123456,
		];

		// Make sure wrong data gives us an empty array.
		update_option( 'boldgrid_settings', false );

		// Make sure wrong data gives us an empty array.
		$settings = [
			'plugins_checked' => [],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->plugin->getPluginsChecked();

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
		$this->plugin->getPluginsChecked();
	}
	//Test Page::getNoticeById()
	public function testGetNoticeById() {
		$expected_id = $this->config['page_notices'][0]['id'];
		$this->assertEquals( $expected_id, $this->page->getNoticeById( $expected_id )->getId() );
	}

	//Test Page::getNotices() to be sure it matches the config contents.
	public function testGetNotices() {
		$expected_notice_ids = [];
		foreach ( $this->config['page_notices'] as $config_notice ) {
			if ( $config_notice['page'] === $this->page->getSlug() ) {
				$expected_notice_ids[] = $config_notice['id'];
			}
		}
		sort( $expected_notice_ids );
		$notices    = $this->page->getNotices();
		$notice_ids = [];
		foreach ( $notices as $notice ) {
			$notice_ids[] = $notice->getId();
		}
		sort( $notice_ids );
		$this->assertEquals( $expected_notice_ids, $notice_ids );
	}

	//Test Page::getPlugin() to be sure it matches $this->plugin
	public function testGetPlugin() {
		$this->assertEquals( $this->plugin, $this->page->getPlugin() );
	}

	//Test Page:getPluginConfig() to be sure it matchees the supplied config file.
	public function testGetPluginConfig() {
		$this->assertEquals( $this->config, $this->page->getPluginConfig() );
	}

	//Test that the UnreadCount for the page is correct.
	public function testGetUnreadCount() {
		$expected_count = 0;
		foreach ( $this->config['page_notices'] as $config_notice ) {
			if ( $config_notice['page'] === $this->page->getSlug() ) {
				$expected_count++;
			}
		}
		$this->plugin->pluginData = $this->plugin_data;
		$this->assertEquals( $expected_count, $this->page->getUnreadCount() );
	}
	//Test that the UnreadCountMarkup for the page is correct.
	public function testGetUnreadCountMarkup() {
		$expected_count  = 0;

		foreach ( $this->config['page_notices'] as $config_notice ) {
			if ( $config_notice['page'] === $this->page->getSlug() ) {
				$expected_count++;
			}
		}

		$expected_markup = '<span class="bglib-unread-notice-count">' . $expected_count . '</span>';

		$this->plugin->pluginData = $this->plugin_data;
		$this->assertEquals( $expected_markup, $this->page->getUnreadMarkup() );
	}

	//Tests that all notices on page can be set to read
	public function testSetAllNoticesRead() {
		$expected_markup = '<span class="bglib-unread-notice-count hidden"></span>';

		$this->plugin->pluginData = $this->plugin_data;
		$this->page->setAllNoticesRead();
		$this->assertEquals( $expected_markup, $this->page->getUnreadMarkup() );
		$this->page->setAllNoticesRead( true );
	}
	//Tests trying to get a Notice that does not exist
	public function testGetNonExistNotice() {
		$expected_id = null;
		$this->assertEquals( $expected_id, $this->page->getNoticeById( 'non_existant_notice' ) );
	}
}
