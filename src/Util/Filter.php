<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid\Premium
 * @copyright BoldGrid.com
 * @version 1.0.0
 * @author BoldGrid <support@boldgrid.com>
 */

namespace Boldgrid\Library\Util;

/**
 * BoldGrid Library Filter Class.
 */
class Filter {

	/**
	 * Removes an anonymous object filter.
	 *
	 * @global $wp_filter WordPress filter global.
	 *
	 * @param  string $tag      Hook name.
	 * @param  string $class    Class name
	 * @param  string $method   Method name
	 * @param  int    $priority Filter priority.
	 *
	 * @return bool             Success of removing filter.
	 */
	public static function remove( $tag, $class, $name, $priority = 10 ) {
		global $wp_filter;

		// Check that filter exists.
		if ( isset( $wp_filter[ $tag ] ) ) {

			/**
			 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
			 * a simple array, and it is an object that implements the ArrayAccess interface.
			 *
			 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated).
			 *
			 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
			 */
			if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
				// Create $fob object from filter tag, to use below.
				$fob = $wp_filter[ $tag ];
				$callbacks = &$wp_filter[ $tag ]->callbacks;
			} else {
				$callbacks = &$wp_filter[ $tag ];
			}

			// Exit if there aren't any callbacks for specified priority.
			if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
				return false;
			}

			// Loop through each filter for the specified priority, looking for our class & method.
			foreach( ( array ) $callbacks[ $priority ] as $filter_id => $filter ) {

				// Filter should always be an array - array( $this, 'method' ), if not goto next.
				if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
					continue;
				}

				// If first value in array is not an object, it can't be a class.
				if ( ! is_object( $filter['function'][0] ) ) {
					continue;
				}

				// Method doesn't match the one we're looking for, goto next.
				if ( $filter['function'][1] !== $name ) {
					continue;
				}

				// Callback method matched, so check class.
				if ( get_class( $filter['function'][0] ) === $class ) {

					// WordPress 4.7+ use core remove_filter() since we found the class object.
					if ( isset( $fob ) ) {

						// Handles removing filter, reseting callback priority keys mid-iteration, etc.
						$fob->remove_filter( $tag, $filter['function'], $priority );
					} else {
						// Use legacy removal process (pre 4.7).
						unset( $callbacks[ $priority ][ $filter_id ] );
						// If it was the only filter in that priority, unset that priority.
						if ( empty( $callbacks[ $priority ] ) ) {
							unset( $callbacks[ $priority ] );
						}
						// If the only filter for that tag, set the tag to an empty array.
						if ( empty( $callbacks ) ) {
							$callbacks = array();
						}
						// Remove this filter from merged_filters, which specifies if filters have been sorted.
						unset( $GLOBALS['merged_filters'][ $tag ] );
					}

					return true;
				}
			}
		}

		return false;
	}
}
