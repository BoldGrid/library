<?php
/**
 * BoldGrid Library Ui Dashboard.
 *
 * @package Boldgrid\Library
 *
 * @version x.x.x
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Ui;

use Boldgrid\Library\Library\Configs;

/**
 * BoldGrid Library Dashboard Class.
 *
 * @since x.x.x
 */
class Dashboard {
	/**
	 * Cards.
	 *
	 * @since xxx
	 * @var array
	 */
	public $cards = [];

	/**
	 * Print Cards.
	 *
	 * @since xxx
	 */
	public function printCards() {
		wp_enqueue_style( 'bglib-dashboard',
			Configs::get( 'libraryUrl' ) . 'src/assets/css/dashboard.css'
		);

		echo '<div class="bglib-card-container">';

		foreach ( $this->cards as $card ) {
			$card->init();
			$card->print();
		}

		echo '</div>';
	}
}
