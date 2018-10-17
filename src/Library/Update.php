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
	 * Auto-update settings.
	 *
	 * @since 2.6.0
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Constructor.
	 *
	 * @since 2.3.0
	 *
	 * @see \Boldgrid\Library\Util\Option::get()
	 */
	public function __construct() {
		Filter::add( $this );

		$this->settings = (array) \Boldgrid\Library\Util\Option::get( 'autoupdate' );
	}

	/**
	 * Auto update WordPress Core: All types.
	 *
	 * @since 2.6.0
	 *
	 * @hook: auto_update_core
	 *
	 * @param  bool   $update Update API response.
	 * @return bool
	 */
	public function auto_update_core( $update ) {
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		return $update || ! empty( $this->settings['wpcore']['all'] );
	}

	/**
	 * Auto update WordPress Core: Major Updates.
	 *
	 * @since 2.6.0
	 *
	 * @hook: allow_major_auto_core_updates
	 *
	 * @param  bool   $update Update API response.
	 * @return bool
	 */
	public function allow_major_auto_core_updates( $update ) {
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		return $update || ! empty( $this->settings['wpcore']['all'] ) ||
			! empty( $this->settings['wpcore']['major'] );
	}

	/**
	 * Auto update WordPress Core: Minor Updates.
	 *
	 * @since 2.6.0
	 *
	 * @hook: allow_minor_auto_core_updates
	 *
	 * @param  bool   $update Update API response.
	 * @return bool
	 */
	public function allow_minor_auto_core_updates( $update ) {
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		return $update || ! empty( $this->settings['wpcore']['all'] ) ||
			! empty( $this->settings['wpcore']['minor'] );
	}

	/**
	 * Auto update WordPress Core: Development Updates.
	 *
	 * @since 2.6.0
	 *
	 * @hook: allow_dev_auto_core_updates
	 *
	 * @param  bool   $update Update API response.
	 * @return bool
	 */
	public function allow_dev_auto_core_updates( $update ) {
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		return $update || ! empty( $this->settings['wpcore']['all'] ) ||
			! empty( $this->settings['wpcore']['dev'] );
	}

	/**
	 * Auto update WordPress Core: Translation Updates.
	 *
	 * @since 2.6.0
	 *
	 * @hook: auto_update_translation
	 *
	 * @param  bool   $update Update API response.
	 * @return bool
	 */
	public function auto_update_translation( $update ) {
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		return $update || ! empty( $this->settings['wpcore']['all'] ) ||
			! empty( $this->settings['wpcore']['translation'] );
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
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		// Old settings.
		$pluginAutoupdate = \Boldgrid\Library\Util\Option::get( 'plugin_autoupdate' );

		// Update if global setting is on, individual settings is on, or not set and default is on.
		if ( ! empty( $pluginAutoupdate ) ||
			! empty( $this->settings['plugins'][ $item->plugin ] ) ||
			( ! isset( $this->settings['plugins'][ $item->plugin ] ) &&
			! empty( $this->settings['plugins']['default'] ) ) ) {
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
	 * @param  bool   $update Update API response.
	 * @param  object $item   Item being updated.
	 * @return bool
	 */
	public function auto_update_theme( $update, $item ) {
		if ( ! apply_filters( 'Boldgrid\Library\Update\isEnalbed', false ) ) {
			return $update;
		}

		// Old settings.
		$themeAutoupdate = \Boldgrid\Library\Util\Option::get( 'theme_autoupdate' );

		// Update if global setting is on, individual settings is on, or not set and default is on.
		if ( ! empty( $themeAutoupdate ) ||
			! empty( $this->settings['themes'][ $item->theme ] ) ||
			( ! isset( $this->settings['themes'][ $item->theme ] ) &&
			! empty( $this->settings['themes']['default'] ) ) ) {
				$update = true;
		}

		return $update;
	}
}
