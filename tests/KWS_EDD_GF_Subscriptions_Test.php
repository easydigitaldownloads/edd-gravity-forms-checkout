<?php

defined( 'DOING_EDD_GF_TESTS' ) || exit;

/**
 * @group subscriptions
 */
class KWS_EDD_GF_Subscriptions_Test extends KWS_EDD_GF_UnitTestCase {

	public function test_edd_add_subscription_payment() {

		$form = $this->factory->form->create_and_get();
		$entry = $this->factory->entry->create_and_get( array( 'form_id' => $form['id'] ) );

		$action = array(
            'type' => 'cancel_subscription',     // See Below
            'transaction_id' => 'testing-123',              // What is the ID of the transaction made?
            'subscription_id' => '',             // What is the ID of the Subscription made?
            'amount' => '12.34',                  // Amount to charge?
            'entry_id' => $entry['id'],          // What entry to check?
            'transaction_type' => '',
            'payment_status' => '',
            'note' => ''
		);

		$result = $this->KWS_GF_EDD->subscriptions->edd_add_subscription_payment( $entry, $action );

		$this->assertWPError( $result );
		$this->assertEquals( 'missing-payment-id', $result->get_error_code() );


		gform_update_meta( $entry['id'], 'edd_payment_id', 123 );

		$result = $this->KWS_GF_EDD->subscriptions->edd_add_subscription_payment( $entry, $action );
		$this->assertEquals( 'no-edd-subscription', $result->get_error_code() );

		$payment_id = EDD_Helper_Payment::create_recurring_payment();
		$this->assertNotEmpty( $payment_id );

		$sub = new EDD_Subscription();

		$sub_atts = array(
			'parent_payment_id' => $payment_id,
			'period' => 'week',
			'bill_times' => 0,
			'recurring_amount' => 12.34,
			'initial_amount' => 12.34,
		    'status' => 'pending',
		    'transaction_id' => '1234',
		    'expiration' => date( 'Y-m-d H:i:s', strtotime( '+1 week 23:59:59', current_time( 'timestamp' ) ) ),
		);

		$sub->create( $sub_atts );

		gform_update_meta( $entry['id'], 'edd_payment_id', $payment_id );

		$result = $this->KWS_GF_EDD->subscriptions->edd_add_subscription_payment( $entry, $action );

		// It worked to at least find a subscription
		$this->assertTrue( $result );

		$deleted_subscription = $sub->delete();

		$this->assertTrue( $deleted_subscription );

		$db = new EDD_Subscriptions_DB;

		$subscriptions = $db->get_subscriptions( array( 'id' => $sub->id ) );

		$this->assertEquals( array(), $subscriptions, 'The sub we just created has not been deleted.' );

		// Create a new subscription, then process payment for it.
		$sub->create( $sub_atts );

		$this->assertEquals( 'pending', $sub->get_status() );

		$result = $this->KWS_GF_EDD->subscriptions->edd_add_subscription_payment( $entry, $action );

		$payment_data = array(
			'amount'         => 12.34,
			'transaction_id' => 'asdsadasds',
			'gateway'        => 'paypal',
		);

		$added_payment = $sub->add_payment( $payment_data );

		$this->assertTrue( $added_payment );

		$result = $this->KWS_GF_EDD->subscriptions->edd_add_subscription_payment( $entry, $action );

		$this->assertTrue( $result );

		// Expects $sub->renew() to process
		$this->assertEquals( 'active', $sub->get_status() );

		// Create a new subscription, then process payment for it.
		#$sub_atts_with_bill_times = $sub_atts;
		#$sub_atts_with_bill_times['bill_times'] = 1;
		#$sub_with_bill_times = new EDD_Subscription();

		#$sub_with_bill_times->create( $sub_atts_with_bill_times );

		#$this->assertNotEmpty( $sub_with_bill_times );

		#$added_payment = $sub_with_bill_times->add_payment( $payment_data );

		#$this->assertTrue( $added_payment );

		// Expects $sub->renew() to process
		#$this->assertEquals( 'complete', $sub_with_bill_times->get_status() );
	}

