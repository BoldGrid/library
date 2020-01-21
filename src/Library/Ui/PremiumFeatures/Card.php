<?php //phpcs:ignore WordPress.Files.FileName
/**
 * Premiums class.
 *
 * @link       https://www.boldgrid.com
 * @since      SINCEVERSION
 *
 * @package    Boldgrid\Backup
 * @subpackage Boldgrid\Backup\Card
 * @copyright  BoldGrid
 * @author     BoldGrid <support@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui\PremiumFeatures;

/**
 * Class: Premiums
 *
 * This class is responsible for rendering the Premium features cards on this plugin's dashboard.
 *
 * @since SINCEVERSION
 */
class Card extends \Boldgrid\Library\Library\Ui\Card {

	/**
	 * Link.
	 *
	 * Markup for a Premium card's "Setup Guide" link.
	 *
	 * @since SINCEVERSION
	 * @var array
	 */
	public $link;

	/**
	 * Video.
	 *
	 * Markup for a Premium card's Learn More Video button.
	 *
	 * @since SINCEVERSION
	 * @var string
	 */
	public $learn_more;

	/**
	 * Prints Cards.
	 *
	 * Markup for a Printing a Premium card.
	 *
	 * @param bool $echo whether or not to actually print.
	 * @since SINCEVERSION
	 * @var string
	 */
	public function printCard( $echo = true ) {
		// Create the opening tag.
		$markup = '<div class="bglib-premiums-card" ';
		if ( ! empty( $this->id ) ) {
			$markup .= 'id="' . esc_attr( $this->id ) . '" ';
		}
		$markup .= '>';

		if ( \Boldgrid\Library\Library\NoticeCounts::isUnread( 'boldgrid-backup-premium-features', $this->id ) ) {
			$markup .= '<div class="card-ribbon"><span>NEW</span></div>';
		}

		if ( ! empty( $this->title ) ) {
			$markup .= '<div class="bglib-card-title">';

			$markup .= '<p>' . $this->title . '</p>';

			if ( ! empty( $this->sub_title ) ) {
				$markup .= '<div class="bglib-card-subtitle">' . esc_html( $this->sub_title ) . '</div>';
			}

			$markup .= '</div>';
		}

		if ( ! empty( $this->icon ) ) {
			$markup .= '<div class="bglib-card-icon">' . $this->icon . '</div>';
		}

		if ( ! empty( $this->footer ) ) {
			$markup .= '<div class="bglib-card-footer">' . $this->footer . '</div>';
		}

		if ( ! empty( $this->link ) || ! empty( $this->learn_more ) ) {

			$markup .= '<div class="bglib-card-after-footer">';

			if ( ! empty( $this->learn_more ) ) {
				$markup .= '<a href =" ' . $learn_more . '" target="_blank" class="button button-primary boldgrid-orange bglib-card-button"> ' .
						'<span class="dashicons dashicons-video-alt3"></span>Learn More</a>';
			}

			if ( ! empty( $this->link ) ) {
				$markup .= '<a href="' . $this->link['url'] . '" target="_blank" class="bglib-card-link">' . $this->link['text'] . '</a>';
			}
			$markup .= '</div>';
		}

		$markup .= '</div>';

		if ( $echo ) {
			echo $markup; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		} else {
			return $markup;
		}
	}

}
