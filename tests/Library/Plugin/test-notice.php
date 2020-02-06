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
 * BoldGrid Library Library Plugin Notice Test class.
 *
 * @since 2.12.0
 */
class Test_BoldGrid_Library_Library_Plugin_Notice extends WP_UnitTestCase {

	private
		$plugin,
		$page,
		$config,
		$notice,
		$plugin_data            = [
			'Name'        => 'Total Upkeep',
			'PluginURI'   => 'https://www.boldgrid.com/boldgrid-backup/',
			'Version'     => '1.12.0',
			'Description' => 'Automated backups, remote backup to Amazon S3 and Google Drive, stop website crashes before they happen and more. Total Upkeep is the backup solution you need. By BoldGrid.',
			'Author'      => 'BoldGrid',
			'AuthorURI'   => 'https://www.boldgrid.com/',
			'TextDomain'  => 'boldgrid-backup',
			'DomainPath'  => '/languages',
		],
		$default_first_version  = '1.0.0',
		$default_newest_version = '1.12.0';

	/**
	 * Setup.
	 *
	 * @since 2.12.0
	 */
	public function setUp() {
		// Setup our configs.
		$this->config = $this->getPluginConfig();
		$this->plugin = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup', $this->config );
		$this->page   = $this->plugin->getPageBySlug( $this->config['pages'][0] );
		$this->notice = $this->page->getNotices()[0];
		$this->getFirstVersion();
		$this->getPluginsChecked();

		$this->makeFakePlugin();
	}

