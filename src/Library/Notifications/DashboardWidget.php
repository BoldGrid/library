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
use Boldgrid\Library\Library\Plugin\Plugins;
use Boldgrid\Library\Library\Filter;
use Boldgrid\Library\Library\License;
use Boldgrid\Library\Library\Plugin\Updater;
use Boldgrid\Library\Library\Ui\Dashboard;

/**
 * BoldGrid Library Notifications Dashboard Widget Class.
 *
 * This class is responsible for rendering the "BoldGrid Notifications" widget on the WordPress
 * dashboard.
 *
 * @since xxx
 */
class DashboardWidget {
	/**
	 * Initialize class and set class properties.
	 *
	 * @since xxx
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since xxx
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
	 * Print the "BoldGrid Notifications" WordPress Dashboard widget.
	 *
	 * @since xxx
	 */
	public function printWidget() {
		Dashboard::enqueueScripts();

		$card = new \Boldgrid\Library\Library\Ui\Card();

		// Add all of our active plugins.
		$activePlugins = Plugins::getActive();
		foreach( $activePlugins as $plugin ) {
			$card->features[] = $this->getFeaturePlugin( $plugin );
		}

		// Add a "feature" for free / premium.
		$card->features[] = $this->getFeatureKey();

		$card->print();
	}

	/**
	 * Get the "feature" for our BoldGrid Connect Key.
	 *
	 * @since xxx
	 *
	 * @return \Boldgrid\Library\Library\Ui\Feature
	 */
	public function getFeatureKey( ) {
		$feature = new \Boldgrid\Library\Library\Ui\Feature();

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

		$feature->title = esc_html__( 'BoldGrid Connect Key', 'boldgrid-backup' );

		switch( $licenseString ) {
			case 'None':
				$feature->icon    = '<span class="dashicons dashicons-admin-network"></span>';
				$feature->content = '<div class="notice notice-warning inline"><p><a href="' . admin_url( 'options-general.php?page=boldgrid-connect.php' ) . '">' . esc_html__( 'Please install your Connect Key', 'boldgrid-library' ) . '</a></p></div>';
				break;
			case 'Free':
				$feature->icon    = '<span class="dashicons dashicons-admin-network boldgrid-orange"></span>';
				$feature->content = '<div class="notice notice-warning inline"><p><a href="' . esc_url( Configs::get( 'learnMore' ) ) . '">' .	esc_html__( 'Learn about the advanced features of a Premium Key.', 'boldgrid-library' ) . '</a></p></div>';
				break;
			case 'Premium':
				$feature->icon    = '<span class="dashicons dashicons-admin-network boldgrid-orange"></span>';
				$feature->content = esc_html__( 'Premium Connect Key Installed!', 'boldgrid-library' );
				break;
		}

		return $feature;
	}

	/**
	 * Get the "feature" for a plugin.
	 *
	 * @since xxx
	 *
	 * @param  \Boldgrid\Library\Library\Plugin\Plugin A plugin object.
	 * @return \Boldgrid\Library\Library\Ui\Feature    A feature object.
	 */
	public function getFeaturePlugin( $plugin ) {
		// Get the markup for the plugin's icon.
		$icons = $plugin->getIcons();
		if ( empty( $icons ) ) {
			$icon = '<span class="dashicons dashicons-admin-plugins"></span>';
		} else {
			$iconUrl = ! empty( $icons['1x'] ) ? $icons['1x'] : reset( $icons );
			$icon = '<img src="' . esc_url( $iconUrl ) . '" />';
		}

		$feature = new \Boldgrid\Library\Library\Ui\Feature();

		$feature->title = $plugin->getData( 'Name' );

		$feature->icon = $icon;

		if ( $plugin->hasUpdate() ) {
			$feature->content = '<div class="notice notice-warning inline"><p>' . wp_kses(
				sprintf(
					__( '%1$s is out of date. %2$sFix this%3$s.', 'boldgrid-backup' ),
					$plugin->getData( 'Name' ),
					'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">',
					'</a>'
				),
				[ 'a' => [ 'href' => [] ] ]
			) . '</p></div>';
		}

		/**
		 * Allow items to be filtered.
		 *
		 * @since xxx
		 *
		 * @param \Boldgrid\Library\Library\Ui\Feature    The feature object.
		 * @param \Boldgrid\Library\Library\Plugin\Plugin The plugin object.
		 */
		$feature = apply_filters( 'Boldgrid\Library\Notifications\DashboardWidget\getFeaturePlugin\\' . $plugin->getSlug(), $feature, $plugin );

		if ( empty( $feature->content ) ) {
			$feature->content = __( 'No issues to report!', 'boldgrid-library' );
		}

		return $feature;
	}


	/**
	 * Setup our dashboard widget.
	 *
	 * @since xxx
	 *
	 * @link https://codex.wordpress.org/Dashboard_Widgets_API
	 */
	public function wp_dashboard_setup() {
		wp_add_dashboard_widget(
			'boldgrid-notifications',
			__( 'BoldGrid Notifications', 'boldgrid-library' ),
			array( $this, 'printWidget' )
		);
	}
}
