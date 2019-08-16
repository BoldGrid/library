<?php
/**
 * BoldGrid Library Ui Card.
 *
 * @package Boldgrid\Library
 *
 * @version x.x.x
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

/**
 * BoldGrid Library Util Card Class.
 *
 * @since x.x.x
 */
class Card {
	/**
	 * Footer.
	 *
	 * Generally where the content is going.
	 *
	 * @since xxx
	 * @var string
	 */
	public $footer;

	/**
	 * Icon.
	 *
	 * Generally markup for a span, a dashicon.
	 *
	 * @since xxx
	 * @var string
	 */
	public $icon;

	/**
	 * Features.
	 *
	 * An array of features.
	 *
	 * @since xxx
	 * @var array
	 */
	public $features;

	/**
	 * Id.
	 *
	 * @since xxx
	 * @var string
	 */
	public $id;

	/**
	 * Sub title.
	 *
	 * @since xxx
	 * @var string
	 */
	public $subTitle;

	/**
	 * Title.
	 *
	 * @since xxx
	 * @var string
	 */
	public $title;

	/**
	 * Print the card.
	 *
	 * @since xxx
	 *
	 * @param  bool  $echo True to print the card, false to return the markup.
	 * @return mixed.
	 */
	public function print( $echo = true ) {
		// Before printing, initialize all of the features.
		foreach ( $this->features as $feature ) {
			$feature->init();
			$this->footer .= $feature->print( false );
		}

		// Create the opening tag.
		$markup = '<div class="bglib-card" ';
		if ( ! empty( $this->id ) ) {
			$markup .= 'id="' . esc_attr( $this->id ) . '" ';
		}
		$markup .= '>';

		if ( ! empty( $this->title ) ) {
			$markup .= '<div class="bglib-card-title">';

			$markup .= '<p>' . $this->title . '</p>';

			if ( ! empty( $this->subTitle ) ) {
				$markup .= '<div class="bglib-card-subtitle">' . esc_html( $this->subTitle ) . '</div>';
			}

			$markup .= '</div>';
		}

		if ( ! empty( $this->icon ) ) {
			$markup .= '<div class="bglib-card-icon">' . $this->icon . '</div>';
		}


		if ( ! empty( $this->footer ) ) {
			$markup .= '<div class="bglib-card-footer">' . $this->footer . '</div>';
		}

		$markup .= '</div>';

		if ( $echo ) {
			echo $markup;
		} else {
			return $markup;
		}
	}
}
