<?php
/**
 * BoldGrid Library Ui Card.
 *
 * @package Boldgrid\Library
 *
 * @version 2.10.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

/**
 * BoldGrid Library Util Card Class.
 *
 * @since 2.10.0
 */
class Card {
	/**
	 * Footer.
	 *
	 * Generally where the content is going.
	 *
	 * @since 2.10.0
	 * @var string
	 */
	public $footer;

	/**
	 * Icon.
	 *
	 * Generally markup for a span, a dashicon.
	 *
	 * @since 2.10.0
	 * @var string
	 */
	public $icon;

	/**
	 * Features.
	 *
	 * An array of features.
	 *
	 * @since 2.10.0
	 * @var array
	 */
	public $features;

	/**
	 * Id.
	 *
	 * @since 2.10.0
	 * @var string
	 */
	public $id;

	/**
	 * Sub title.
	 *
	 * @since 2.10.0
	 * @var string
	 */
	public $subTitle;

	/**
	 * Title.
	 *
	 * @since 2.10.0
	 * @var string
	 */
	public $title;

	/**
	 * Print the card.
	 *
	 * @since 2.10.0
	 *
	 * @param  bool  $echo True to print the card, false to return the markup.
	 * @return mixed.
	 */
	public function printCard( $echo = true ) {
		// Before printing, initialize all of the features.
		foreach ( $this->features as $feature ) {
			$feature->init();
			$this->footer .= $feature->printFeature( false );
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
