<?php
/**
 * BoldGrid Library Usage Helper Class.
 *
 * @package Boldgrid\Library
 *
 * @version SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Usage;

/**
 * BoldGrid Library Usage Helper Class.
 *
 * @since SINCEVERSION
 */
class Helper {
	/**
	 * Whether or not page in the admin.php?page= begins with a prefix.
	 *
	 * @return bool True if the page begins with one of the prefixes.
	 */
	public static function hasPagePrefix() {
		$prefixes      = apply_filters( 'Boldgrid\Library\Usage\getPrefixes', [] );
		$hasPagePrefix = false;
		$page          = ! empty( $_GET['page'] ) ? $_GET['page'] : null;

		foreach ( $prefixes as $prefix ) {
			if ( substr( $page, 0, strlen( $prefix ) ) === $prefix ) {
				$hasPagePrefix = true;
			}
		}

		return $hasPagePrefix;
	}
}
