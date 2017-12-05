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
			),
			'boldgrid-backup' => array(
				'key' => 'backup',
				'file' => 'boldgrid-backup/boldgrid-backup.php',
			),
			'boldgrid-staging' => array(
				'key' => 'staging',
				'file' => 'boldgrid-staging/boldgrid-staging.php',
			),
			'boldgrid-gallery' => array(
				'key' => 'gallery-wc-canvas',
				'file' => 'boldgrid-gallery/wc-gallery.php',
			),
			'boldgrid-ninja-forms' => array(
				'key' => 'ninja-forms',
				'file' => 'boldgrid-ninja-forms/ninja-forms.php',
			),
		),

		// WordPress.org recommended plugins.
		'wporgPlugins' => array(
			array(
				'slug' => 'post-and-page-builder',
				'link' => '//wordpress.org/plugins/post-and-page-builder/',
			),
			array(
				'slug' => 'boldgrid-easy-seo',
				'link' => '//wordpress.org/plugins/boldgrid-easy-seo/',
			),
			array(
				'slug' => 'wpforms-lite',
				'link' => '//wpforms.com/lite-upgrade/',
			),
		),
	),
);
