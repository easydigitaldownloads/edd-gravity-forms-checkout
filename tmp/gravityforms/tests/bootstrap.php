<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = dirname( dirname( __FILE__ ) ) . '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	if ( ! class_exists( 'RGForms' ) ) {
		require_once dirname( dirname( __FILE__ ) ) . '/gravityforms.php';
	}
	GFFormsModel::drop_tables();
	gf_upgrade()->install();
	GFForms::loaded();
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

require dirname( __FILE__ ) . '/gravityforms-factory.php';
require dirname( __FILE__ ) . '/gravityforms-testcase.php';
