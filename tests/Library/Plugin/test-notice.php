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
 * @since SINCEVERSION
 */
class Test_BoldGrid_Library_Library_Plugin_Notice extends WP_UnitTestCase {

	private
		$plugin,
		$page,
		$config,
		$notice,
		$plugin_data = [
			'Name'        => 'Total Upkeep',
			'PluginURI'   => 'https://www.boldgrid.com/boldgrid-backup/',
			'Version'     => '1.12.0',
			'Description' => 'Automated backups, remote backup to Amazon S3 and Google Drive, stop website crashes before they happen and more. Total Upkeep is the backup solution you need. By BoldGrid.',
			'Author'      => 'BoldGrid',
			'AuthorURI'   => 'https://www.boldgrid.com/',
			'TextDomain'  => 'boldgrid-backup',
			'DomainPath'  => '/languages',
		],
		$default_first_version = '1.0.0',
		$default_newest_version = '1.12.0';

	/**
	 * Setup.
	 *
	 * @since SINCEVERSION
	 */
	public function setUp() {
		// Setup our configs.
		$this->config = $this->getPluginConfig();
		$this->plugin = new Boldgrid\Library\Library\Plugin\Plugin( 'boldgrid-backup', $this->config, $this->plugin_data );
		$this->page   = $this->plugin->getPageBySlug( $this->config['pages'][0] );
		$this->notice = $this->page->getNotices()[0];
		$this->getFirstVersion();
		$this->getPluginsChecked();
	}

	/**
	 * Set settings versions.
	 * 
	 * Sets the version information in the boldgrid_settings option.
	 * 
	 * @since SINCEVERSION
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
	 * @since SINCEVERSION
	 * 
	 * @param string $version notice version number for testing.
	 * @param array $add_pages optional. an array of page_slug strings.
	 * @param array $add_notices optional. an array of page_notices to add.
	 * 
	 * @return array
	 */
	public function getPluginConfig( $version = '1.12.16', array $add_pages = [], array  $add_notices = [] ) {
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
	 * @since SINCEVERSION
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
				'other-plugin/other-plugin.php' => [
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
	 * @since SINCEVERSION
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
				'other-plugin/other-plugin.php' => [
					'1.0.0' => 12345,
					'1.1.0' => 12346,
				],
				'boldgrid-backup/boldgrid-backup.php' => $backupPluginsChecked,
			],
		];
		update_option( 'boldgrid_settings', $settings );
		$this->plugin->getPluginsChecked();
	}

	//Test Notice::setNoticeId.
	public function testSetNoticeId() {
		$original_notice_id = $this->notice->getId();
		$new_notice_id = 'test_set_new_id';
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
		$new_version = '9.9.9';
		$this->notice->setVersion( $new_version );
		$this->assertEquals( $new_version, $this->notice->getVersion() );
		$this->notice->setVersion( $original_version );
		$this->assertEquals( $original_version, $this->notice->getVersion() );
	}

	//Test that notices return as read if this is first install.
	public function testNoticeIfFirstInstall() {
		$this->set_settings_versions('1.12.0', '1.12.0');
		$this->assertEquals( false, $this->notice->getIsUnread() );
		$this->set_settings_versions( $this->default_first_version, $this->default_newest_version );
		$this->assertEquals( true, $this->notice->getIsUnread() );
	}

	//Test Notice::getPlugin().
	public function testGetPlugin() {
		$plugin = $this->notice->getPlugin();
		$this->assertEquals( $this->plugin, $plugin );
	}

	//Test Notice::noticeVersionChanged().
	public function testNoticeVersionChanged() {
		$this->assertEquals( true, $this->notice->getIsUnread() );
		$this->notice->setIsUnread( false );
		$new_plugin = new Boldgrid\Library\Library\Plugin\Plugin(
			'boldgrid-backup',
			$this->getPluginConfig( $version = '2.12.16' ),
			$this->plugin_data
		);
		$this->page = $new_plugin->getPageBySlug( $this->config['pages'][0] );
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