<?php

class KWS_GF_EDD_Subscriptions {

	/**
	 * @var KWS_GF_EDD
	 */
	private $parent;

	/**
	 * KWS_GF_EDD_Subscriptions constructor.
	 */
	public function __construct( KWS_GF_EDD $parent ) {

		// add edd subscription when GF subscription complete
		add_action( 'gform_post_subscription_started', array( $this, 'add_entry_subscription_id' ), 10, 2 );
		add_action( 'gform_post_add_subscription_payment', array( $this, 'edd_renew_subscription_payment' ), 10, 2 );

		// cancel edd subscription when GF subscription cancelled
		add_action( 'gform_subscription_cancelled', array( $this, 'edd_cancel_subscription_payment' ), 10, 3 );

		// expire edd subscription when GF subscription expired
		add_action( 'gform_post_payment_action', array( $this, 'edd_expire_subscription_payment' ), 10, 2 );

		add_action( 'edd_gf_payment_added', array( $this, 'maybe_start_subscription' ), 10, 2 );

		$this->parent = $parent;
	}

	/**
	 * Add gf subscription id to entry when GF subscription started
	 *
	 * @param array $entry Entry Object
	 * @param array $subscription The new Subscription object
	 */
	function add_entry_subscription_id( $entry = array(), $subscription = array() ) {

		gform_update_meta( $entry['id'], 'gf_subscription_id', $subscription['subscription_id'] );

	}

	/**
	 * Add EDD new subscription if there's a
	 *
	 * @param array $entry Entry Object
	 * @param int $edd_payment_id EDD Payment ID
	 * @param array $edd_purchase_data Data used to create purchase in EDD
	 *
	 * @return void
	 */
	public function maybe_start_subscription( $entry = array(), $edd_payment_id = 0, $edd_purchase_data = array() ) {

		if ( empty( $entry ) ) {
			return;
		}

		$subscription_id = gform_get_meta( $entry['id'], 'gf_subscription_id' );

		if ( empty( $subscription_id ) ) {
			return;
		}

		$processed_feeds = $this->get_feeds_by_entry( $entry['id'] );

		if ( ! $processed_feeds ) {
			$this->parent->r( 'No feeds exist for Entry ID ' . $entry['id'] );
			return;
		}

		$this->parent->r( $processed_feeds, false, 'Processed Feeds' );

		foreach ( (array) $processed_feeds as $feed_slug => $processed_feed ) {

			$feed = $this->get_feed( $processed_feed[0] );

			if ( ! $feed ) {
				$this->parent->log_error( __METHOD__  . 'Feed does not exist, even though it should' );
				continue;
			}

			// get subscription payment id
			$edd_payment = $this->get_subscription_payment( $entry, $feed );

			if ( ! $edd_payment ) {
				$this->parent->r( 'There is no subscription payment associated with this entry' );
				continue;
			}

			$this->parent->r( $edd_payment, false, 'EDD Subscription Payment' );

			// get GF by form id
			$form = GFAPI::get_form( $entry['form_id'] );

			if ( ! $form ) {
				$this->parent->log_error( 'The form no longer exists (ID #' . $entry['form_id'] . ') - cannot process feed.' );
				break;
			}

			// get feed subscription data
			$feed_settings = $this->get_subscription_feed_settings( $feed );

			// get cart details
			$data = $this->parent->get_edd_data_array_from_entry( $entry, $form );

			$this->add_edd_subscription( $entry, $subscription_id, $data['cart_details'], $feed_settings, $edd_payment->customer_id, $edd_payment->ID );

			break;
		}
	}

	/**
	 * Get processed feeds by entry.
	 *
	 * TODO: Should we use get_single_submission_feed() code instead, to handle single submission feeds?
	 * @see GFFeedAddOn::get_feeds_by_entry()
	 *
	 * @return array|false Array of feeds, false if none found. MODIFIED original code to return all feeds instead of just the current addon
	 */
	public function get_feeds_by_entry( $entry_id ) {

		$processed_feeds = gform_get_meta( $entry_id, 'processed_feeds' );

		if ( ! $processed_feeds ) {
			return false;
		}

		// MODIFIED: Return all feeds instead of just the current addon's
		return $processed_feeds;
	}

