var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

BOLDGRID.LIBRARY.Api = function( $ ) {
	var notice,
		self = this;

	self.lang = BoldGridLibraryApiNotice;

	/**
	 * Set key if parameter is set.
	 */
	$( function() {
		var $activateKey = self.GetURLParameter( 'activateKey' );
		notice = $( '#container_boldgrid_api_key_notice' );

		if ( $activateKey ) {
			document.getElementById( 'boldgrid_api_key' ).value = $activateKey;
		}

		/** Toggle the forms around **/
		$( '.boldgridApiKeyLink', notice ).on( 'click', function() {
			$( '.api-notice', notice ).hide();
			$( '.new-api-key', notice ).fadeIn( 'slow' );
		} );
		$( notice ).on( 'click', '.enterKeyLink', function() {
			$( '.new-api-key', notice ).hide();
			$( '.api-notice', notice ).fadeIn( 'slow' );
		} );

		/** Submit action **/
		$( '#requestKeyForm' ).submit( function( event ) {
			event.preventDefault();

			var posting,
				$form = $( this ),
				$firstName = $form.find( '#firstName' ).val(),
				$lastName = $form.find( '#lastName' ).val(),
				$email = $form.find( '#emailAddr' ).val(),
				$link = $form.find( '#siteUrl' ).val(),
				$alertBox = $( '.error-alerts' ),
				$genericError = self.lang.errorCommunicating,
				$submit = $form.find( '#requestKey' ),
				$spinner = $form.find( '.spinner' ),
				$tos = $form.find( '#requestTos' );

			$( '.error-color' ).removeClass( 'error-color' );

			// Basic js checks before server-side verification.
			if ( ! $firstName ) {
				$alertBox.text( self.lang.firstRequired );
				$form
					.find( '#firstName' )
					.prev()
					.addClass( 'error-color' );
				return false;
			}
			if ( ! $lastName ) {
				$alertBox.text( self.lang.lastRequired );
				$form
					.find( '#lastName' )
					.prev()
					.addClass( 'error-color' );
				return false;
			}
			if ( ! ( -1 < $email.indexOf( '@' ) && -1 < $email.indexOf( '.' ) ) ) {
				$alertBox.text( self.lang.emailRequired );
				$form
					.find( '#emailAddr' )
					.prev()
					.addClass( 'error-color' );
				return false;
			}
			if ( ! $tos.prop( 'checked' ) ) {
				$alertBox.text( self.lang.tosRequired );
				$tos.closest( 'label' ).addClass( 'error-color' );
				return false;
			}

			$submit.prop( 'disabled', 'disabled' );
			$spinner.addClass( 'inline' );

			posting = $.post( $( '#generate-api-key' ).val(), {
				first: $firstName,
				last: $lastName,
				email: $email,
				link: $link
			} );

			posting
				.done( function( response ) {
					$alertBox.text( $genericError );
					if ( 200 === response.status ) {
						$( '.key-request-content' )
							.text( response.message )
							.append( '<p><a href="#" class="enterKeyLink">' + self.lang.clickEnterKey + '</a></p>' );
					}
				} )
				.fail( function( post ) {
					var message = post.responseJSON.message;
					if ( 0 <= message.indexOf( 'First name' ) ) {
						$form
							.find( '#firstName' )
							.prev()
							.addClass( 'error-color' );
					}
					if ( 0 <= message.indexOf( 'Last name' ) ) {
						$form
							.find( '#lastName' )
							.prev()
							.addClass( 'error-color' );
					}
					if ( 0 <= message.indexOf( 'e-mail' ) ) {
						$form
							.find( '#emailAddr' )
							.prev()
							.addClass( 'error-color' );
					}
					$alertBox.text( message );

					$submit.prop( 'disabled', false );
					$spinner.removeClass( 'inline' );
				} );
		} );

		/**
		 * When the submit button is pressed.
		 */
		$( '#boldgrid-api-form' ).submit( function( e ) {
			e.preventDefault();
		} );

		/**
		 * Handle the "Submit" button click.
		 */
		$( '#submit_api_key', notice ).on( 'click', function() {
			var $submitButton = $( this ),
				$spinner = $submitButton.parent().find( '.spinner' );

			$( '#boldgrid_api_key_notice_message' ).empty();

			// Require the TOS box be checked.
			if ( ! $( '#tos-box:checked' ).length ) {
				$( '#boldgrid_api_key_notice_message', notice )
					.html( self.lang.tosRequired )
					.addClass( 'error-color' );
				return false;
			}

			// Validate the connect key.
			var key = $( '#boldgrid_api_key', notice )
				.val()
				.replace( /[^a-z0-9]/gi, '' )
				.replace( /(.{8})/g, '$1-' )
				.slice( 0, -1 );
			if ( ! key || 35 !== key.length ) {
				$( '#boldgrid_api_key_notice_message', notice )
					.html( self.lang.keyRequired )
					.addClass( 'error-color' );
				return false;
			}
			$( '#boldgrid_api_key_notice_message', notice ).removeClass( 'error-color' );

			self.set( key );

			// Disable the submit button and show the spinner.
			$submitButton.attr( 'disabled', true );
			$spinner.addClass( 'inline' );
		} );

		self._setupChangeKey();
	} );

	/**
	 * When a user clicks on change connect key, change the presentation to key input.
	 *
	 * @since 2.4.0
	 */
	this._setupChangeKey = function() {
		notice.find( 'a[data-action="change-connect-key"]' ).on( 'click', function( e ) {
			e.preventDefault();
			notice.attr( 'data-notice-state', 'no-key-added' );
		} );
	};

	/**
	 * Get parameter from URL
	 *
	 * @link http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
	 */
	this.GetURLParameter = function( sParam ) {
		var sPageURL, sURLVariables, sParameterName;

		sPageURL = window.location.search.substring( 1 );
		sURLVariables = sPageURL.split( '&' );

		for ( var i = 0; i < sURLVariables.length; i++ ) {
			sParameterName = sURLVariables[i].split( '=' );

			if ( sParameterName[0] == sParam ) {
				return sParameterName[1];
			}
		}
	};

	this.trackActivation = function() {

		// Create iframe element.
		var iframe = document.createElement( 'iframe' );

		// Assign iframe ID.
		iframe.setAttribute( 'id', 'tracking' );

		// Assign iframe width.
		iframe.setAttribute( 'width', 0 );

		// Assign iframe height.
		iframe.setAttribute( 'height', 0 );

		// Assign iframe tabindex.
		iframe.setAttribute( 'tabindex', -1 );

		// Place iframe before response message.
		var el = document.getElementById( 'boldgrid_api_key_notice_message' );
		el.parentNode.insertBefore( iframe, el );

		// Assign src URL to iframe.
		iframe.setAttribute( 'src', 'https://www.boldgrid.com/activation/' );

		// Set display:none to iframe;
		iframe.style.display = 'none';
	};

	/**
	 * Set the API key.
	 */
	this.set = function( key ) {
		var data, nonce, wpHttpReferer, $noticeContainer, $spinner, fail, success;

		// Get the wpnonce and referer values.
		nonce = $( '#set_key_auth', notice ).val();
		wpHttpReferer = $( '[name="_wp_http_referer"]', notice ).val();
		data = {
			action: 'addKey',
			api_key: key,
			set_key_auth: nonce,
			_wp_http_referer: wpHttpReferer
		};

		$noticeContainer = $( '#container_boldgrid_api_key_notice' );

		$spinner = $noticeContainer.find( '.api-notice .spinner' );

		// The Connect key was not validated successfully.
		fail = function( message ) {
			message = message || self.lang.unexpectedError;

			// Hide the spinner and enable the submit button.
			$spinner.removeClass( 'inline' );
			$( '#submit_api_key', $noticeContainer ).attr( 'disabled', false );

			// Display the error message to the user.
			$( '#boldgrid_api_key_notice_message', $noticeContainer )
				.html( message )
				.addClass( 'error-color' );
		};

		success = function( response ) {
			var message = response.data.message,
				isKeypromptMini = $( '.keyprompt-mini', $noticeContainer );

			// Change the notice from red to green, and hide the form.
			if ( ! isKeypromptMini.length ) {
				$noticeContainer
					.toggleClass( 'error' )
					.toggleClass( 'updated' )
					.addClass( 'success-add-key' );

				message += ' <a class="notice-dismiss" onClick="window.location.reload(true)" style="cursor:pointer;"></a>';
				$( '.tos-box', $noticeContainer ).fadeOut();
			}

			// Initiate tracking iframe.
			self.trackActivation();

			$( '#boldgrid_api_key_notice_message', $noticeContainer ).html( message );

			$spinner.fadeOut();

			// Hide the prompt and show the success message.
			$noticeContainer
				.hide()
				.before( '<div class="notice notice-success is-dismissible bg-key-saved" style="display:block;"><p>' + message + '</p></div>' );

			// Trigger an event, for others to do things.
			$( 'body' )
				.addClass( 'boldgrid-key-saved' )
				.trigger( 'boldgrid-key-saved', response.data );

			if ( typeof IMHWPB !== 'undefined' && typeof IMHWPB.configs !== 'undefined' ) {
				IMHWPB.configs.api_key   = response.data.api_key;
				IMHWPB.configs.site_hash = response.data.site_hash;
			}

			/*
			 * Reload page after 2 seconds, except if:
			 * 1. This is a mini key entry prompt.
			 * 2. The notice has the no-refresh class (may be dyanmically added by other scripts).
			 */
			if ( ! isKeypromptMini.length && ! $noticeContainer.hasClass( 'no-refresh' ) ) {
				setTimeout( function() {
					window.location.reload();
				}, 2000 );
			}
		}

		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				success( response );
			} else {
				fail( response.data ? response.data.message : null );
			}
		} ).fail( function() {
			fail();
		} );
	};
};

new BOLDGRID.LIBRARY.Api( jQuery );
