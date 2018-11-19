/* global ajaxurl,jQuery */

var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

( function( $ ) {
	BOLDGRID.LIBRARY.Connect = {

		/**
		 * Constructor.
		 *
		 * @since 2.4.0
		 */
		init: function() {
			$( self._onLoad );
		},

		/**
		 * On DOM load.
		 *
		 * @since 2.4.0
		 */
		_onLoad: function() {
			var $bgBox = $( '.bg-box' );

			self._repositionNotice();

			// Initialize jquery-toggles.
			$bgBox
				.find( '.toggle' )
				.toggles( {
					text: {
						on: '',
						off: ''
					},
					height: 15,
					width: 40
				} );

			self._setMasterToggles();

			$bgBox
				.find( '.toggle-group' )
				.on( 'click swipe contextmenu', self._toggleGroup );

			$bgBox
				.find( '.toggle' )
				.not( '.toggle-group' )
				.on( 'click swipe contextmenu', self._setMasterToggles );

			$( '#submit' ).on( 'click', self._submit );

			$bgBox.find( '.dashicons-editor-help' ).on( 'click', self._toggleHelp );

			$bgBox.find( '.bglib-collapsible-control' ).on( 'click', function() {
				$( this ).toggleClass( 'bglib-collapsible-open' );
			} );
		},

		/**
		 * Reposition the notice into the correct location.
		 *
		 * @since 2.4.0
		 */
		_repositionNotice: function() {
			var $connectKeySection = $( '.connect-key-prompt' );

			setTimeout( function() {
				$connectKeySection.after( $( '#container_boldgrid_api_key_notice' ) );
			} );
		},

		/**
		 * Set inputs for toggles.
		 *
		 * @since 2.7.0
		 */
		_setInputs: function() {
			var $bgBox = $( '.bg-box' ),
				$wpcoreToggles = $bgBox.find( '.wpcore-toggle' ),
				$pluginToggles = $bgBox.find( '.plugin-toggle' ),
				$themeToggles = $bgBox.find( '.theme-toggle' ),
				$pluginsDefault = $bgBox.find( '#toggle-default-plugins' ),
				$themesDefault = $bgBox.find( '#toggle-default-themes' );

			// If the updates section is not in use, then just return.
			if ( ! $pluginsDefault.data( 'toggles' ) ) {
				return;
			}

			$wpcoreToggles.each( function() {
				var $this = $( this );

				$this
					.next( 'input' )
					.attr( 'name', 'autoupdate[wpcore][' + $this.data( 'wpcore' ) + ']' )
					.val( $this.data( 'toggles' ).active ? 1 : 0 );
			} );

			$pluginToggles.each( function() {
				var $this = $( this );

				$this
					.parent()
					.next( 'input' )
					.attr( 'name', 'autoupdate[plugins][' + $this.data( 'plugin' ) + ']' )
					.val( $this.data( 'toggles' ).active ? 1 : 0 );
			} );

			$themeToggles.each( function() {
				var $this = $( this );

				$this
					.parent()
					.next( 'input' )
					.attr( 'name', 'autoupdate[themes][' + $this.data( 'stylesheet' ) + ']' )
					.val( $this.data( 'toggles' ).active ? 1 : 0 );
			} );

			$pluginsDefault.next( 'input' ).val( $pluginsDefault.data( 'toggles' ).active ? 1 : 0 );

			$themesDefault.next( 'input' ).val( $themesDefault.data( 'toggles' ).active ? 1 : 0 );
		},

		/**
		 * Set master toggles.
		 *
		 * @since 2.7.0
		 */
		_setMasterToggles: function() {
			var $masters = $( '.bg-box' ).find( '.toggle-group' );

			$masters.each( function() {
				var $master = $( this ),
					state = true;

				$master
					.closest( '.div-table-body' )
					.find( '.toggle' )
					.not( '.toggle-group,#toggle-default-plugins,#toggle-default-themes' )
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
		 * Toggle an entire group on/off.
		 *
		 * @since 2.7.0
		 */
		_toggleGroup: function() {
			var $this = $( this ),
				$toggles = $this
					.parent()
					.parent()
					.parent()
					.find( '.toggle' )
					.not( '#toggle-default-plugins,#toggle-default-themes' );

			$toggles.toggles( $this.data( 'toggles' ).active );

			self._setInputs();
		},

		/**
		 * Handle form submission.
		 *
		 * @since 2.7.0
		 */
		_submit: function() {
			var $this = $( this ),
				$spinner = $this.next(),
				$notice = $( '#settings-notice' ),
				$toggleDefaultPlugins = $bgBox.find( '#toggle-default-plugins' ),
				$toggleDefaultThemes = $bgBox.find( '#toggle-default-themes' );
				data = {
					action: 'boldgrid_library_connect_settings_save',
					_wpnonce: $( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: $( '[name="_wp_http_referer"]' ).val(),
					plugin_release_channel: $( 'input[name="plugin_release_channel"]:checked' ).val(),
					theme_release_channel: $( 'input[name="theme_release_channel"]:checked' ).val()
				};

			$this.attr( 'disabled', 'disabled' );

			$spinner.addClass( 'inline' );

			if ( $toggleDefaultPlugins.length ) {
				data.autoupdate.plugins['default'] = $toggleDefaultPlugins
					.data( 'toggles' )
					.active ? 1 : 0
			}

			$bgBox.find( '.plugin-update-setting .toggle' ).each( function() {
				var $this = $( this ),
					plugin = $this.data( 'plugin' ),
					value = $this.data( 'toggles' ).active ? 1 : 0;

				data.autoupdate.plugins[plugin] = value;
			} );

			if ( $toggleDefaultThemes.length ) {
				data.autoupdate.themes['default'] = $toggleDefaultThemes
					.data( 'toggles' )
					.active ? 1 : 0
			}

			$bgBox.find( '.theme-update-setting .toggle' ).each( function() {
				var $this = $( this ),
					stylesheet = $this.data( 'stylesheet' ),
					value = $this.data( 'toggles' ).active ? 1 : 0;

				data.autoupdate.themes[stylesheet] = value;
			} );

			$.post(
				ajaxurl,
				data,
				function( response ) {
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
				.fail( function( jqXHR ) {
					$notice
						.removeClass( 'notice-success' )
						.addClass( 'notice-error' )
						.html( BoldGridLibraryConnect.ajaxError + jqXHR.status + ' (' + jqXHR.statusText + ')' );
				} )
				.always( function() {
					self._replaceNotice( $notice );

					$notice.wrapInner( '<p></p>' ).show();
					$spinner.removeClass( 'inline' );
					$this.removeAttr( 'disabled' );
					$( 'body' ).trigger( 'make_notices_dismissible' );
				} );
		},

		/**
		 * Replace the notice with a clone when removed by dismissal.
		 *
		 * @since 2.7.0
		 */
		_replaceNotice: function( $notice ) {
			var $noticeClone = $notice.clone(),
				$noticeNext = $notice.next();

			$notice.one( 'click.wp-dismiss-notice', '.notice-dismiss', function() {
				$noticeNext.before( $noticeClone );
				$notice = $noticeClone;
				$notice.hide();
			} );
		},

		/**
		 * Handle form submission.
		 *
		 * @since 2.7.0
		 */
		_toggleHelp: function( e ) {
			var id = $( this ).attr( 'data-id' );

			e.preventDefault();

			if ( id === undefined ) {
				return false;
			}

			$( '.help[data-id="' + id + '"]' ).slideToggle();

			return false;
		},
	};

	var self = BOLDGRID.LIBRARY.Connect;
	BOLDGRID.LIBRARY.Connect.init();
} )( jQuery );
