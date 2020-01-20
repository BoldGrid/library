<?php //phpcs:ignore WordPress.Files.FileName
/**
 * BoldGrid Library Ui Premium Listing.
 *
 * @package Boldgrid\Library
 *
 * @version SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Dashboard Class.
 *
 * @since SINCEVERSION
 */
class Premium {
	/**
	 * Cards.
	 *
	 * @since SINCEVERSION
	 * @var array
	 */
	public $cards = [];

	/**
	 * Enqueue scripts.
	 *
	 * @since SINCEVERSION
	 */
	public static function enqueueScripts() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName
		wp_enqueue_style( 'bglib-premium',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/premium.css'
		);
	}

	/**
	 * Print Cards.
	 *
	 * @since SINCEVERSION
	 */
	public function printCards() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName
		echo '<div class="bglib-premium-card-container">';

		foreach ( $this->cards as $card ) {
			$card->init();
			$card->printCard();
		}

		echo '</div>';
	}
}
