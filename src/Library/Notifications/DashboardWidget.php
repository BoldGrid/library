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
use Boldgrid\Library\Library\License;
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
	 * Enqueue scripts.
	 *
	 * @since 2.9.0
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style(
			'bglib-dashboard-widget-css',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/dashboard-widget.css'
		);

		switch( $hook ) {
			case 'index.php':
				wp_enqueue_style( 'bglib-dashboard-widget-css' );
				break;
		}
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

		// Some items will not have a wrapper. Setup the default.
		$defaultWrapper = array(
			'attributes' => array(),
		);

		foreach( $items as $item ) {
			$item['wrapper'] = empty( $item['wrapper'] ) ? $defaultWrapper : $item['wrapper'];

			// Generate the attributes of our div container.
			$attributes = '';
			foreach( $item['wrapper']['attributes'] as $attribute => $value ) {
				$attributes .= $attribute . '="' . esc_attr( $value ) . '" ';
			}

			// Print our container for this item.
			echo '<div ' . $attributes . '>';

			if ( ! empty( $item['title'] ) ) {
				echo '<p><strong>' . esc_html( $item['title'] ) . '</strong></p>';
			}

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
		// Add plugins.
		$items = $this->getItemsPlugins();

		// Add Connect Key.
		array_push( $items, $this->getItemKey() );

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
	 * Get our "BoldGrid Connect Key" item for the dashboard widget.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function getItemKey( ) {
		$subItems = array();

		/*
		 * Get our license string: "None", "Free", "Premium".
		 *
		 * Be default, the license string is based on a single product's license. Instead of using
		 * this logic, below we are trying to figure out a key's license (and not a product's license).
		 *
		 * To get the license, we need to check if a particular product is premium. To do that, we
		 * will set our product to be the plugin that is loading the library.
		 */
		$license = new License();
		$key     = $license->getApiKey();
		if ( empty( $key ) ) {
			$licenseString = 'None';
		} else {
			$product = Configs::getFileSlug();
			// Call isPermium(), which sets the license string.
			$license->isPremium( $product );
			$licenseString = $license->getLicenseString();
		}

		switch( $licenseString ) {
			case 'None':
				$subItems[] = '
				<p>
					<span class="dashicons dashicons-admin-network"></span>
					<a href="' . admin_url( 'options-general.php?page=boldgrid-connect.php' ) . '">' .
						esc_html( 'Please install your Connect Key', 'boldgrid-library' ) . '
					</a>
				</p>';
				$subItems[] = '
				<p>
					<a href="' . esc_url( Configs::get( 'getNewKey' ) ) . '">' . esc_html__( 'Click here to access BoldGrid Central and obtain a key', 'boldgrid-library' ) . '</a>
				</p>';
				break;
			case 'Free':
				$subItems[] = '
				<p>
					<span class="dashicons dashicons-admin-network boldgrid-orange"></span> ' .
					esc_html( 'Free Connect Key Installed', 'boldgrid-library' ) . '
				</p>';
				$subItems[] = '
				<p>
					<a href="' . esc_url( Configs::get( 'learnMore' ) ) . '">' .
						esc_html__( 'Learn about the advanced features of a Premium Key.', 'boldgrid-library' ) . '
					</a>
				</p>';
				break;
			case 'Premium':
				$subItems[] = '
				<p>
					<span class="dashicons dashicons-admin-network boldgrid-orange"></span> ' .
					esc_html( 'Premium Connect Key Installed', 'boldgrid-library' ) . '
				</p>';
				break;
		}

		$item = array(
			'type'     => 'key',
			'subItems' => $subItems,
			'title'    => 'BoldGrid Connect Key',
		);

		return $item;
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
