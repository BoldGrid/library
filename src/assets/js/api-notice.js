var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

BOLDGRID.LIBRARY.Api = function( $ ) {
	var notice,
		self = this;

	/**
	 * Set key if parameter is set.
	 */
	$( function() {
		var $activateKey = self.GetURLParameter( 'activateKey' ),
			notice = $( '#container_boldgrid_api_key_notice' );

		if ( $activateKey ) {
			document.getElementById( 'boldgrid_api_key' ).value = $activateKey;
		}

		/** Toggle the forms around **/
		$( '.boldgridApiKeyLink', notice ).on( 'click', function() {
			$( '.api-notice', notice ).hide();
			$( '.new-api-key', notice ).fadeIn( 'slow' );
		});
		$( '.enterKeyLink', notice ).on( 'click', function() {
			$( '.new-api-key', notice ).hide();
			$( '.api-notice', notice ).fadeIn( 'slow' );
		});

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
				$genericError = 'There was an error communicating with the BoldGrid Connect Key server.  Please try again.',
				$submit = $form.find( '#requestKey' ),
				$spinner = $form.find( '.spinner' );



				$('.error-color').removeClass( 'error-color' );

			// Basic js checks before server-side verification.
			if ( ! $firstName ) {
				$alertBox.text( 'First name is required.' );
				$form.find( '#firstName' ).prev().addClass( 'error-color' );
				return false;
			}
			if ( ! $lastName ) {
				$alertBox.text( 'Last name is required.' );
				$form.find( '#lastName' ).prev().addClass( 'error-color' );
				return false;
			}
			if ( ! ( $email.indexOf( '@' ) > -1 && $email.indexOf( '.' ) > -1 ) ) {
				$alertBox.text( 'Please enter a valid e-mail address.' );
				$form.find( '#emailAddr' ).prev().addClass( 'error-color' );
				return false;
			}

			$submit.prop( 'disabled', 'disabled' );
			$spinner.addClass( 'inline' );

			posting = $.post( $( '#generate-api-key' ).val(),
				{
					first: $firstName,
					last: $lastName,
					email: $email,
					link: $link,
				}
			);

			posting.done( function( response ) {
				$alertBox.text( $genericError );
				if ( 200 === response.status ) {
					$( '.key-request-content' ).text( response.message );
				}
			}).fail( function( post ) {
				var message = post.responseJSON.message
				if ( message.indexOf( 'First name' ) >= 0 ) {
					$form.find( '#firstName' ).prev().addClass( 'error-color' );
				}
				if ( message.indexOf( 'Last name' ) >= 0 ) {
					$form.find( '#lastName' ).prev().addClass( 'error-color' );
				}
				if ( message.indexOf( 'e-mail' ) >= 0 ) {
					$form.find( '#emailAddr' ).prev().addClass( 'error-color' );
				}
				$alertBox.text( message );

				$submit.prop( 'disabled', false );
				$spinner.removeClass( 'inline' );
			});
		});

		/**
		 * When the submit button is pressed.
		 */
		$( '#boldgrid-api-form' ).submit( function( e ){
			e.preventDefault();
		});

		$( '#boldgrid-api-loading', notice ).hide();

		$( '#submit_api_key', notice ).on('click', function() {
			$( '#boldgrid_api_key_notice_message' ).empty();
			if ( ! $( '#tos-box:checked').length  ) {
				$( '#boldgrid_api_key_notice_message', notice )
					.html( 'You must agree to the Terms of Service before continuing.' )
					.addClass( 'error-color' );
				return false;
			}
			var key = $( '#boldgrid_api_key', notice ).val()
				.replace( /[^a-z0-9]/gi,'' )
				.replace( /(.{8})/g,"$1\-" )
				.slice( 0, -1 );
			if ( ! key || key.length !== 35 ) {
				$( '#boldgrid_api_key_notice_message', notice )
					.html( 'You must enter a valid BoldGrid Connect Key.' )
					.addClass( 'error-color' );
				return false;
			}
			$( '#boldgrid_api_key_notice_message', notice ).removeClass( 'error-color' );

			self.set( key );

			// hide the button
			$( this ).hide();

			// show the loading graphic.
			$( '#boldgrid-api-loading', notice ).show();
		});
	});

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

	this.trackActivation = function () {
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
		var data, nonce, wpHttpReferer;
		// Get the wpnonce and referer values.
		nonce = $( '#set_key_auth', notice ).val();
		wpHttpReferer = $( '[name="_wp_http_referer"]', notice ).val();
		data = {
			'action'  : 'addKey',
			'api_key' :  key,
			'set_key_auth' : nonce,
			'_wp_http_referer' : wpHttpReferer,
		};

		$.post( ajaxurl, data, function( response ) {
			// Declare variables.
			var response, message,
				notice = $( '#container_boldgrid_api_key_notice' );

			// If the key was saved successfully.
			if ( response.success ) {
				// Change the notice from red to green.
				notice.toggleClass( 'error' ).toggleClass( 'updated' );

				// Initiate tracking iframe.
				self.trackActivation();

				$( '#boldgrid_api_key_notice_message', notice )
					.html( response.data.message + ' <a onClick="window.location.reload(true)" style="cursor:pointer;"> Dismiss Notification</a>' );

				// Remove the loading graphic since success.
				$( '#boldgrid-api-loading', notice ).fadeOut();

				// Finally hide the input elements as we do not need them anymore.
				$( '#boldgrid_api_key', notice ).fadeOut();

				// Reload page after 3 seconds.
				setTimeout( function() {
					window.location.reload();
				}, 3000 );
			} else {
				$( '#boldgrid-api-loading', notice ).hide();
				$( '#submit_api_key', notice ).show();
				$( '#boldgrid_api_key_notice_message', notice )
					.html( response.data.message )
					.addClass( 'error-color' );
			}
		});
	};
};

new BOLDGRID.LIBRARY.Api( jQuery );
