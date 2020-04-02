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
 * @param string $url URL to get.
 * @since SINCEVERSION
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
 * @since SINCEVERSION
 */
class Test_BoldGrid_Library_Library_Plugin_UpdateData extends \WP_UnitTestCase {
	/**
	 * Setup.
	 *
	 * @since SINCEVERSION
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
	 * @since SINCEVERSION
	 */
	public function test_fetchPluginStats() {
		$update_data = new UpdateData( null, 'FakeFake' );
		$this->assertFalse( $update_data->fetchPluginStats() );
	}
}