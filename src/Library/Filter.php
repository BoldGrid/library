<?php
/**
 * BoldGrid Library Filter
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Filter Class.
 *
 * This class is responsible for filter/action related methods used within
 * the BoldGrid Library. Special thanks to rarst and scribu for filter ideas.
 *
 * @since 1.0.0
 */
class Filter {

	/**
	 * Adds hooks.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $class Class to add hooks for.
	 *
	 * @return null
	 */
	public static function add( $class ) {
		self::doFilter( 'add_filter', $class );
	}

	/**
	 * Remove hooks.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $class Class to remove hooks from.
	 *
	 * @return null
	 */
	public static function remove( $class ) {
		self::doFilter( 'remove_filter', $class );
	}

	/**
	 * Process hooks.
	 *
	 * This sets up our automatic filter binding.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $action Action name.
	 * @param  string $class  Class name.
	 *
	 * @return null
	 */
	private static function doFilter( $action, $class ) {
		$reflection = new \ReflectionClass( $class );
		foreach ( $reflection->getMethods() as $method ) {
			if ( $method->isPublic() && ! $method->isConstructor() ) {
				$comment = $method->getDocComment();

				// No hooks.
				if ( preg_match( '/@nohook[ \t\*\n]+/', $comment ) ) {
					continue;
				}

				// Set hook.
				preg_match_all( '/@hook:?\s+([^\s]+)/', $comment, $matches ) ? $matches[1] : $method->name;
				if ( empty( $matches[1] ) ) {
					$hooks = array( $method->name );
				} else {
					$hooks = $matches[1];
				}

				// Allow setting priority.
				$priority = preg_match( '/@priority:?\s+(\d+)/', $comment, $matches ) ? $matches[1] : 10;

				// Fire.
				foreach ( $hooks as $hook ) {
					call_user_func( $action, $hook, array( $class, $method->name ), $priority, $method->getNumberOfParameters() );
				}
			}
		}
	}
}
