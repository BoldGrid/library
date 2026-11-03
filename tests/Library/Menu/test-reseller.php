<?php
/**
 * Menu reseller reads a missing option as an empty array.
 *
 * @package Boldgrid_Library
 */

/**
 * Class: Test_BoldGrid_Library_Menu_Reseller
 */
class Test_BoldGrid_Library_Menu_Reseller extends WP_UnitTestCase {

	/**
	 * A missing boldgrid_reseller option does not get used as an array.
	 */
	public function test_missing_option_returns_default_data() {
		delete_option( 'boldgrid_reseller' );

		$reseller = new Boldgrid\Library\Library\Menu\Reseller();
		$data     = $reseller->getData();

		$this->assertIsArray( $data );
		$this->assertNull( $data['reseller_identifier'] );
		$this->assertSame( 'https://www.boldgrid.com/', $data['reseller_website_url'] );
	}
}
