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

	public $stylesheet;

	public function __construct( \WP_Theme $wp_theme ) {
		$this->wp_theme   = $wp_theme;
		$this->stylesheet = $this->wp_theme->__get( 'stylesheet' );
		$this->version    = $this->wp_theme->__get( 'version' );
		$this->parentIs();
		$this->hasUpdate();
		$this->isActive();
		$this->updateData = new UpdateData( $this );
	}

	public function parentIs() {
		$this->parent = $this->wp_theme->parent();
		return $this->parent;
	}

	public function isActive() {
		$active_theme = wp_get_theme();
		if ( $active_theme->__get( 'stylesheet' ) === $this->stylesheet ) {
			$this->isActive = true;
		} else {
			$this->isActive = false;
		}

		return $this->isActive;
	}

	public function hasUpdate() {
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
