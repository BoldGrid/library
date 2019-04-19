<?php
/**
 * BoldGrid Library Notifications Dashboard Widget Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 2.9.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Notifications;

use Boldgrid\Library\Library\Configs;
use Boldgrid\Library\Library\Filter;
use Boldgrid\Library\Library\Plugin\Updater;

/**
 * BoldGrid Library Notifications Dashboard Widget Class.
 *
 * @since 2.9.0
 */
class DashboardWidget {
	/**
	 * Initialize class and set class properties.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Display our Dashboard widget.
	 *
	 * The following visual should help show how things are setup:
	 * ----
	 * BoldGrid Notifications
	 * # Item
	 * ## <div $attributes
	 * ### sub items
	 * ## </div>
	 *
	 * @since 2.9.0
	 */
	public function displayWidget() {
		$items = $this->getItems();

		foreach( $items as $item ) {
			// Print our container for this item.
			echo '<div ';
			foreach( $item['wrapper']['attributes'] as $attribute => $value ) {
				echo $attribute . '="' . esc_attr( $value ) . '" ';
			}
			echo '>';

			echo implode( '', $item['subItems'] );

			// Close our container.
			echo '</div>';
		}
	}

	/**
	 * Get all items.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function getItems() {
		$items = array();

		$items = array_merge( $items, $this->getItemsPlugins() );

		return $items;
	}

	/**
	 * Get BoldGrid Plugin items.
	 *
	 * Each active BoldGrid plugin will be an item within the widget. This method loops through all
	 * active BoldGrid plugins and gets their item.
	 *
	 * @since 2.9.0
	 *
	 * @return array.
	 */
	public function getItemsPlugins() {
		$items = array();

		$activePlugins = $this->getActivePlugins();
		foreach( $activePlugins as $plugin ) {
			$items[] = $this->getItemPlugin( $plugin );
		}

		return $items;
	}

	/**
	 * Get our active plugins.
	 *
	 * Active plugins will be displayed in the dashboard widget.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function getActivePlugins() {
		// Get an array of plugins that should be within the widget.
		$plugins = Configs::getPlugins( array(
			'inNotificationsWidget' => true,
		) );

		// Then filter that array of plugins to get our active plugins.
		$activePlugins = array();
		foreach( $plugins as $plugin ) {
			if ( $plugin->isActive() ) {
				$activePlugins[] = $plugin;
			}
		}
		unset( $plugins );

		return $activePlugins;
	}

	/**
	 * Get the item for a single plugin.
	 *
	 * @since 2.9.0
	 *
	 * @param Boldgrid\Library\Library\Plugin\Plugin $plugin
	 * @return array
	 */
	public function getItemPlugin( $plugin ) {
		$subItems = array();

		// Add the heading.
		$subItems[] = '<p><strong>' . esc_html( $plugin->getData( 'Name' ) ) . '</strong></p>';

		// Adding the plugin's version info.
		$subItems[] =
		'<div style="text-align:right;">
			<span class="bglib-plugin-version">' .
				sprintf(
					__( 'Version %1$s', 'boldgrid-library' ),
					$plugin->getData( 'Version' )
				) . '
			</span> -
			<span class="bglib-version-status">' .
				( ! $plugin->hasUpdate() ? esc_html__( 'Up to Date', 'boldgrid-library' ) : esc_html__( 'Update Available', 'boldgrid-library' ) ) . '
			</span>
		</div>';

		// If applicable, add a notice in which the user can upgrade the plugin.
		if ( $plugin->hasUpdate() ) {
			$updater = new Updater( $plugin->getSlug() );
			$subItems[] = $updater->getMarkup();
		}

		$item = array(
			'type'         => 'plugin',
			'subItems'     => $subItems,
			'wrapper' => array(
				'attributes' => array(
					'class'       => 'bglib-plugin-notifications',
					'data-plugin' => $plugin->getFile(),
				),
			)
		);

		return $item;
	}

	/**
	 * Setup our dashboard widget.
	 *
	 * @since 2.9.0
	 *
	 * @link https://codex.wordpress.org/Dashboard_Widgets_API
	 */
	public function wp_dashboard_setup() {
		wp_add_dashboard_widget(
			'boldgrid-notifications',
			__( 'BoldGrid Notifications', 'boldgrid-library' ),
			array( $this, 'displayWidget' )
		);
	}
}
