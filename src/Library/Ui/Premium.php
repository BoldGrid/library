<?php //phpcs:ignore WordPress.Files.FileName
/**
 * BoldGrid Library Ui Premium Listing.
 *
 * @package Boldgrid\Library
 *
 * @version 2.11.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Dashboard Class.
 *
 * @since 2.11.0
 */
class Premium {
	/**
	 * Cards.
	 *
	 * @since 2.11.0
	 * @var array
	 */
	public $cards = [];

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.11.0
	 */
	public static function enqueueScripts() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName
		wp_enqueue_style( 'bglib-premium',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/premium.css'
		);
	}

	/**
	 * Print Cards.
	 *
	 * @since 2.11.0
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
