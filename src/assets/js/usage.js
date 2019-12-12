/**
 * Usage class.
 *
 * @summary This file handles usage statistics.
 *
 * @since SINCEVERSION
 */

/* global jQuery,ga,gtag */

var BOLDGRID = BOLDGRID || {};

BOLDGRID.LIBRARY = BOLDGRID.LIBRARY || {};

(function($) {
	'use strict';

	var self;

	/**
	 * Total Upkeep Usage.
	 *
	 * @since SINCEVERSION
	 */
	BOLDGRID.LIBRARY.Usage = {
		/**
		 * Add our client id to all boldgrid.com links.
		 *
		 * @since SINCEVERSION
		 */
		addClientId: function() {},

		/**
		 * Prepend all boldgrid.com links with our user's GA client id.
		 *
		 * @since SINCEVERSION
		 *
		 * @link https://www.analyticsmania.com/post/google-analytics-cross-domain-tracking-with-google-tag-manager/
		 * @link https://developers.google.com/analytics/devguides/collection/gtagjs/cross-domain
		 */
		prependClientId: function() {
			var clientId = self.getClientId(),
				part;

			// Abort if we can't get a client id.
			if ('' === clientId) {
				return;
			}

			part = '_ga=' + clientId;

			$('a[href*="www.boldgrid.com"]').each(function() {
				var $anchor = $(this),
					url = $anchor.attr('href');

				/*
				 * Add our client id to the url.
				 *
				 * @link https://stackoverflow.com/questions/486896/adding-a-parameter-to-the-url-with-javascript
				 */
				url =
					url.indexOf('?') != -1
						? url.split('?')[0] + '?' + part + '&' + url.split('?')[1]
						: url.indexOf('#') != -1
						? url.split('#')[0] + '?' + part + '#' + url.split('#')[1]
						: url + '?' + part;

				$anchor.attr('href', url);
			});
		},

		/**
		 * Get the user's client id.
		 *
		 * @since SINCEVERSION
		 */
		getClientId: function() {
			var clientId = '';

			ga.getAll().forEach(tracker => {
				if (tracker.get('trackingId') === self.i18n.ga_id) {
					clientId = tracker.get('clientId');
				}
			});

			return clientId;
		},

		/**
		 * Get the page path we will use to track the pageview.
		 *
		 * @since SINCEVERSION
		 *
		 * @return string
		 */
		getPagePath: function() {
			return '/user-domain/wp-admin/admin.php?page=' + self.i18n.page;
		},

		/**
		 * i18n.
		 *
		 * @since SINCEVERSION
		 *
		 * @type object
		 */
		i18n: window.BglibUsage || {},

		/**
		 * Init.
		 *
		 * @since SINCEVERSION
		 */
		init: function() {
			self._onReady();
		},

		/**
		 * Actions to take when a user clicks a nav item of the bglib UI class.
		 *
		 * @since SINCEVERSION
		 */
		onNavClick: function() {
			var pageviewParams = {
				page_path: self.getPagePath() + '&section=' + $(this).attr('data-section-id')
			};

			self.triggerPageview(pageviewParams);
		},

		/**
		 * Trigger a pageview.
		 *
		 * @since SINCEVERSION
		 *
		 * @link https://www.simoahava.com/analytics/add-clientid-to-custom-dimension-gtag-js/
		 *
		 * @param object params An object containing params for the gtag call.
		 */
		triggerPageview: function(params) {
			if (params === undefined) {
				params = {
					page_path: self.getPagePath()
				};
			}

			params.custom_map = {
				dimension7: 'license'
			};

			gtag('config', self.i18n.ga_id, params);

			gtag('event', 'license_demension', { license: self.i18n.license });
		},

		/**
		 * On ready.
		 *
		 * @since 1.7.0
		 */
		_onReady: function() {
			$(function() {
				// Log the pageview.
				self.triggerPageview();

				self.prependClientId();

				// Listen to clicks on the bglib UI's nav.
				$('.bg-left-nav li').on('click', self.onNavClick);
			});
		}
	};

	self = BOLDGRID.LIBRARY.Usage;
})(jQuery);

BOLDGRID.LIBRARY.Usage.init();
