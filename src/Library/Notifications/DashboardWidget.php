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
	 * ## icon | title | version
	 * ## sub items
	 *
	 * @since 2.9.0
	 */
	public function displayWidget() {
		$items = $this->getItems();

		foreach( $items as $item ) {
			if ( empty( $item['wrapper']['attributes']['class'] ) ) {
				$item['wrapper']['attributes']['class'] = 'bglib-item';
			} else {
				$item['wrapper']['attributes']['class'] .= ' bglib-item';
			}

			// Generate the attributes of our div container.
			$attributes = '';
			foreach( $item['wrapper']['attributes'] as $attribute => $value ) {
				$attributes .= $attribute . '="' . esc_attr( $value ) . '" ';
			}

			echo '
			<div ' . $attributes . '>
				<div class="bglib-icon-container">' .
					$item['icon'] . '
				</div>
				<div class="bglib-title-container">
					<p class="bglib-title">' . esc_html( $item['title'] ) . '</p>
				</div>
				<div class="bglib-version-container">
					<p class="bglib-version ' . esc_attr( $item['version']['class'] ) . '"> ' . $item['version']['markup'] . '</p>
				</div>
				<div style="clear:both;"></div>
				<div class="bglib-subitems">'
					. implode( '', $item['subItems'] ) . '
				</div>
			</div>';
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
				$icon          = '<span class="dashicons dashicons-admin-network"></span>';
				$versionClass  = 'dashicons-before dashicons-warning';
				$versionMarkup = '
					<a href="' . admin_url( 'options-general.php?page=boldgrid-connect.php' ) . '">' .
						esc_html( 'Please install your Connect Key', 'boldgrid-library' ) . '
					</a>';
				$subItems[]    = '
					<p>
						<a href="' . esc_url( Configs::get( 'getNewKey' ) ) . '">' . esc_html__( 'Click here to access BoldGrid Central and obtain a key', 'boldgrid-library' ) . '</a>
					</p>';
				break;
			case 'Free':
				$icon          = '<span class="dashicons dashicons-admin-network boldgrid-orange"></span>';
				$versionClass  = 'dashicons-before dashicons-yes';
				$versionMarkup = __( 'Free Connect Key Installed', 'boldgrid-library' );
				$subItems[]    = '
					<p>
						<a href="' . esc_url( Configs::get( 'learnMore' ) ) . '">' .
							esc_html__( 'Learn about the advanced features of a Premium Key.', 'boldgrid-library' ) . '
						</a>
					</p>';
				break;
			case 'Premium':
				$icon          = '<span class="dashicons dashicons-admin-network boldgrid-orange"></span>';
				$versionClass  = 'dashicons-before dashicons-yes';
				$versionMarkup = __( 'Premium Connect Key Installed', 'boldgrid-library' );
				break;
		}

		$item = array(
			'type'     => 'key',
			'icon'     => $icon,
			'version'  => array(
				'markup' => $versionMarkup,
				'class'  => $versionClass,
			),
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

		$icons = $plugin->getIcons();
		$icon  = empty( $icons ) ? '<span class="dashicons dashicons-admin-plugins"></span>' : '<img src="' . array_values( $icons )[0] . '" />';

		// Adding the plugin's version info.
		$versionMarkup = '<span class="bglib-version-status">';
		switch( $plugin->hasUpdate() ) {
			case true:
				$versionMarkup .= __( 'Update Available', 'boldgrid-library' );
				$versionClass   = 'dashicons-before dashicons-warning';

				// Add the markup to upgdate the plugin.
				$updater    = new Updater( $plugin->getSlug() );
				$subItems[] = $updater->getMarkup();
				break;
			case false:
				$versionMarkup .= __( 'Up to Date', 'boldgrid-library' );
				$versionClass   = 'dashicons-before dashicons-yes';
				break;
		}
		$versionMarkup .= '</span>';

		$item = array(
			'type'     => 'plugin',
			'title'    => $plugin->getData( 'Name' ),
			'version'  => array(
				'markup' => $versionMarkup,
				'class'  => $versionClass,
			),
			'subItems' => $subItems,
			'wrapper'  => array(
				'attributes' => array(
					'class'       => 'bglib-plugin-notifications',
					'data-plugin' => $plugin->getFile(),
				),
			),
			'icon'    => $icon,
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
