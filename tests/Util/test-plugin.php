<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Plugintest
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Util Plugin Test class.
 *
 * @since 1.1.4
 */
class Test_BoldGrid_Util_Plugin extends WP_UnitTestCase {

	/**
	 * Test getFiltered.
	 *
	 * @since 1.1.4
	 */
	public function testGetFiltered() {
		$plugin = new Boldgrid\Library\Util\Plugin();

		$allPlugins = get_plugins();

		// This should find all plugins.
		$plugins = $plugin->getFiltered();
		$this->assertSame( $allPlugins, $plugins );
		$plugins = $plugin->getFiltered( 'php' );
		$this->assertSame( $allPlugins, $plugins );

		// Hello plugin is included with WordPress.
		$plugins = $plugin->getFiltered( 'hello' );
		$this->assertSame( 1, count( $plugins ) );

		// No BoldGrid plugins are included with WordPress.
		$plugins = $plugin->getFiltered( 'boldgrid-' );
		$this->assertSame( 0, count( $plugins ) );
	}
}