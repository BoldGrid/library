<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

require_once dirname( dirname( __FILE__ ) ) . '/src/Library/License.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Api/Call.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Api/Availability.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Configs.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Filter.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Reseller.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Util/Plugin.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Plugin/Plugin.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/RatingPrompt.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Library/Activity.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Util/Option.php';
require_once dirname( dirname( __FILE__ ) ) . '/src/Util/Version.php';

require $_tests_dir . '/includes/bootstrap.php';

