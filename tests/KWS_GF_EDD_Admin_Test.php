<?php

defined( 'DOING_EDD_GF_TESTS' ) || exit;

class KWS_GF_EDD_Admin_Test extends GF_UnitTestCase {

	function setUp() {
		parent::setUp();

		$KWS_GF_EDD_Admin = new KWS_GF_EDD_Admin();

		$KWS_GF_EDD_Admin->add_hooks();
		$KWS_GF_EDD_Admin->admin_init();
	}


	/**
	 * @covers KWS_GF_EDD_Admin::register_no_conflict
	 */
	function test_register_no_conflict() {
		$no_conflict_scripts = apply_filters( 'gform_noconflict_scripts', array() );
		$no_conflict_styles = apply_filters( 'gform_noconflict_styles', array() );

		$this->assertTrue( in_array( 'edd-gf-admin', $no_conflict_scripts ) );
		$this->assertTrue( in_array( 'edd-gf-admin', $no_conflict_styles ) );
	}

	/**
	 * @covers KWS_GF_EDD_Admin::enable_gform_logging
	 */
	function test_enable_gform_logging() {

		$supported_plugins = gf_logging()->get_supported_plugins();

		$this->assertTrue( isset( $supported_plugins['edd-gf'] ) );
	}

	function test_gf_tooltips() {
		$tooltips = apply_filters( 'gform_tooltips', array() );

		$this->assertTrue( isset( $tooltips['edd_gf_load_variations'] ) );
		$this->assertTrue( isset( $tooltips["form_field_edd_download_value"] ) );
		$this->assertTrue( isset( $tooltips["form_field_edd_customer_data"] ) );
	}
}