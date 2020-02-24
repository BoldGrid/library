<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Theme Themes.
 *
 * Library package uses different naming convention
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
 * @package Boldgrid\Theme
 *
 * @since SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Theme;

/**
 * Themes Class.
 *
 * This class stores all Theme objects and helps retrieve them.
 * Boldgrid\Library\Library\Theme\Theme class.
 *
 * @since 2.12.0
 */
class Themes {
	/**
	 * Themes array
	 *
	 * @since SINCEVERSION
	 * @var array
	 */
	public $themes;

	/**
	 * Constructor
	 *
	 * @since SINCEVERSION
	 */
	public function __construct() {
		$this->themes = [];
		$wp_themes    = wp_get_themes();
		foreach ( $wp_themes as $wp_theme ) {
			$this->themes[] = new Theme( $wp_theme );
		}
	}

	/**
	 * Returns an array of Theme objects
	 *
	 * @since SINCEVERSION
	 *
	 * @return array
	 */
	public function getList() {
		return $this->themes;
	}

	/**
	 * Get Theme from Stylesheet
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $stylesheet Theme's Stylesheet.
	 * @return Theme
	 */
	public function getFromStylesheet( $stylesheet ) {
		foreach ( $this->getList() as $theme ) {
			if ( $theme->stylesheet === $stylesheet ) {
				return $theme;
			}
		}
	}

	/**
	 * Get the Active Theme
	 *
	 * @since SINCEVERSION
	 *
	 * @return Theme
	 */
	public function getActive() {
		return new Theme( wp_get_theme() );
	}
}
