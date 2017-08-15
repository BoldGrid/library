<?php
/**
 * BoldGrid Library Release Channel Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Util;

/**
 * BoldGrid Library Release Channel Class.
 *
 * This class is responsible for retrieving and setting the release channel the
 * user has selected for plugin and theme updates.
 *
 * @since 1.0.0
 */
class ReleaseChannel {

	/**
	 * @var string $pluginChannel The plugin release channel set for the user.
	 * @var string $themeChannel  The theme release channel set for the user.
	 */
	private
		$pluginChannel,
		$themeChannel;

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setPluginChannel();
		$this->setThemeChannel();
		Filter::add( $this );
	}

	/**
	 * Sets the pluginChannel class property.
	 *
	 * @since 1.0.0
	 *
	 * @return object $this Sets $pluginChannel class property.
	 */
	private function setPluginChannel() {
		return $this->pluginChannel = Util\Option::get( 'release_channel' ) ? Util\Option::get( 'release_channel' ) : 'stable';
	}

	/**
	 * Sets the themeChannel class property.
	 *
	 * @since 1.0.0
	 *
	 * @return object $this Sets $themeChannel class property.
	 */
	private function setThemeChannel() {
		return $this->themeChannel = Util\Option::get( 'theme_release_channel' ) ? Util\Option::get( 'theme_release_channel' ) : 'stable';
	}

	/**
	 * Update Plugin Channel
	 *
	 * This methods fires when boldgrid_settings is updated.  We check the values
	 * for the new plugin release channel to see if it changed here.
	 *
	 * @since 1.0.0
	 *
	 * @hook: update_site_option_boldgrid_settings
	 *
	 * @param string $option The option name.
	 * @param mixed  $new    New option value being set.
	 * @param mixed  $old    Old option value being set.
	 *
	 * @return mixed $new    The new option being set.
	 */
	public function updateChannel( $option, $new, $old ) {

		// Plugin checks.
		if ( ! empty( $old['release_channel'] ) || ! empty( $new['release_channel'] ) ) {
			if ( $old['release_channel'] !== $new['release_channel'] ) {
				Util\Option::deletePluginTransients();
				wp_update_plugins();
			}
		}

		// Theme checks.
		if ( ! empty( $old['theme_release_channel'] ) || ! empty( $new['theme_release_channel'] ) ) {
			if ( $old['theme_release_channel'] !== $new['theme_release_channel'] ) {

				/**
				 * Action to take when theme release channel has changed.
				 *
				 * @since 1.1
				 *
				 * @param type string $old Old theme release channel.
				 * @param type string $new New theme release channel.
				 */
				do_action( 'Boldgrid\Library\Library\ReleaseChannel\theme_channel_updated', $old['theme_release_channel'], $new['theme_release_channel'] );

				delete_site_transient( 'boldgrid_api_data' );
				delete_site_transient( 'update_themes' );
				wp_update_themes();
			}
		}

		return $new;
	}

	/**
	 * Gets the pluginChannel class property.
	 *
	 * @since 1.0.0
	 *
	 * @nohook
	 *
	 * @return string $pluginChannel Plugin Channel that is set.
	 */
	public function getPluginChannel() {
		return $this->pluginChannel;
	}

	/**
	 * Gets the themeChannel class property.
	 *
	 * @since 1.0.0
	 *
	 * @nohook
	 *
	 * @return string $themeChannel Theme Channel that is set.
	 */
	public function getThemeChannel() {
		return $this->themeChannel;
	}
}
