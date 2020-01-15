<?php
/**
 * Premiums class.
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

	/**
	 * Video.
	 *
	 * Markup for a Premium card's Learn More Video button.
	 *
	 * @since 2.10.0
	 * @var string
	 */
	public $learn_more;

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
		
		if ( ! empty($this->link) || ! empty($this->learn_more)) {

			$markup .= '<div class="bglib-card-after-footer">';

			if ( ! empty( $this->learn_more ) ) {
				$markup .= '<a href =" ' . $learn_more .'" class ="button button-primary boldgrid-orange bglib-card-button"> ' . 
						'<span class="dashicons dashicons-video-alt3"></span>Learn More</a>';
			}

			if ( ! empty( $this->link ) ) {
				$markup .= '<a href="' . $this->link["url"] . '" class="bglib-card-link">' . $this->link["text"] . '</a>';
			}
			$markup .= '</div>';
		}

		$markup .= '</div>';

		if ( $echo ) {
			echo $markup;
		} else {
			return $markup;
		}
	}

}
