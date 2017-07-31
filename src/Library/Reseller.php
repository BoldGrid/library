<?php
/**
 * BoldGrid Library Reseller Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.1
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Reseller Class.
 *
 * @since 1.1
 */
class Reseller {

	/**
	 * BoldGrid Central url.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	public $centralUrl = 'https://www.boldgrid.com/central';

	/**
	 * Reseller data.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * Constructor.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		$this->setData();
		Filter::add( $this );
	}

	/**
	 * Set data.
	 *
	 * @since 1.1
	 *
	 * @hook: update_option_boldgrid_reseller
	 */
	public function setData() {
		$defaults = array(
				'reseller_coin_url' => $this->centralUrl,
		);

		$this->data = array_merge( $defaults, get_option( 'boldgrid_reseller', array() ) );
	}
}
