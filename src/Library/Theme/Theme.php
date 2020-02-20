<?php
/**
 * BoldGrid Library Theme Theme.
 *
 * @package Boldgrid\Theme
 *
 * @since SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Theme;

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
	 * @param WP_Theme
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
	public function parentIs() {
		$this->parent = $this->wp_theme->parent();
		return $this->parent;
	}

	/**
	 * Determine if this is the active theme or not.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	public function getIsActive() {
		$active_theme = wp_get_theme();
		if ( $active_theme->__get( 'stylesheet' ) === $this->stylesheet ) {
			$this->isActive = true;
		} else {
			$this->isActive = false;
		}

		return $this->isActive;
	}

	/**
	 * Determine if this theme has an update available.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	public function getHasUpdate() {
		$transient = get_site_transient( 'update_themes', [] )->response;
		$transient = null !== $transient ? $transient : [];
		if ( array_key_exists( $this->stylesheet, $transient ) ) {
			$this->hasUpdate = true;
		} else {
			$this->hasUpdate = false;
		}
		return $this->hasUpdate;
	}
}
