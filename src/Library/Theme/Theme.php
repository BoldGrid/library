<<<<<<< HEAD
<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * BoldGrid Library Theme Theme.
 *
 * Library package uses different naming convention
 * phpcs:disable WordPress.NamingConventions.ValidVariableName
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 *
=======
<?php
/**
 * BoldGrid Library Theme Theme.
 *
>>>>>>> added theme functionality
 * @package Boldgrid\Theme
 *
 * @since SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Theme;

<<<<<<< HEAD
/**
 * Theme Class.
 *
 * Stores and retrieves various data for the themes on the site.
 *
 * Boldgrid\Library\Library\Theme\Theme class.
 *
 * @since 2.12.0
 */
class Theme {
	/**
	 * WP_Theme Object
	 *
	 * @since SINCEVERSION
	 * @var WP_Theme
	 */
	public $wp_theme;

	/**
	 * Stylesheet
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $stylesheet;

	/**
	 * Version
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $version;

	/**
	 * Parent Theme
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $parent;

	/**
	 * Is Active
	 *
	 * @since SINCEVERSION
	 * @var bool
	 */
	public $isActive;

	/**
	 * Has Update
	 *
	 * @since SINCEVERSION
	 * @var bool
	 */
	public $hasUpdate;

	/**
	 * UpdateData
	 *
	 * @since SINCEVERSION
	 * @var UpdateData
	 */
	public $updateData;

	/**
	 * Constructor
	 *
	 * @since SINCEVERSION
	 *
	 * @param \WP_Theme $wp_theme The WP_Theme object for this theme.
	 */
	public function __construct( \WP_Theme $wp_theme ) {
		$this->wp_theme   = $wp_theme;
		$this->stylesheet = $this->wp_theme->__get( 'stylesheet' );
		$this->version    = $this->wp_theme->__get( 'version' );
		$this->parentIs();
		$this->getHasUpdate();
		$this->getIsActive();
		$this->updateData = new UpdateData( $this );
	}

	/**
	 * Determines the parent theme if this theme is a child theme.
	 *
	 * @since SINCEVERSION
	 *
	 * @return string
	 */
=======
class Theme {

	public function __construct( \WP_Theme $wp_theme ) {
		$this->wp_theme = $wp_theme;
		$this->stylesheet = $this->wp_theme->__get( 'stylesheet' );
		$this->version = $this->wp_theme->__get( 'version' );
		$this->parentIs();
		$this->hasUpdate();
		$this->isActive();
	}

>>>>>>> added theme functionality
	public function parentIs() {
		$this->parent = $this->wp_theme->parent();
		return $this->parent;
	}

<<<<<<< HEAD
	/**
	 * Determine if this is the active theme or not.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	public function getIsActive() {
=======
	public function isActive() {
>>>>>>> added theme functionality
		$active_theme = wp_get_theme();
		if ( $active_theme->__get( 'stylesheet' ) === $this->stylesheet ) {
			$this->isActive = true;
		} else {
			$this->isActive = false;
		}

		return $this->isActive;
	}

<<<<<<< HEAD
	/**
	 * Determine if this theme has an update available.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	public function getHasUpdate() {
		if ( get_site_transient( 'update_themes' ) ) {
			$transient = get_site_transient( 'update_themes' )->response;
		} else {
			$transient = null;
		}

		$transient = null !== $transient ? $transient : array();

		if ( array_key_exists( $this->stylesheet, $transient ) ) {
=======
	public function hasUpdate() {
		$transient = get_site_transient( 'update_themes' )->response;
		if ( array_key_exists( $this->stylesheet, $transient) ) {
>>>>>>> added theme functionality
			$this->hasUpdate = true;
		} else {
			$this->hasUpdate = false;
		}
		return $this->hasUpdate;
	}
<<<<<<< HEAD
}
=======
}
>>>>>>> added theme functionality
