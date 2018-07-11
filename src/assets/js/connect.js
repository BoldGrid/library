/* global ajaxurl,jQuery */

var BOLDGRID = BOLDGRID || {};
BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

( function ( $ ) {
	BOLDGRID.LIBRARY.Connect = {

		/**
		 * Constructor.
		 *
		 * @since X.X.X
		 */
		init: function () {
			$( self._onLoad );
		},

		/**
		 * On DOM load.
		 *
		 * @since X.X.X
		 */
		_onLoad: function() {
			self._repositionNotice();
		},

		/**
		 * Reposition the notice into the correct location.
		 *
		 * @since X.X.X
		 */
		_repositionNotice: function() {
			var $connectKeySection = $( '.connect-key-prompt' );

			setTimeout( function () {
				$connectKeySection.after( $( '#container_boldgrid_api_key_notice' ) );
			} );
		}
	};

	var self = BOLDGRID.LIBRARY.Connect;
	BOLDGRID.LIBRARY.Connect.init();
} )( jQuery );
