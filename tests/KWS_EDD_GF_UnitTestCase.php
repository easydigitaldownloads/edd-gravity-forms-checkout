<?php

defined( 'DOING_EDD_GF_TESTS' ) || exit;

class KWS_EDD_GF_UnitTestCase extends GF_UnitTestCase {

	/** @var KWS_GF_EDD */
	var $KWS_GF_EDD = null;

	/** @var GF_UnitTest_Factory */
	var $factory = null;

	/** @var WP_UnitTest_Factory */
	var $wp_factory = null;

	public function setUp() {
		parent::setUp();

		$this->KWS_GF_EDD = new KWS_GF_EDD;
		$this->wp_factory = new WP_UnitTest_Factory;

		do_action( 'gform_loaded' );

		gf_coupons();
	}

}