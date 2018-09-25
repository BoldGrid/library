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
			$( '.toggle' ).toggles( {
				text: {
				    on: '',
				    off: ''
				  }
			} );
			self._setMasterToggles();
			$( '.toggle-group' ).on( 'click swipe contextmenu', self._toggleGroup );
			$( '.toggle' ).not( '.toggle-group' )
				.on( 'click swipe contextmenu', self._setMasterToggles );
			$( '#submit' ).on( 'click', self._submit );
			$( '.dashicons-editor-help' ).on( 'click', self._toggleHelp );
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
		 * Set inputs for toggles.
		 *
		 * @since 2.5.0
		 */
		_setInputs: function() {
			var $wpcoreToggles = $( '.wpcore-toggle' ),
				$pluginToggles = $( '.plugin-toggle' ),
				$themeToggles = $( '.theme-toggle' ),
				$pluginsDefault = $( '#toggle-default-plugins' ),
				$themesDefault = $( '#toggle-default-themes' );

			$wpcoreToggles.each( function() {
				var $this = $( this );

				$this.next( 'input' )
					.attr( 'name', 'autoupdate[wpcore][' + $this.data( 'wpcore' ) + ']' )
					.val( $this.data( 'toggles' ).active ? 1 : 0 );
			} );

			$pluginToggles.each( function() {
				var $this = $( this );

				$this.next( 'input' )
					.attr( 'name', 'autoupdate[plugins][' + $this.data( 'plugin' ) + ']' )
					.val( $this.data( 'toggles' ).active ? 1 : 0 );
			} );

			$themeToggles.each( function() {
				var $this = $( this );

				$this.next( 'input' )
					.attr( 'name', 'autoupdate[themes][' + $this.data( 'stylesheet' ) + ']' )
					.val( $this.data( 'toggles' ).active ? 1 : 0 );
			} );

			$pluginsDefault
				.next( 'input' )
				.val( $pluginsDefault.data( 'toggles' ).active ? 1 : 0 );

			$themesDefault
				.next( 'input' )
				.val( $themesDefault.data( 'toggles' ).active ? 1 : 0 );
		},

		/**
		 * Set master toggles.
		 *
		 * @since 2.5.0
		 */
		_setMasterToggles: function() {
			var $masters = $( '.toggle-group' );

			$masters.each( function() {
				var $master = $( this ),
					state = true;

				$master
					.closest( '.div-table-body' )
					.find( '.toggle' )
					.not( '.toggle-group' )
					.each( function() {
						if ( ! state || ! $( this ).data( 'toggles' ).active ) {
							state = false;
						}
					} );

					$master.toggles( state );
			} );

			self._setInputs();
		},

		/**
		 * Handle form submission.
		 *
		 * @since 2.5.0
		 */
		_toggleGroup: function() {
			var $this = $( this ),
				$toggles = $this.parent().parent().find( '.toggle' );

			$toggles.toggles( $this.data( 'toggles' ).active );

			self._setInputs();
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
					_wp_http_referer: $( '[name="_wp_http_referer"]' ).val(),
					plugin_release_channel: $( 'input[name="plugin_release_channel"]:checked' ).val(),
					theme_release_channel: $( 'input[name="theme_release_channel"]:checked' ).val()
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
		},

		/**
		 * Handle form submission.
		 *
		 * @since 2.5.0
		 */
		_toggleHelp: function( e ) {
			var id = $( this ).attr( 'data-id' );

			e.preventDefault();

			if ( id === undefined ) {
				return false;
			}

			$( '.help[data-id="' + id + '"]' ).slideToggle();

			return false;
		}
	};

	var self = BOLDGRID.LIBRARY.Connect;
	BOLDGRID.LIBRARY.Connect.init();
} )( jQuery );
