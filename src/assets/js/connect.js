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
			self._repositionNotice();
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
		}
	};

	var self = BOLDGRID.LIBRARY.Connect;
	BOLDGRID.LIBRARY.Connect.init();
} )( jQuery );
