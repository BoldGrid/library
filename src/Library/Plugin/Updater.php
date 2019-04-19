<?php
/**
 * BoldGrid Library Plugin Updater.
 *
 * @package Boldgrid\Plugin
 *
 * @since 2.9.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

use Boldgrid\Library\Library\Configs;

/**
 * Updater class.
 *
 * This class handles functions needed for updating plugins.
 *
 * @since 2.9.0
 */
class Updater extends Plugin {
	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 *
	 * @param string $slug For example, "plugin" from plugin/plugin.php
	 */
	public function __construct( $slug ) {
		parent::__construct( $slug );
	}

	/**
	 * Get update markup.
	 *
	 * Our update markup is a div containing the current version of the plugin, a link to see what's
	 * new, and a link to upgrade the plugin.
	 *
	 * It mimics the notice shown for a plugin in Dashboard > Plugins when an update is available.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function getMarkup() {
		$markup = '<div class="update-message notice inline notice-warning notice-alt">
			<p data-slug="' . esc_attr( $this->slug ) . '" data-plugin="' . esc_attr( $this->file ) . '" data-nonce="' . esc_attr( wp_create_nonce( 'updates' ) ) . '">';

		/*
		 * Get the markup required to display:
		 * There is a new version of PLUGIN available. View version VERSION details or update now.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.1.1/wp-admin/includes/update.php#L432-L449
		 */
		$markup .= sprintf(
			/* translators: 1: plugin name, 2: details URL, 3: additional link attributes, 4: version number, 5: update URL, 6: additional link attributes */
			__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.', 'boldgrid-library' ),
			$this->getData( 'Name' ),
			esc_url( $this->getDetailsUrl() ),
			sprintf(
				'class="thickbox open-plugin-details-modal" aria-label="%s"',
				/* translators: 1: plugin name, 2: version number */
				esc_attr( sprintf( __( 'View %1$s version %2$s details', 'boldgrid-library' ), $this->getData( 'Name' ), $this->getNewVersion() ) )
			),
			$this->getNewVersion(),
			wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->file, 'upgrade-plugin_' . $this->file ),
			sprintf(
				'class="update-link bglib-update-now" aria-label="%s"',
				/* translators: %s: plugin name */
				esc_attr( sprintf( __( 'Update %s now', 'boldgrid-library' ), $this->getData( 'Name' ) ) )
			)
		);

		$markup .= '</p></div>';

		// If we're getting the markup to update a plugin, enqueue the necessary scripts.
		$this->enqueue_ajax_updates();

		return $markup;
	}

	/**
	 * Enqueue scripts needed for ajaxy plugin updates.
	 *
	 * @since 2.9.0
	 */
	public function enqueue_ajax_updates() {
		$handle = 'bglib-plugin-updater-js';

		wp_register_script(
			$handle,
			Configs::get( 'libraryUrl' ) . 'src/assets/js/plugin-updater.js',
			'jquery'
		);

		wp_localize_script(
			$handle,
			'BoldGridLibraryPluginUpdater',
			array(
				'updated'      => esc_html__( 'Updated!', 'boldgrid-library' ),
				'updateFailed' => esc_html__( 'Update Failed:', 'boldgrid-library' ),
				'updating'     => esc_html__( 'Updating...', 'boldgrid-library' ),
				'upToDate'     => esc_html__( 'Up to Date', 'boldgrid-library' ),
			)
		);

		wp_enqueue_script( $handle);
	}
}
