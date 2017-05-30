<?php
/**
 * BoldGrid Premium Default Configurations.
 */

$file = explode( '/', plugin_basename( __FILE__ ) );
$slug = $file[0];

return array(
	'api' => 'https://api.boldgrid.com',
	'option' => 'license',
	'plugin_name' => $slug,
	'plugin_path' => plugin_dir_path( "$slug/$slug.php" ),
	'plugin_key_code' => $slug,
	'main_file_path' => "$slug/$slug.php",
	'key' => get_site_option( 'boldgrid_api_key', null ),
	'apiData' => get_site_transient( 'boldgrid_api_data' ),
	'keyValidate' => true,
	'licenseActivate' => true,
);