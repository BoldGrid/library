<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Plugintest
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library\Configs;

/**
 * WP Remote Get Override.
 *
 * This is a function written to override the default wp_remote_get function.
 * It is outsite of the class , but within the same namespace as the class.
 * This is necessary for the class to override the existing wp_remote_get().
 * function during testing.
 *
 * @param string $url URL to get.
 * @since 2.12.2
 */
function wp_remote_get( $url ) {
	if ( false === strpos( $url, 'FakeFake' ) ) {
		return \wp_remote_get( $url );
	} else {
		return false;
	}
}

/**
 * BoldGrid Library Library Plugin Plugin Test class.
 *
 * @since 2.12.2
 */
class Test_BoldGrid_Library_Library_Plugin_UpdateData extends \WP_UnitTestCase {
	/**
	 * Setup.
	 *
	 * @since 2.12.2
	 */
	public function setUp() {
		// Setup our configs.
		$this->transient_data = array(
			'twentytwenty' => array(
				'version'      => '1.1',
				'downloaded'   => 482251,
				'last_updated' => new \DateTime( '2020-02-05' ),
			),
		);
	}

	/**
	 * Test FetchPluginStats.
	 *
	 * @since 2.12.2
	 */
	public function test_fetchPluginStats() {
		$update_data = new UpdateData( null, 'FakeFake' );
		$this->assertFalse( $update_data->fetchPluginStats() );
	}
}