	/**
	 * @covers KWS_GF_EDD_Subscriptions::edd_expire_subscription_payment
	 */
	public function test_edd_expire_subscription_payment() {

		$form = $this->factory->form->create_and_get();
		$entry = $this->factory->entry->create_and_get( array( 'form_id' => $form['id'] ) );

		$no_entry_or_action = $this->KWS_GF_EDD->subscriptions->edd_expire_subscription_payment( array(), array() );

		$this->assertFalse( $no_entry_or_action );

		$no_action = $this->KWS_GF_EDD->subscriptions->edd_expire_subscription_payment( $entry, array() );

		$this->assertFalse( $no_action );

		$action = array(
			'type' => 'expire_subscription',
		);

		$no_entry = $this->KWS_GF_EDD->subscriptions->edd_expire_subscription_payment( array(), $action );

		$this->assertFalse( $no_entry );

		$no_entry_payment_id_meta = $this->KWS_GF_EDD->subscriptions->edd_expire_subscription_payment( $entry, $action );

		$this->assertFalse( $no_entry_payment_id_meta );

		gform_update_meta( $entry['id'], 'edd_payment_id', 123 );

		$this->assertEquals( 123, gform_get_meta( $entry['id'], 'edd_payment_id' ) );

		$no_subscriptions = $this->KWS_GF_EDD->subscriptions->edd_expire_subscription_payment( $entry, $action );

		// No errors, but no subscriptions either
		$this->assertNull( $no_subscriptions );

		// Create payment
		$payment_id = EDD_Helper_Payment::create_recurring_payment();

		$this->assertNotEmpty( $payment_id );

		// Create subscription
		EDD_Helper_Payment::create_recurring_payment();

		// Trigger expire subscription
		#do_action( 'gform_post_payment_action', $entry, $action );

	}

	/**
	 * @covers KWS_GF_EDD_Subscriptions::get_entry_subscription_id
	 * @covers KWS_GF_EDD_Subscriptions::add_entry_subscription_id
	 */
	public function test_add_entry_subscription_id() {

		$form = $this->factory->form->create_and_get();
		$entry = $this->factory->entry->create_and_get( array( 'form_id' => $form['id'] ) );

		$subscription = array( 'subscription_id' => 123 );

		do_action( 'gform_post_subscription_started', $entry, $subscription );

		$this->assertEquals( 123, $this->KWS_GF_EDD->subscriptions->get_entry_subscription_id( $entry ) );

		$entry2 = $this->factory->entry->create_and_get( array( 'form_id' => $form['id'] ) );

		do_action( 'gform_post_subscription_started', $entry, array() );

		// Should return false because no subscription details
		$this->assertFalse( $this->KWS_GF_EDD->subscriptions->get_entry_subscription_id( $entry2 ) );
	}

	/**
	 * @covers KWS_GF_EDD_Subscriptions::get_edd_gf_recurring_periods
	 * @covers KWS_GF_EDD_Subscriptions::get_edd_recurring_subscription_frequency
	 */
	public function test_get_edd_gf_recurring_periods() {

		remove_all_filters( 'edd_recurring_periods' );

		$periods = EDD_Recurring::periods();

		$this->assertFalse( isset( $periods['11 month'] ) );
		$this->assertFalse( isset( $periods['11 week'] ) );
		$this->assertFalse( isset( $periods['300 day'] ) );

		add_filter( 'edd_recurring_periods', array( $this->KWS_GF_EDD->subscriptions, 'get_edd_gf_recurring_periods' ) );

		$periods = EDD_Recurring::periods();

		$this->assertTrue( isset( $periods['11 month'] ) );
		$this->assertTrue( isset( $periods['11 week'] ) );
		$this->assertTrue( isset( $periods['300 day'] ) );

		$frequency = edd_recurring()->get_pretty_subscription_frequency( '11 day' );

		$this->assertEquals( '&nbsp;&nbsp;' . sprintf( esc_html__('%d Days', 'edd-gf' ), 11 ), $frequency );
	}

	/**
	 * Triggered via edd_gf_payment_added hook
	 */
	public function _test_maybe_start_subscription() {

		$entry = array(); $edd_payment_id = 0; $edd_purchase_data = array();

		#do_action( 'edd_gf_payment_added' );
	}

	public function tearDown() {
		RGFormsModel::drop_tables();
		parent::tearDown();
	}

}