<?php

/**
 * Debug to console.
 *
 * @since 2.12.0
 *
 * @param mixed $var Message to write to STDERR.
 */
function bglib_phpunit_error_log( $var ) {
	fwrite( // phpcs:ignore
		STDERR,
		"\n\n## --------------------\n" .
		print_r( $var, 1 ) . // phpcs:ignore
		"\n## ------------------\n\n"
	);
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

require_once dirname( dirname( __FILE__ ) ) . '/src/Library/License.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Settings.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Api/Call.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Api/Availability.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Configs.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Filter.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Reseller.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Util/Plugin.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/Plugin.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/Factory.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/Plugins.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/UpdateData.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/Page.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/Notice.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Theme/Theme.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Theme/Themes.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Theme/UpdateData.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/RatingPrompt.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Activity.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Util/Option.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Util/Version.php';

/*
 * Yoast/PHPUnit-Polyfills, required for running the WP test suite.
 * Please see https://make.wordpress.org/core/2021/09/27/changes-to-the-wordpress-core-php-test-suite/
 *
 * The WP Core test suite can now run on all PHPUnit versions between PHPUnit 5.7.21 up to the latest
 * release (at the time of writing: PHPUnit 9.5.10), which allows for running the test suite against
 * all supported PHP versions using the most appropriate PHPUnit version for that PHP version.
 */
require_once dirname( dirname( __FILE__ ) ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

require $_tests_dir . '/includes/bootstrap.php';
