<?php
/**
 * BoldGrid Premium Default Configurations.
 */

return array(
	'api' => 'https://api.boldgrid.com',
	'option' => 'license',
	'key' => get_site_option( 'boldgrid_api_key', null ),
	'apiData' => get_site_transient( 'boldgrid_api_data' ),

	// Enable key validation in library.
	'keyValidate' => true,

	// Enable license activation/deactivation in library.
	'licenseActivate' => true,

	// Library's Plugin Installer for "Plugins > Add New" in WordPress Dashboard.
	'pluginInstaller' => array(

		// Enabled the plugin installer feature in library.
		'enabled' => true,

		// Installable plugins.
		'plugins' => array(
			'boldgrid-inspirations' => array(
				'key' => 'core',
				'file' => 'boldgrid-inspirations/boldgrid-inspirations.php',
			),
			'boldgrid-staging' => array(
				'key' => 'staging',
				'file' => 'boldgrid-staging/boldgrid-staging.php',
			),
			'boldgrid-seo' => array(
				'key' => 'seo',
				'file' => 'boldgrid-seo/boldgrid-seo.php',
			),
			'boldgrid-editor' => array(
				'key' => 'editor',
				'file' => 'boldgrid-editor/boldgrid-editor.php',
			),
			'boldgrid-backup' => array(
				'key' => 'backup',
				'file' => 'boldgrid-backup/boldgrid-backup.php',
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
	),
);