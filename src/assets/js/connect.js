/* global ajaxurl,jQuery */

var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

( function ( $ ) {
	BOLDGRID.LIBRARY.Connect = {

		/**
		 * Constructor.
		 *
		 * @since 2.4.0
		 */
		init: function () {
			$( self._onLoad );
		},

		/**
		 * On DOM load.
		 *
		 * @since 2.4.0
		 */
		_onLoad: function() {
			self._repositionNotice();

			// Initialize jquery-toggles.
			$( '.toggle' ).toggles();

			$( '.toggle-group' ).on( 'click', self._toggleGroup );

			$( '#submit' ).on( 'click', self._submit );
		},

		/**
		 * Reposition the notice into the correct location.
		 *
		 * @since 2.4.0
		 */
		_repositionNotice: function() {
			var $connectKeySection = $( '.connect-key-prompt' );

			setTimeout( function () {
				$connectKeySection.after( $( '#container_boldgrid_api_key_notice' ) );
			} );
		},

		/**
		 * Handle form submission.
		 *
		 * @since 2.5.0
		 */
		_toggleGroup: function() {
			var $this = $( this ),
				$toggles = $this.parent().parent().find( '.toggle' ),
				masterToggleState = $this.data('toggles').active;

			$toggles.toggles( masterToggleState );
		},

		/**
		 * Handle form submission.
		 *
		 * @since 2.5.0
		 */
		_submit: function() {
			var $this = $( this ),
				$pluginUpdateSettings = $( '.plugin-update-setting .toggle' ),
				$themeUpdateSettings = $( '.theme-update-setting .toggle' ),
				$spinner = $this.next(),
				$notice = $( '#settings-notice' ),
				data = {
					action: 'boldgrid_library_connect_settings_save',
					_wpnonce: $( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: $( '[name="_wp_http_referer"]' ).val()
				};

			data.autoupdate = {};
			data.autoupdate.plugins = {};
			data.autoupdate.themes = {};

			$this.attr( 'disabled', 'disabled' );

			$spinner.addClass( 'inline' );

			$pluginUpdateSettings.each( function() {
				var $this = $( this ),
					plugin = $this.data( 'plugin' ),
					value = $this.data( 'toggles' ).active ? 1 : 0;

				data.autoupdate.plugins[ plugin ] = value;
			} );

			$themeUpdateSettings.each( function() {
				var $this = $( this ),
					stylesheet = $this.data( 'stylesheet' ),
					value = $this.data( 'toggles' ).active ? 1 : 0;

				data.autoupdate.themes[ stylesheet ] = value;
			} );

			jqxhr = $.post( ajaxurl, data, function( response ) {
				if ( response.success !== undefined && true === response.success ) {
					$notice
						.removeClass( 'notice-error' )
						.addClass( 'notice-success' )
						.html( BoldGridLibraryConnect.settingsSaved );
				} else if ( response.data !== undefined && response.data.error !== undefined ) {
					$notice
						.removeClass( 'notice-success' )
						.addClass( 'notice-error' )
						.html( response.data.error );

					$this.removeAttr( 'disabled' );
				} else {
					$notice
						.removeClass( 'notice-success' )
						.addClass( 'notice-error' )
						.html( BoldGridLibraryConnect.unknownError );
					$this.removeAttr( 'disabled' );
				}
				},
				'json'
			)
				.fail( function() {
					$notice
						.removeClass( 'notice-success' )
						.addClass( 'notice-error' )
						.html( BoldGridLibraryConnect.ajaxError + jqxhr.status + ' (' + jqxhr.statusText + ')' );
				} )
				.always( function() {
					$notice.wrapInner( '<p></p>' ).show();
					$spinner.removeClass( 'inline' );
					$this.removeAttr( 'disabled' );
				} );
		}
	};

	var self = BOLDGRID.LIBRARY.Connect;
	BOLDGRID.LIBRARY.Connect.init();
} )( jQuery );
