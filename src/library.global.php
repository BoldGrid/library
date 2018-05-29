<?php
/**
 * BoldGrid Premium Default Configurations.
 */

defined( 'WPFORMS_SHAREASALE_ID' ) || define( 'WPFORMS_SHAREASALE_ID', '1581233' );

return array(
	'api' => 'https://api.boldgrid.com',
	'option' => 'license',
	'key' => get_site_option( 'boldgrid_api_key', null ),
	'apiData' => get_site_transient( 'boldgrid_api_data' ),
	'themeData' => get_site_transient( 'boldgrid_theme_data' ),

	// Enable key validation in library.
	'keyValidate' => true,

	// Enable license activation/deactivation in library.
	'licenseActivate' => false,

	// Library's Plugin Installer for "Plugins > Add New" in WordPress Dashboard.
	'pluginInstaller' => array(

		// Enabled the plugin installer feature in library.
		'enabled' => true,

		// Default Premium Link.
		'defaultLink' => 'https://www.boldgrid.com/connect-keys/',

		// Installable plugins.
		'plugins' => array(
			'boldgrid-inspirations' => array(
				'key' => 'core',
				'file' => 'boldgrid-inspirations/boldgrid-inspirations.php',
				'priority' => 20,
			),
			'boldgrid-staging' => array(
				'key' => 'staging',
				'file' => 'boldgrid-staging/boldgrid-staging.php',
				'priority' => 60,
			),
			'boldgrid-gallery' => array(
				'key' => 'gallery-wc-canvas',
				'file' => 'boldgrid-gallery/wc-gallery.php',
				'priority' => 70,
			),
			'boldgrid-ninja-forms' => array(
				'key' => 'ninja-forms',
				'file' => 'boldgrid-ninja-forms/ninja-forms.php',
				'priority' => 80,
			),
		),

		// WordPress.org recommended plugins.
		'wporgPlugins' => array(
			'post-and-page-builder' => array(
				'slug' => 'post-and-page-builder',
				'link' => '//wordpress.org/plugins/post-and-page-builder/',
				'priority' => 10,
			),
			'boldgrid-easy-seo' => array(
				'slug' => 'boldgrid-easy-seo',
				'link' => '//wordpress.org/plugins/boldgrid-easy-seo/',
				'priority' => 30,
			),
			'boldgrid-backup' => array(
				'slug' => 'boldgrid-backup',
				'link' => '//wordpress.org/plugins/boldgrid-backup/',
				'priority' => 40,
			),
			'wpforms-lite' => array(
				'slug' => 'wpforms-lite',
				'link' => '//wpforms.com/lite-upgrade/',
				'priority' => 80,
			),
		),
	),
	'api_calls' => array(
		'get_theme_data' => '/api/open/get-theme-data',
		'get_asset'      => '/api/open/get-asset',
	),
);
