/**
 * Handle rating prompts in the dashboard.
 *
 * @since 2.7.7
 */

/* global ajaxurl, jQuery */

var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

( function( $ ) {
	'use strict';

	var self;

	BOLDGRID.LIBRARY.RatingPrompt = {
		/**
		 * @summary Dismiss (or snooze) a rating prompt.
		 *
		 * @since 2.7.7
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
					'security': $( '.boldgrid-library-rating-prompt #_wpnonce' ).val()
				};

			$.ajax( {
				url: ajaxurl,
				data: data,
				type: 'post',
				dataType: 'json'
			} );
		},

		/**
		 * @summary Take action when a decision is clicked.
		 *
		 * @since 2.7.7
		 */
		onClickDecision: function() {
			var $decision = $( this ),
				$slides = $( '.boldgrid-library-rating-prompt [data-slide-id]' ),
				action = $decision.attr( 'data-action' ),
				name = $decision.closest( '.boldgrid-library-rating-prompt' ).attr( 'data-slide-name' ),
				nextSlide = $decision.attr( 'data-next-slide' );

			// Handle the toggle to another slide.
			if ( nextSlide ) {
				$slides.hide();
				$( '.boldgrid-library-rating-prompt [data-slide-id="' + nextSlide + '"]' ).show();
			}

			// Handle dismissing / snoozing.
			if ( 'dismiss' === action ) {
				self.dismiss( name, 'dismiss', 0 );
			} else if ( 'snooze' === action ) {
				self.dismiss( name, 'snooze', $decision.attr( 'data-snooze' ) );
			}

			if ( 0 === $decision.attr( 'href' ).length ) {
				return false;
			}
		},
	};

	self = BOLDGRID.LIBRARY.RatingPrompt;

	$( function() {
		$( 'body' ).on( 'click', '.notice.boldgrid-library-rating-prompt li a', BOLDGRID.LIBRARY.RatingPrompt.onClickDecision );
	} );
})( jQuery );