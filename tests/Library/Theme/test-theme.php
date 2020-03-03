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
use Boldgrid\Library\Library\Theme;

/**
 * BoldGrid Library Library Plugin Plugin Test class.
 *
 * @since 2.7.7
 */
class Test_BoldGrid_Library_Library_Theme_Theme extends WP_UnitTestCase {

	private
		$stylesheet,
		$old_theme_url;

	/**
	 * Setup.
	 *
	 * @since 1.7.7
	 */
	public function setUp() {
		// Setup our configs.
		delete_site_transient( 'update_themes' );
		$this->stylesheet = 'twentytwenty';
		$this->old_theme_url = 'https://downloads.wordpress.org/theme/twentytwenty.1.0.zip';
		$this->transient_data = array(
			$this->stylesheet => array(
				'version'      => '1.1',
				'downloaded'   => 482251,
				'last_updated' => new DateTime('2020-02-05'),
			),
		);
	}

	public function test_constructor() {
		$wp_theme = wp_get_theme( $this->stylesheet );
		$theme = new Theme\Theme( $wp_theme );
		$this->assertEquals( $wp_theme, $theme->wp_theme );
		$this->assertEquals( $this->stylesheet, $theme->stylesheet );
	}

	public function test_getIsActive() {
		$theme = new Theme\Theme( wp_get_theme( $this->stylesheet ) );
		switch_theme( 'twentynineteen' );
		$this->assertFalse( $theme->getIsActive() );
		switch_theme( $this->stylesheet );
		$this->assertTrue( $theme->getIsActive() );
	}

	public function test_getHasUpdate() {
		$wp_theme = wp_get_theme( $this->stylesheet );
		$theme = new Theme\Theme( $wp_theme );

		$this->assertFalse( $theme->getHasUpdate() );

		switch_theme( 'twentynineteen' );
		$deleted = delete_theme( $this->stylesheet );
		$unzip = unzip_file( download_url( $this->old_theme_url ), ABSPATH . '/wp-content/themes/' );
		switch_theme( 'twentytwenty' );
		delete_transient( 'boldgrid_theme_information' );
		wp_cache_flush();
		$old_wp_theme = wp_get_theme( $this->stylesheet );
		$old_theme = new Theme\Theme( wp_get_theme( $this->stylesheet ) );
		wp_update_themes();

		$this->assertTrue( $old_theme->getHasUpdate() );
	}
}