	/**
	 * Set settings versions.
	 *
	 * Sets the version information in the boldgrid_settings option.
	 *
	 * @since 2.12.0
	 *
	 * @param string @first_version
	 * @param string @current_version
	 */
	public function set_settings_versions( $first_version, $current_version ) {
		$boldgrid_settings = get_option( 'boldgrid_settings', [] );
		$boldgrid_settings['plugins_checked']['boldgrid-backup/boldgrid-backup.php'] = [
			$first_version   => 123456,
			$current_version => 123456,
		];
		update_option( 'boldgrid_settings', $boldgrid_settings );
	}
	/**
	 * Get the Plugin Config and / or change it
	 *
	 * Gets the plugin config file set here for testing
	 *
	 * @since 2.12.0
	 *
	 * @param string $version notice version number for testing.
	 * @param array $add_pages optional. an array of page_slug strings.
	 * @param array $add_notices optional. an array of page_notices to add.
	 *
	 * @return array
	 */
	public function getPluginConfig( $version = '1.12.16', array $add_pages = [], array $add_notices = [] ) {
		$config = [
			'pages'        => [
				'boldgrid-backup-premium-features',
				'boldgrid-backup-settings',
			],
			'page_notices' => [
				[
					'id'      => 'bgbkup_database_encryption',
					'page'    => 'boldgrid-backup-premium-features',
					'version' => $version,
				],
				[
					'id'      => 'bgbkup_google_drive',
					'page'    => 'boldgrid-backup-premium-features',
					'version' => $version,
				],
				[
					'id'      => 'bgbkup_new_settings_feature',
					'page'    => 'boldgrid-backup-settings',
					'version' => $version,
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
	 * getFirstVersion.
	 *
	 * @since 2.12.0
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
	 * getPluginsChecked.
	 *
	 * @since 2.12.0
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

	/**
	 * Make a fake plugin to test this class against.
	 *
	 * @since 2.12.0
	 */
	public function makeFakePlugin() {
		$path = ABSPATH . 'wp-content/plugins/fake-plugin';

		// Make the plugin's directory.
		if ( file_exists( $path ) ) {
			exec( 'rm -rf ' . ABSPATH . 'wp-content/plugins/fake-plugin' ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
		}
		mkdir( $path );

		// Add the plugin's main file.
		file_put_contents( //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			$path . '/fake-plugin.php',
			'<?php
			/**
			 * File: fake-plugin.php
			 *
			 * @link https://www.domain.com
			 * @since 1.0.0
			 * @package Fake_Plugin
			 *
			 *          @wordpress-plugin
			 *          Plugin Name: Fake Plugin
			 *          Plugin URI: https://www.domain.com
			 *          Description: Description goes here.
			 *          Version: 1.5.0
			 *          Author: Me
			 *          Author URI: https://www.me.com
			 *          License: GPL-2.0+
			 *          License URI: http://www.gnu.org/licenses/gpl-2.0.txt
			 *          Text Domain: fake-plugin
			 *          Domain Path: /languages
			 */'
		);

		$configs = [
			'pages'        => [
				'boldgrid-backup-premium-features',
			],
			'page_notices' => [
				[
					'id'      => 'notice-1',
					'page'    => 'boldgrid-backup-premium-features',
					'version' => '1.1.0',
				],
			],
		];

		$this->fakePlugin = new Boldgrid\Library\Library\Plugin\Plugin( 'fake-plugin', $configs );

		// Configure this plugin's version info (IE first version installed).
		$boldgrid_settings = get_option( 'boldgrid_settings', [] );
		$boldgrid_settings['plugins_checked']['fake-plugin/fake-plugin.php'] = [
			'1.0.0' => 946684800,
			'1.3.0' => 946685800,
			'1.5.0' => 946686800,
		];
		update_option( 'boldgrid_settings', $boldgrid_settings );
	}

	/**
	 * Test setIsUnread.
	 *
	 * @since 2.12.0
	 */
	public function testSetIsUnread() {
		// Create a new notice.
		$notice = new Boldgrid\Library\Library\Plugin\Notice(
			$this->fakePlugin,
			[
				'id'      => 'notice-testSetUnread',
				'page'    => 'page-slug',
				'version' => '1.5.0',
			]
		);

		// By default, it should be unread.
		$this->assertTrue( $notice->getIsUnread() );

		// After we set it as being read, it should be read.
		$notice->setIsUnread( false );
		$this->assertFalse( $notice->getIsUnread() );

		// If for some reason we mark it as unread, it should be unread.
		$notice->setIsUnread( true );
		$this->assertTrue( $notice->getIsUnread() );
	}

	//Test Notice::setNoticeId.
	public function testSetNoticeId() {
		$original_notice_id = $this->notice->getId();
		$new_notice_id      = 'test_set_new_id';

		$this->notice->setId( $new_notice_id );
		$this->assertEquals( $new_notice_id, $this->notice->getId() );
		$this->notice->setId( $original_notice_id );
		$this->assertEquals( $original_notice_id, $this->notice->getId() );
	}

	//Test Notice::setPageSlug().
	public function testSetPageSlug() {
		$original_slug = $this->notice->getPageSlug();
		$new_page_slug = 'test_set_page_slug';
		$this->notice->setPageSlug( $new_page_slug );
		$this->assertEquals( $new_page_slug, $this->notice->getPageSlug() );
		$this->notice->setPageSlug( $original_slug );
		$this->assertEquals( $original_slug, $this->notice->getPageSlug() );
	}

	//Test Notice::setVersion().
	public function testSetNoticeVersion() {
		$original_version = $this->notice->getVersion();
		$new_version      = '9.9.9';

		$this->notice->setVersion( $new_version );
		$this->assertEquals( $new_version, $this->notice->getVersion() );
		$this->notice->setVersion( $original_version );
		$this->assertEquals( $original_version, $this->notice->getVersion() );
	}

	//Test that notices return as read if this is first install.
	public function testNoticeIfFirstInstall() {
		$this->plugin->pluginData = $this->plugin_data;
		$this->set_settings_versions( '1.12.0', '1.12.0' );
		$this->assertEquals( false, $this->notice->getIsUnread() );
		$this->set_settings_versions( $this->default_first_version, $this->default_newest_version );
		$this->assertEquals( true, $this->notice->getIsUnread() );
	}

	/**
	 * Test alreadyExists.
	 *
	 * @since 2.12.0
	 */
	public function testAlreadyExists() {
		// Create a new notice.
		$notice = new Boldgrid\Library\Library\Plugin\Notice(
			$this->fakePlugin,
			[
				'id'      => 'notice-testAlreadyExists',
				'page'    => 'page-slug',
				'version' => '1.5.0',
			]
		);

		// By default, it should not exist.
		$this->assertFalse( $notice->alreadyExists() );

		// After we set the notice as being read, it should exist.
		$notice->setIsUnread( false );
		$this->assertTrue( $notice->alreadyExists() );
	}

	//Test Notice::getPlugin().
	public function testGetPlugin() {
		$plugin = $this->notice->getPlugin();
		$this->assertEquals( $this->plugin, $plugin );
	}

	/**
	 * Test maybeShow.
	 *
	 * @since 2.12.0
	 */
	public function testMaybeShow() {
		/*
		 * The notice should show in this scenario.
		 *
		 * We are on the version that this new feature is for.
		 *
		 * Plugin's first version: 1.0.0
		 * This version:           1.5.0
		 * This notice's version:  1.5.0
		 */
		$notice = new Boldgrid\Library\Library\Plugin\Notice(
			$this->fakePlugin,
			[
				'id'      => 'notice-1',
				'page'    => 'page-slug',
				'version' => '1.5.0',
			]
		);
		$this->assertTrue( $notice->maybeShow() );

		/*
		 * The notice should show in this scenario.
		 *
		 * Maybe we upgrade from 1.3.0 to 1.5.0, and this notice is for 1.4.0.
		 *
		 * Plugin's first version: 1.0.0
		 * This version:           1.5.0
		 * This notice's version:  1.4.0
		 */
		$notice = new Boldgrid\Library\Library\Plugin\Notice(
			$this->fakePlugin,
			[
				'id'      => 'notice-2',
				'page'    => 'page-slug',
				'version' => '1.4.0',
			]
		);
		$this->assertTrue( $notice->maybeShow() );

		/*
		 * The notice should NOT show in this scenario.
		 *
		 * The notice is for a version prior to what we installed.
		 *
		 * Plugin's first version: 1.0.0
		 * This version:           1.5.0
		 * This notice's version:  0.9.0
		 */
		$notice = new Boldgrid\Library\Library\Plugin\Notice(
			$this->fakePlugin,
			[
				'id'      => 'notice-3',
				'page'    => 'page-slug',
				'version' => '0.9.0',
			]
		);
		$this->assertFalse( $notice->maybeShow() );

		/*
		 * The notice should NOT show in this scenario.
		 *
		 * They just installed a plugin so essentially they shouldn't be seeing anything new.
		 *
		 * Plugin's first version: 1.0.0
		 * This version:           1.5.0
		 * This notice's version:  1.0.0
		 */
		$notice = new Boldgrid\Library\Library\Plugin\Notice(
			$this->fakePlugin,
			[
				'id'      => 'notice-3',
				'page'    => 'page-slug',
				'version' => '1.0.0',
			]
		);
		$this->assertFalse( $notice->maybeShow() );
	}

	//Test Notice::noticeVersionChanged().
	public function testNoticeVersionChanged() {
		$this->plugin->pluginData = $this->plugin_data;
		$this->assertEquals( true, $this->notice->getIsUnread() );
		$this->notice->setIsUnread( false );
		$new_plugin = new Boldgrid\Library\Library\Plugin\Plugin(
			'boldgrid-backup',
			$this->getPluginConfig( '2.12.16' )
		);

		$new_plugin->pluginData = $this->plugin_data;

		$this->page   = $new_plugin->getPageBySlug( $this->config['pages'][0] );
		$this->notice = $this->page->getNotices()[0];
		$this->assertEquals( true, $this->notice->getIsUnread() );

		$this->assertEquals( true, true );
	}

	//Test Notice::setPlugin().
	public function testSetPlugin() {
		$new_plugin = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup' );
		$this->notice->setPlugin( $new_plugin );
		$plugin = $this->notice->getPlugin();
		$this->assertNotEquals( $this->plugin, $plugin );
	}
}
