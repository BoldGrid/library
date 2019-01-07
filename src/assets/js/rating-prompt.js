/**
 * Handle rating prompts in the dashboard.
 *
 * @since xxx
 */

/* global jQuery */

var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

( function( $ ) {
	'use strict';

	var self;

	BOLDGRID.LIBRARY.RatingPrompt = {
		test: 'test',

		/**
		 * @summary Dismiss (or snooze) a rating prompt.
		 *
		 * @since xxx
		 *
		 * @param string name
		 * @param string type
		 * @param string length
		 */
		dismiss: function( name, type, length ) {
			var data = {
					'action': 'blib_rating_prompt_dismiss',
					'type': type,
					'length': length,
					'name': name,
					'security': $( '.bglib-rating-prompt #_wpnonce' ).val()
				},
				successCallback,
				errorCallback;

			console.log( data );

			$.ajax( {
				url: ajaxurl,
				data: data,
				type: 'post',
				dataType: 'json',
				success: successCallback,
				error: errorCallback
			} );
		},

		/**
		 * @summary Take action when a decision is clicked.
		 */
		onClickDecision: function() {
			var $decision = $( this ),
				$slides = $( '.bglib-rating-prompt [data-slide-id]' ),
				action = $decision.attr( 'data-action' ),
				name = $decision.closest( '.bglib-rating-prompt' ).attr( 'data-slide-name' ),
				nextSlide = $decision.attr( 'data-next-slide' ),
				snooze_length;

			// Handle the toggle to another slide.
			if ( nextSlide ) {
				$slides.hide();
				$( '.bglib-rating-prompt [data-slide-id="' + nextSlide + '"]' ).show();
			}

			// Handle dismissing / snoozing.
			if ( 'dismiss' === action ) {
				//self.dismiss( name, 'dismiss', 0 );
			} else if ( 'snooze' === action ) {
				//self.dismiss( name, 'snooze', $decision.attr( 'data-snooze' ) );
			}

			if ( 0 === $decision.attr( 'href' ).length ) {
				return false;
			}
		},


	};

	self = BOLDGRID.LIBRARY.RatingPrompt;

	$( function() {
//		$( 'body' ).on( 'click', '.bglib-misc-pub-section a.edit', BOLDGRID.LIBRARY.Attributes.onClickEdit );
//		$( 'body' ).on( 'click', '.bglib-misc-pub-section a.button-cancel', BOLDGRID.LIBRARY.Attributes.onClickCancel );
//		$( 'body' ).on( 'click', '.bglib-misc-pub-section a.button', BOLDGRID.LIBRARY.Attributes.onClickOk );
//		self.initValuesDisplayed();

		$( 'body' ).on( 'click', '.notice.bglib-rating-prompt li a', BOLDGRID.LIBRARY.RatingPrompt.onClickDecision );
	} );
})( jQuery );