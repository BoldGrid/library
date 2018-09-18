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
	 * @see \Boldgrid\Library\Util\Option::get()
	 *
	 * @param  bool   $update Update API response.
	 * @param  object $item   Item being updated.
	 * @return bool
	 */
	public function auto_update_plugin( $update, $item ) {
		$update = false;

		// New settings.
		$autoupdateSettings = \Boldgrid\Library\Util\Option::get( 'autoupdate' );

		// Old settings.
		$pluginAutoupdate = \Boldgrid\Library\Util\Option::get( 'plugin_autoupdate' );

		// Update if global setting is on, individual settings is on, or not set and default is on.
		if ( ! empty( $pluginAutoupdate ) ||
			! empty( $autoupdateSettings['plugins'][ $item->plugin ] ) ||
			( ! isset( $autoupdateSettings['plugins'][ $item->plugin ] ) &&
			! empty( $autoupdateSettings['plugins']['default'] ) ) ) {
				$update = true;
		}

		return $update;
	}

	/**
	 * Auto update theme.
	 *
	 * @since 2.3.0
	 *
	 * @hook: auto_update_theme
	 *
	 * @see \Boldgrid\Library\Util\Option::get()
	 *
	 * @param  bool   $update Update API response.
	 * @param  object $item   Item being updated.
	 * @return bool
	 */
	public function auto_update_theme( $update, $item ) {
		$update = false;

		// New settings.
		$autoupdateSettings = \Boldgrid\Library\Util\Option::get( 'autoupdate' );

		// Old settings.
		$themeAutoupdate = \Boldgrid\Library\Util\Option::get( 'theme_autoupdate' );

		// Update if global setting is on, individual settings is on, or not set and default is on.
		if ( ! empty( $themeAutoupdate ) ||
			! empty( $autoupdateSettings['themes'][ $item->theme ] ) ||
			( ! isset( $autoupdateSettings['themes'][ $item->theme ] ) &&
			! empty( $autoupdateSettings['themes']['default'] ) ) ) {
				$update = true;
		}

		return $update;
	}
}
