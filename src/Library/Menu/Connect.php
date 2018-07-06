<?php
/**
 * BoldGrid Library Configs Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Menu;

use Boldgrid\Library\Library\Filter;

/**
 * BoldGrid Library Configs Class.
 *
 * This class is responsible for setting and getting configuration
 * options that are set for the library.
 *
 * @since 1.0.0
 */
class Connect {

	/**
	 *
	 * @return [type] [description]
	 */
	protected static function getMenuItems() {
		return array(
			'topLevel' => array(
				'id' => 'boldgrid-adminbar-icon',
				'title' => '<span aria-hidden="true" class="boldgrid-icon ab-icon"></span>',
				'href' => 'https://www.boldgrid.com/',
				'meta' => array(
					'class' => 'boldgrid-node-icon',
				),
			),
			'items' => array(
				array(
					'id' => 'boldgrid-site-url',
					'parent' => 'boldgrid-adminbar-icon',
					'title' => __( 'BoldGrid.com', 'boldgrid-inspirations' ),
					'href' => 'https://www.boldgrid.com/',
					'meta' => array(
						'class' => 'boldgrid-dropdown',
						'target' => '_blank',
						'title' => 'BoldGrid.com',
						'tabindex' => '1',
					),
				),
				array(
					'id' => 'boldgrid-site-documentation',
					'parent' => 'boldgrid-adminbar-icon',
					'title' => __( 'Documentation', 'boldgrid-inspirations' ),
					'href' => 'https://www.boldgrid.com/docs',
					'meta' => array(
						'class' => 'boldgrid-dropdown',
						'target' => '_blank',
						'title' => 'Documentation',
						'tabindex' => '1',
					),
				),
				array(
					'id' => 'boldgrid-central-url',
	 				'parent' => 'boldgrid-adminbar-icon',
					'title' => __( 'BoldGrid Central', 'boldgrid-inspirations' ),
					'href' => 'https://www.boldgrid.com/central',
					'meta' => array(
						'class' => 'boldgrid-dropdown',
						'target' => '_blank',
						'title' => 'BoldGrid Central',
						'tabindex' => '1',
					),
				),
				array(
					'id' => 'boldgrid-connect-url',
					'parent' => 'boldgrid-adminbar-icon',
					'title' => __( 'BoldGrid Connect', 'boldgrid-inspirations' ),
					'href' => 'https://www.boldgrid.com/feedback',
					'meta' => array(
						'class' => 'boldgrid-dropdown',
						'target' => '_blank',
						'title' => 'BoldGrid Connect',
						'tabindex' => '1',
					),
				),
				array(
					'id' => 'boldgrid-feedback-url',
					'parent' => 'boldgrid-adminbar-icon',
					'title' => __( 'Feedback', 'boldgrid-inspirations' ),
					'href' => 'https://www.boldgrid.com/feedback',
					'meta' => array(
						'class' => 'boldgrid-dropdown',
						'target' => '_blank',
						'title' => 'Feedback',
						'tabindex' => '1',
					),
				),
			),
		);
	}

	/**
	 * Constructor.
	 *
	 * Add Filters.
	 *
	 * @since X.X.X
	 */
	public function __construct() {
		Filter::add( $this );
	}

	/**
	 * Add Menu item configurations.
	 *
	 * @hook: admin_bar_menu
	 *
	 * @since X.X.X
	 */
	public function addMenu( $wpAdminBar ) {
		$configs = self::getMenuItems();

		$wpAdminBar->add_node( $configs['topLevel'] );
		foreach ( $configs['items'] as $item ) {
			$wpAdminBar->add_menu( $item );
		}
	}
}
