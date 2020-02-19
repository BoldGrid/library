<<<<<<< HEAD
<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Theme Themes.
 *
 * Library package uses different naming convention
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
=======
<?php
/**
 * BoldGrid Library Theme Themes.
 *
>>>>>>> added theme functionality
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
		$this->themes = array();
		$wp_themes    = wp_get_themes();
		foreach ( $wp_themes as $wp_theme ) {
			$this->themes[] = new Theme( $wp_theme );
		}
	}

<<<<<<< HEAD
<<<<<<< HEAD
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
=======
=======
	public function __invoke() {
		return $this->themes;
	}

>>>>>>> added additional theme update functionality
	public function list() {
		return $this->themes;
	}

	public function getFromStylesheet( $stylesheet ) {
		foreach ( $this->list() as $theme ) {
>>>>>>> added theme functionality
			if ( $theme->stylesheet === $stylesheet ) {
				return $theme;
			}
		}
	}

<<<<<<< HEAD
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
=======
	public function getActive() {
		return new Theme( wp_get_theme() );
	}
}
>>>>>>> added theme functionality
