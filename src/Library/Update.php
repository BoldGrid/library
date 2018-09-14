<?php
/**
 * BoldGrid Update.
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 2.3.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Library;

/**
 * BoldGrid Update.
 *
 * The main purpose of this class is to handle auto updates (as configured in
 * the boldgrid_settings option).
 *
 * @since 2.3.0
 *
 * @see https://codex.wordpress.org/Configuring_Automatic_Background_Updates
 */
class Update {

	/**
	 * Constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Auto update plugin.
	 *
	 * @since 2.3.0
	 *
	 * @hook: auto_update_plugin
	 *
	 * @param  bool   $update Update API response.
	 * @param  object $item   Item being updated.
	 * @return bool
	 */
	public function auto_update_plugin( $update, $item ) {
		// New settings.
		$connectSettings = \Boldgrid\Library\Util\Option::get( 'connect_settings' );

		// Old settings.
		$pluginAutoupdate = \Boldgrid\Library\Util\Option::get( 'plugin_autoupdate' );

		return ! empty( $connectSettings['autoupdate']['plugins'][ $item->plugin ] ) ||
			! empty( $pluginAutoupdate );
	}

	/**
	 * Auto update theme.
	 *
	 * @since 2.3.0
	 *
	 * @hook: auto_update_theme
	 *
	 * @param  bool   $update Update API response.
	 * @param  object $item   Item being updated.
	 * @return bool
	 */
	public function auto_update_theme( $update, $item ) {
		// New settings.
		$connectSettings = \Boldgrid\Library\Util\Option::get( 'connect_settings' );

		// Old settings.
		$themeAutoupdate = \Boldgrid\Library\Util\Option::get( 'theme_autoupdate' );

		return ! empty( $connectSettings['autoupdate']['themes'][ $item->theme ] ) ||
			! empty( $themeAutoupdate );
	}
}
