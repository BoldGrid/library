<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}
?><style>
#container_boldgrid_api_key_notice .dashicons-before:before {
	padding-right: .8em !important;
	font-size: 58px !important;
	color: #ff6600;
}

#container_boldgrid_api_key_notice {
	width: 563px;
	padding: 1.4em;
}
#requestKeyForm  input {
	width: 83%;
	margin: .5em 0;
}
#requestKeyForm label {
	display: block;
}

#boldgrid_api_key_notice_message,
.tos-box,
.key-request-content,
.enterKeyLink,
.boldgridApiKeyLink {
	margin-left: 66px;
}
#requestKeyMessage {
	margin-right: 66px;
}
.error-alerts {
	color: red;
}
.error-color::before {
	content: '*';
	padding-right: 5px;
}
.error-color {
	color: red;
}

input#boldgrid_api_key {
	margin-left: 66px !important;
	width: 59%;
}

.boldgrid-wp-spin {
	width: 20px;
	height: 20px;
	float: left;
	border-radius: 100%;
	background-color: #a3a3a3;
	-webkit-animation: rotation 1.5s infinite linear;
	-moz-animation: rotation 1.5s infinite linear;
	-animation: rotation 1.5s infinite linear;
}

.boldgrid-wp-spin::after {
	width: 4px;
	height: 4px;
	border-radius: 100%;
	background-color: #fff;
	position: relative;
	left: 10px;
	top: 5px;
	display: block;
	content: '';
}

@
-webkit-keyframes rotation {from { -webkit-transform:rotate(0deg);

}

to {
	-webkit-transform: rotate(359deg);
}

}
@
-moz-keyframes rotation {from { -moz-transform:rotate(0deg);

}

to {
	-moz-transform: rotate(359deg);
}

}
@
-keyframes rotation {from { -transform:rotate(0deg);

}

to {
	-transform: rotate(359deg);
}

}
#boldgrid-api-loading {
	margin-left: 423px;
	margin-top: -2em;
	display: none;
}
</style>
<script type="text/javascript">

var IMHWPB = IMHWPB || {};

IMHWPB.Api = function( configs ) {
	( function( $ ) {
		var notice, self = this;

		/**
		 * Set key if parameter is set.
		 */
		$( function() {
			var $activateKey = self.GetURLParameter( 'activateKey' ),
			    container = $( 'container_boldgrid_api_key_notice' );
			if ( $activateKey ) {
				document.getElementById( 'boldgrid_api_key' ).value = $activateKey;
			}
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

		notice = $( '#container_boldgrid_api_key_notice' );

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
		$( "#requestKeyForm" ).submit( function( event ) {
			event.preventDefault();

			var posting,
				$form = $( this ),
				$firstName = $form.find( '#firstName' ).val(),
				$lastName = $form.find( '#lastName' ).val(),
				$email = $form.find( '#emailAddr' ).val(),
				$link = $form.find( '#siteUrl' ).val(),
				$alertBox = $( '.error-alerts' ),
				$genericError = 'There was an error communicating with the BoldGrid Connect Key server.  Please try again.';


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
			});
		});

		/**
		 * Bind events.
		 *
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
				var response, message;

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
	})( jQuery );
};

new IMHWPB.Api();
</script>
