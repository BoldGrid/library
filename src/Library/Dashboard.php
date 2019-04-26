<?php
/**
 * BoldGrid Library Dashboard Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 2.9.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

/**
 * BoldGrid Library Dashboard Class.
 *
 * @since 2.9.0
 */
class Dashboard {
	/**
	 * Init.
	 *
	 * @since 2.9.0
	 */
	public function init() {
		// sortWidgets class runs Filter::add($this) in __construct.
		$sortWidgets = new \Boldgrid\Library\Library\Dashboard\SortWidgets();
	}
}
