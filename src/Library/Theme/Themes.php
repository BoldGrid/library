<?php
/**
 * BoldGrid Library Theme Themes.
 *
 * @package Boldgrid\Theme
 *
 * @since SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Theme;

class Themes {
	/**
	 * Themes
	 *
	 * @since SINCEVERSION
	 * @var array
	 */
	public $themes;

	public function __construct() {
		$this->themes = [];
		$wp_themes = wp_get_themes();
		foreach ( $wp_themes as $wp_theme ) {
			$this->themes[] = new Theme( $wp_theme );
		}
	}

	public function list() {
		return $this->themes;
	}

	public function getFromStylesheet( $stylesheet ) {
		foreach ( $this->list() as $theme ) {
			if ( $theme->stylesheet === $stylesheet ) {
				return $theme;
			}
		}
	}

	public function getActive() {
		return new Theme( wp_get_theme() );
	}
}