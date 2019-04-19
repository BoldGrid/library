/**
 * Handle updating plugins via ajax.
 *
 * This script is used in conjunction with Boldgrid\Library\Library\Plugin\Updater\getMarkup().
 *
 * @since 2.9.0
 */

/* global jQuery, wp, BoldGridLibraryPluginUpdater */

var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

( function( $ ) {
	'use strict';

	var self;

	BOLDGRID.LIBRARY.PluginUpdater = {
		/**
		 * Event handler for click of "Update".
		 *
		 * @since 2.9.0
		 *
		 * @param event e
		 */
		onClickUpdate: function( e ) {
			var $link          = jQuery( this ),
				$container     = $link.closest( '.bglib-plugin-notifications' ),
				$div           = $link.closest( 'div' ),
				$p             = $link.closest( 'p' ),
				$version       = $container.find( '.bglib-plugin-version' ),
				$versionStatus = $container.find( '.bglib-version-status' ),
				plugin         = $p.attr( 'data-plugin' ),
				slug           = $p.attr( 'data-slug' ),
				nonce          = $p.attr( 'data-nonce' );

			e.preventDefault();

			// UI changes to show an update is in progress.
			$div.addClass( 'updating-message' );
			$p.text( self.lang.updating );

			// Make the ajax call to update the plugin.
			wp.updates.ajax(
				'update-plugin',
				{
					action: 'update-plugin',
					plugin: plugin,
					slug: slug,
					_ajax_nonce: nonce,

					/**
					 * Success function.
					 *
					 * @since 2.9.0
					 */
					success: function( response ) {
						$div
							.addClass( 'updated-message notice-success' )
							.removeClass( 'notice-warning updating-message' );
						$p.text( self.lang.updated );
						$version.text( response.newVersion );
						$versionStatus.text( self.lang.upToDate );
					},

					/**
					 * Error function.
					 *
					 * @since 2.9.0
					 */
					error: function( response ) {
						$div
							.addClass( 'notice-error' )
							.removeClass( 'notice-warning updating-message' );
						$p.text( self.lang.updateFailed + ' ' + response.errorMessage );
					}
				}
			);
		},
	};

	self = BOLDGRID.LIBRARY.PluginUpdater;

	self.lang = BoldGridLibraryPluginUpdater;

	$( function() {
		$( document ).on( 'click', '.bglib-update-now', self.onClickUpdate );
	} );
})( jQuery );