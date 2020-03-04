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
 * @since 2.7.7
 */
class Test_BoldGrid_Library_Library_Plugin_UpdateData extends \WP_UnitTestCase {
	/**
	 * Setup.
	 *
	 * @since 1.7.7
	 */
	public function setUp() {
		// Setup our configs.
		$this->transient_data = array(
			'twentytwenty' => array(
				'version'      => '1.1',
				'downloaded'   => 482251,
				'last_updated' => new \DateTime('2020-02-05'),
			),
		);
	}

	public function test_getResponseData() {
		$updateData  = new UpdateData( null, 'akismet' );
		$responseData = $updateData->getResponseData();
		$responseData_props = array_keys( ( array ) $responseData );
		sort( $responseData_props );
		$expected_data = array(
			'active_installs',
			'version',
			'downloaded',
			'last_updated',
			'stats',
		);
		sort( $expected_data );
		$this->assertEquals( $expected_data, $responseData_props );
	}

	public function test_fetchPluginStats() {
		$updateData = new UpdateData( null, 'FakeFake' );
		$this->assertFalse( $updateData->fetchPluginStats() );
	}
}