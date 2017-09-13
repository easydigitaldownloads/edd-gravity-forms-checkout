<?php

ob_start();

$is_debug = in_array( '-v', $_SERVER['argv'], true ) || in_array( '-debug', $_SERVER['argv'], true );

define('EDD_GF_TESTS_DEBUG', $is_debug );

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array(
		'gravityforms/gravityforms.php',
		'gravityformscoupons/coupons.php',
		'easy-digital-downloads/easy-digital-downloads.php',
		'edd-recurring/edd-recurring.php',
		'edd-gravity-forms/edd-gravity-forms.php'
	)
);

$wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : dirname( __FILE__ ) . '/../tmp/wordpress-develop/tests/phpunit';

require_once( $wp_tests_dir . '/includes/bootstrap.php' );


require_once dirname( __FILE__ ) . '/../tmp/gravityforms/gravityforms.php';
require_once dirname( __FILE__ ) . '/../tmp/gravityformscoupons/coupons.php';
require_once dirname( __FILE__ ) . '/../tmp/gravityforms/tests/gravityforms-factory.php';
require_once dirname( __FILE__ ) . '/../tmp/gravityforms/tests/gravityforms-testcase.php';
require_once dirname( __FILE__ ) . '/../tmp/easy-digital-downloads/easy-digital-downloads.php';
require_once dirname( __FILE__ ) . '/../tmp/easy-digital-downloads/tests/helpers/class-helper-download.php';
require_once dirname( __FILE__ ) . '/../tmp/edd-recurring/edd-recurring.php';
require_once dirname( __FILE__ ) . '/../tmp/edd-recurring/tests/helpers/class-edd-recurring-unittestcase.php';
require_once dirname( __FILE__ ) . '/../tmp/edd-recurring/tests/helpers/class-helper-payment.php';
require_once dirname( __FILE__ ) . '/../edd-gravity-forms.php';
require_once dirname( __FILE__ ) . '/KWS_EDD_GF_UnitTestCase.php';

echo "Installing Easy Digital Downloads...\n";
activate_plugin( 'easy-digital-downloads/easy-digital-downloads.php' );

// Run EDD Install
edd_run_install();

echo "Installing Recurring Payments...\n";
activate_plugin( 'edd-recurring/edd-recurring.php' );

edd_recurring_install();

global $current_user, $edd_options;
$edd_options = get_option( 'edd_settings' );

$current_user = new WP_User(1);
$current_user->set_role('administrator');

do_action( 'plugins_loaded' );

ob_end_clean();