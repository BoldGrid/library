<?php
/**
 * BoldGrid Library Ui Feature.
 *
 * @package Boldgrid\Library
 *
 * @version x.x.x
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

/**
 * BoldGrid Library Util Feature Class.
 *
 * @since x.x.x
 */
class Feature {
	/**
	 * Feature icon.
	 *
	 * Generally, markup for a span, such as a dashicon.
	 *
	 * @since xxx
	 * @var string
	 */
	public $icon;

	/**
	 * Feature title.
	 *
	 * @since xxx
	 * @var string
	 */
	public $title;

	/**
	 * Feature content.
	 *
	 * @since xxx
	 * @var string
	 */
	public $content;

	/**
	 * Print this feature.
	 *
	 * @since xxx
	 *
	 * @param  bool  $echo Whether to echo the feature now, or return the markup.
	 * @retrun mixed
	 */
	public function print( $echo = true ) {
		$markup = '<div class="bglib-feature">
			<div class="bglib-feature-icon">
				' . $this->icon . '
			</div>
			<div class="bglib-feature-right">
				<div class="bglib-feature-title">' . esc_html( $this->title ) . '</div>
				<div class="bglib-feature-content">' . $this->content . '</div>
			</div>
		</div>';

		if ( $echo ) {
			echo $markup;
		}

		return $echo ? true : $markup;
	}
}
