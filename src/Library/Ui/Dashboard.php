<?php
/**
 * BoldGrid Library Ui Dashboard.
 *
 * @package Boldgrid\Library
 *
 * @version 2.10.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Dashboard Class.
 *
 * @since 2.10.0
 */
class Dashboard {
	/**
	 * Cards.
	 *
	 * @since 2.10.0
	 * @var array
	 */
	public $cards = [];

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.10.0
	 */
	public static function enqueueScripts() {
		wp_enqueue_style( 'bglib-dashboard',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/dashboard.css'
		);
	}

	/**
	 * Print Cards.
	 *
	 * @since 2.10.0
	 */
	public function printCards() {
		echo '<div class="bglib-card-container">';

		foreach ( $this->cards as $card ) {
			$card->init();
			$card->printCard();
		}

		echo '</div>';
	}
}