	/**
	 * Clone of GFFeedAddOn::get_feed()
	 *
	 * @see GFFeedAddOn::get_feed()
	 *
	 * @param int $id Feed ID
	 *
	 * @return array|false
	 */
	private function get_feed( $id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}gf_addon_feed WHERE id=%d", $id );

		$row = $wpdb->get_row( $sql, ARRAY_A );
		if ( ! $row ) {
			return false;
		}

		$row['meta'] = json_decode( $row['meta'], true );

		return $row;
	}

	 * Check feed subscription and return customer id
	 *
	 * @param array $entry Entry Object
	 * @param array $feed The Entry Feed
	 *
	 * @return int $payment_id The Payment ID
	 */
	public function get_subscription_payment( $entry, $feed ) {

		$payment_id = NULL;
		// check if subscription
		if ( rgars( $feed, 'meta/transactionType' ) == 'subscription' ) {
			global $wpdb;
			// get entry payment id
			$payment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_edd_gf_entry_id' AND meta_value = %s LIMIT 1", $entry['id'] ) );

			if ( $payment_id ) {
				// set subscription payment
				$payment = new EDD_Payment( $payment_id );

				// Set subscription_payment
				$payment->update_meta( '_edd_subscription_payment', true );
			}
		}

		return $payment_id;
	}

	/**
	 * Get subscription feed settings data from form feeds
	 *
	 * @param array $feed The Entry Feed
	 *
	 * @return array $feed_settings The Feed Settings data
	 */
	private function get_subscription_feed_settings( $feed ) {

		// set subscription feed addon
		$subscription_addon = $this->get_payment_addon_feed_configuration();

		// set feed settings array
		$feed_settings = array(
			'trial_amount'       => NULL,
			'trial_prod'         => NULL,
			'trial_subscription' => false,
			'trial_period'       => '',
		);
		// get billing cycle
		$feed_settings['recurring_len'] = $feed['meta']['billingCycle_length'] . ' ' . $feed['meta']['billingCycle_unit'];
		$feed_settings['exp_date']      = Date( 'Y-m-d', strtotime( $feed_settings['recurring_len'] . 's' ) );
		// get recurring times
		$feed_settings['recurring_times'] = ( rgars( $feed, 'meta/recurringTimes' ) ) ? intval( $feed['meta']['recurringTimes'] ) : '';
		// get recurring amount
		$feed_settings['recurring_amount'] = ( rgars( $feed, 'meta/recurringAmount' ) ) ? $feed['meta']['recurringAmount'] : '';

		// get gf configuraion trial
		if ( intval( rgars( $feed, 'meta/trial_enabled' ) ) === 1 ) {
			$feed_settings['trial_subscription'] = true;
			// if trial amount is selected
			if ( rgars( $feed, 'meta/trial_product' ) == 'enter_amount' ) {
				$feed_settings['trial_amount'] = edd_sanitize_amount( $feed['meta']['trial_amount'] );
			} else if ( rgars( $feed, 'meta/trial_product' ) ) {
				$feed_settings['trial_prod'] = $feed['meta']['trial_product'];
			} else if ( rgars( $feed, 'meta/setupFee_product' ) ) {
				$feed_settings['trial_prod'] = $feed['meta']['setupFee_product'];
			}

			// get trial period
			$feed_addon   = $feed["addon_slug"];
			$trial_length = '1';
			$trial_unit   = 'day';
			if ( isset( $subscription_addon[ $feed_addon ] ) && $subscription_addon[ $feed_addon ] ) {
				if ( rgars( $feed, $subscription_addon[ $feed_addon ]['trial_period'] ) ) {
					$trial_length = rgars( $feed, $subscription_addon[ $feed_addon ]['trial_period'] );
				}
				if ( rgars( $feed, $subscription_addon[ $feed_addon ]['trial_period_unit'] ) ) {
					$trial_unit = rgars( $feed, $subscription_addon[ $feed_addon ]['trial_period_unit'] );
				}
			}
			$feed_settings['trial_period'] = $trial_length . ' ' . $trial_unit;
			$feed_settings['exp_date']     = Date( 'Y-m-d', strtotime( $feed_settings['trial_period'] ) );
		}

		return $feed_settings;
	}

	/**
	 * Function to get subscription feed addon settings
	 *
	 * @return array $subscription_addon Feed Subscription Addon
	 */
	public function get_payment_addon_feed_configuration() {

		$subscription_addon = array(
			'gravityformspaypal' => array(
				'trial_period'      => 'meta/trialPeriod_length',
				'trial_period_unit' => 'meta/trialPeriod_unit',
			),
			'gravityformsstripe' => array(
				'trial_period'      => 'meta/trialPeriod',
				'trial_period_unit' => '',
			),
		    'gravityformsauthorizenet' => array(
			    'trial_period'      => '',
			    'trial_period_unit' => '',
		    ),
		);

		/**
		 * Modify where the trial period and trial period unit values are stored for each payment addon
		 * @param array $subscription_addon {
		 *  @type string $trial_period Path to the feed key where the trial period (# of days) is stored (example: `meta/trialPeriod`)
		 *  @type string $trial_period_unit Path to the feed key where the trial period (days/weeks/months) is stored (example: `meta/trialPeriod_unit`)
		 * }
		 */
		$subscription_addon = apply_filters( 'edd_gf_subscription_feed_addons', $subscription_addon );

		return $subscription_addon;
	}

	/**
	 * Start EDD new subscription
	 *
	 * @param array $entry The Entry Feed
	 * @param array $subscription_id The New Subscription Object
	 * @param array $cart_details The Cart details
	 * @param array $feed_settings The Feed Settings Data
	 * @param int $customer_id The Customer ID
	 * @param int $payment_id The Payment ID
	 */
	public function add_edd_subscription( $entry, $subscription_id, $cart_details, $feed_settings, $customer_id, $payment_id ) {

		// add edd subscription
		if ( ! class_exists( 'EDD_Recurring_Subscriber' ) || empty( $cart_details ) ) {
			return;
		}

		// get edd subscriber
		$subscriber = new EDD_Recurring_Subscriber( $customer_id );

		foreach ( $cart_details as $cart_detail ) {

			//variable initialization
			$trial_prod      = false;
			$recurring_prod  = true;
			$recurring_times = $feed_settings['recurring_times'];
			$exp_date        = $feed_settings['exp_date'];
			$trial_period    = '';

			// get product discount
			$prod_discount = ( isset( $cart_detail['discount'] ) && $cart_detail['discount'] ) ? $cart_detail['discount'] : 0;

			// get product price
			$prod_price    = ( isset( $cart_detail['price'] ) && $cart_detail['price'] ) ? $cart_detail['price'] : 0;
			$product_total = $prod_price - $prod_discount;

			// get initial amount
			$initial_amount = ( $feed_settings['trial_subscription'] && $feed_settings['trial_amount'] != NULL ) ? $feed_settings['trial_amount'] : $product_total;

			// Check if trial product
			if ( intval( $feed_settings['trial_prod'] ) == intval( $cart_detail['product_field_id'] ) ) {
				$trial_prod     = true;
				$initial_amount = 0;
				$trial_period   = $feed_settings['trial_period'];
			}

			// Check if not recurring product
			if ( $feed_settings['recurring_amount'] && $feed_settings['recurring_amount'] !== 'form_total' && intval( $feed_settings['recurring_amount'] ) !== intval( $cart_detail['product_field_id'] ) ) {
				$recurring_prod  = false;
				$recurring_times = 1;
				$exp_date        = date( 'Y-m-d', strtotime( '+1 years' ) );
			}

			// Check if recurring product or not trial product and not recurring products
			if ( $recurring_prod || ( ! $trial_prod && ! $recurring_prod ) ) {
				// set args to add edd new subscription
				$args = array(
					'product_id'        => $cart_detail['item_number']['id'],
					'user_id'           => $customer_id,
					'parent_payment_id' => $payment_id,
					'status'            => 'Active',
					'period'            => $feed_settings['recurring_len'],
					'initial_amount'    => $initial_amount,
					'recurring_amount'  => $product_total,
					'bill_times'        => $recurring_times,
					'expiration'        => $exp_date,
					'trial_period'      => $trial_period,
					'profile_id'        => $customer_id,
					'transaction_id'    => $subscription_id,
				);

				$subscriber->add_subscription( $args );

				if ( $trial_period ) {
					$subscriber->add_meta( 'edd_recurring_trials', $entry['id'] );
				}
			}
		}
	}

	/**
	 * Renew edd subscription payment when GF subscription payment renew
	 *
	 * @param array $feed The Entry Object
	 * @param array $action The Action Object
	 */
	public function edd_renew_subscription_payment( $entry, $action ) {

		// get download id for entry
		$payment_id = gform_get_meta( $entry['id'], 'edd_payment_id' );

		if ( empty( $payment_id ) ) {
			$this->parent->r( sprintf( 'No EDD payment ID for entry #%d', $entry['id'] ) );

			return;
		}

		// Get EDD Subscription ID
		$db            = new EDD_Subscriptions_DB;
		$subscriptions = $db->get_subscriptions( array( 'parent_payment_id' => $payment_id ) );

		if ( empty( $subscriptions ) ) {
			$this->parent->r( sprintf( 'No subscriptions to process for Entry #%d', $entry['id'] ) );
			return;
		}

		foreach ( (array) $subscriptions as $subscription_info ) {

			// check if payment not cancelled and bill times >= billed times
			$Subscription     = new EDD_Subscription( $subscription_info->id );

			$times_billed = $Subscription->get_times_billed();

			if ( 'cancelled' !== $Subscription->status && ( 0 === intval( $Subscription->bill_times ) || intval( $Subscription->bill_times ) > $times_billed ) ) {

				// get amount and transaction id
				$amount = ( isset( $action['amount'] ) ) ? edd_sanitize_amount( $action['amount'] ) : '0.00';
				$txn_id = ( ! empty( $action['transaction_id'] ) ) ? $action['transaction_id'] : $action['subscription_id'];

				$Subscription->add_payment( array(
					'amount'         => $amount,
					'transaction_id' => $txn_id,
				) );

				$Subscription->renew();
			}
		}
	}

	/**
	 * Cancel edd subscription payment when GF subscription payment renew cancelled
	 *
	 * @param array $entry The Entry Object
	 *
	 * @return void
	 */
	public function edd_cancel_subscription_payment( $entry = array() ) {

		// get download id for entry
		$payment_id = gform_get_meta( $entry['id'], 'edd_payment_id' );

		if ( empty( $payment_id ) ) {
			return;
		}

		// get subscription id
		$db = new EDD_Subscriptions_DB;

		if ( $subscriptions = $db->get_subscriptions( array( 'parent_payment_id' => $payment_id ) ) ) {

			/** @var EDD_Subscription $subscription */
			foreach ( $subscriptions as $subscription ) {
				$subscription->cancel();
			}
		}
	}

	/**
	 * Expire edd subscription payment when GF subscription payment renew expired
	 *
	 * @param array $entry The Entry Object
	 * @param array $action
	 */
	public function edd_expire_subscription_payment( $entry, $action ) {

		// if action type is expired
		if ( 'expire_subscription' !== $action['type'] ) {
			return;
		}

		// get download id for entry
		$edd_payment_id = gform_get_meta( $entry['id'], 'edd_payment_id' );

		if ( ! $edd_payment_id ) {
			return;
		}

		// get subscription id
		$db            = new EDD_Subscriptions_DB;
		$subscriptions = $db->get_subscriptions( array( 'parent_payment_id' => $edd_payment_id ) );

		if ( ! empty( $subscriptions ) ) {

			/** @var EDD_Subscription $subscription */
			foreach ( $subscriptions as $subscription ) {
				$subscription->expire();
			}
		}
	}
}
