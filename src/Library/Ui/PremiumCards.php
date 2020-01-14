<?php
/**
 * Premium Cards class.
 *
 * @link       https://www.boldgrid.com
 * @since      1.11.0
 *
 * @package    Boldgrid\Backup
 * @subpackage Boldgrid\Backup\Card
 * @copyright  BoldGrid
 * @author     BoldGrid <support@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

/**
 * Class: Premiums
 *
 * This class is responsible for rendering the Premium features cards on this plugin's dashboard.
 *
 * @since 1.11.0
 */
class Premiums extends \Boldgrid\Library\Library\Ui\Card {

    /**
	 * Link.
	 *
	 * Markup for a Premium card's "Setup Guide" link.
	 *
	 * @since 2.10.0
	 * @var array
	 */
	public $link;

	public function printCard( $echo = true ) {
        // Before printing, initialize all of the features.
        if ( ! empty( $this->features ) ) {
            foreach ( $this->features as $feature ) {
                $feature->init();
                $this->footer .= $feature->printFeature( false );
            }
        }

		// Create the opening tag.
		$markup = '<div class="bglib-premiums-card" ';
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
		
		if ( ! empty( $this->link ) ) {
			$markup .= '<div class="bglib-card-link"><a href="' . $this->link["url"] . '">' . $this->link["text"] . '</a></div>';
		}

		$markup .= '</div>';

		if ( $echo ) {
			echo $markup;
		} else {
			return $markup;
		}
	}

}
