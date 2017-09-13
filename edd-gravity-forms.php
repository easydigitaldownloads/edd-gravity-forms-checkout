<?php
/**
 * \mainpage Gravity Forms Checkout for EDD
 *
 * \section intro Who this documentation is for
 * This documentation is for _developers_, not for non-developers. If you don't intend to edit any code,
 * then you should instead visit the [Support & Knowledgebase](http://support.katz.co).
 *
 */

/**
 * Plugin Name: Easy Digital Downloads - Gravity Forms Checkout
 * Plugin URI: https://easydigitaldownloads.com/downloads/gravity-forms-checkout/
 * Description: Integrate Gravity Forms purchases with Easy Digital Downloads
 * Author: Katz Web Services, Inc.
 * Version: 1.4.2
 * Requires at least: 3.0
 * Author URI: http://katz.co
 * License: GPL v3
 * Text Domain: edd-gf
 * Domain Path: languages
 */
/*
  Copyright (C) 2015 Katz Web Services, Inc.

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
 */

final class KWS_GF_EDD {

    /**
     * Version number for the updater class
     * @link  http://semver.org
     * @var  string Semantic Versioning version number
     */
    const version = '1.4.2';

    /**
     * Name of the plugin for the updater class
     * @var string
     */
    const name = 'Gravity Forms Checkout';

    /**
     * Set whether to print debug output using the `r()` method and `console.log()`
     * @var boolean
     */
    const debug = false;

	/**
	 * @var KWS_GF_EDD_Logging
	 */
    public $logger = null;

	/**
	 * @var KWS_GF_EDD_Subscriptions
	 */
    public $subscriptions = null;

    /**
     * Set constants, load textdomain, and trigger init()
     * @uses  KWS_GF_EDD::init()
     */
    function __construct() {

        if (!defined('EDD_GF_PLUGIN_FILE')) {
            define('EDD_GF_PLUGIN_FILE', __FILE__);
        }
        if (!defined('EDD_GF_PLUGIN_URL')) {
            define('EDD_GF_PLUGIN_URL', plugins_url('', __FILE__));
        }
        if (!defined('EDD_GF_PLUGIN_DIR')) {
        	/** @define "EDD_GF_PLUGIN_DIR" "./" */
            define('EDD_GF_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }

        // Load the default language files
        load_plugin_textdomain('edd-gf', false, dirname(plugin_basename(EDD_GF_PLUGIN_FILE)) . '/languages/');

        add_action( 'gform_loaded', array( $this, 'init' ) );
    }

	/**
	 * Include the admin script and non-admin hooks
	 */
	public function init() {

		$this->require_files();

		$this->logger = new KWS_GF_EDD_Logging;

		$this->subscriptions = new KWS_GF_EDD_Subscriptions( $this );

		/**
		 * Check for plugin updates. Built into EDD version 1.9+
		 */
		if (class_exists('EDD_License')) {
			new EDD_License(EDD_GF_PLUGIN_FILE, self::name, self::version, 'Katz Web Services, Inc.');
		}

		$this->add_actions();
	}

	/**
	 * Include required files
	 */
    private function require_files() {
        require_once( EDD_GF_PLUGIN_DIR . 'logging.php' );
        require_once( EDD_GF_PLUGIN_DIR . 'admin.php' );
        require_once( EDD_GF_PLUGIN_DIR . 'class-gf-edd-user-fields.php' );
	    require_once( EDD_GF_PLUGIN_DIR . 'subscriptions.php' );
    }

    private function add_actions() {

	    // Run the EDD functionality
	    add_action("gform_after_submission", array($this, 'send_purchase_to_edd'), PHP_INT_MAX, 2);

	    // Backward compatibility
	    add_action('gform_post_payment_status', array($this, 'gform_post_payment_status'), 10, 3);

	    // Update whenever GF updates payment statii
	    add_action('gform_post_payment_completed', array($this, 'post_payment_callback'), 10, 2);
	    add_action('gform_post_payment_refunded', array($this, 'post_payment_callback'), 10, 2);

	    // action to set edd transaction id
	    add_action('gform_post_payment_callback', array($this, 'update_edd_transaction_id'), 10, 3);
    }

    /**
     * Make GF and EDD statuses match
     * @return array Existing statuses
     * @filter edd_gf_default_status Modify the default status when there's no status match. Default: `pending`. Passes default and `$status` arguments.
     * @filter edd_gf_payment_status Override the status when there is a match. Passes matched value and `$status` arguments.
     */
    public function get_payment_status_from_gf_status( $status ) {

        $this->r('Status passed to get_payment_status_from_gf_status: ' . $status);

        $gf_payment_statuses = array(
            "Processing" => 'pending',
            "Pending" => 'pending',
            "Paid" => 'publish',
            "Active" => 'publish',
            "Approved" => 'publish',
            "Completed" => 'publish',
            "Expired" => 'revoked',
            "Failed" => 'failed',
            "Cancelled" => 'failed',
            "Reversed" => 'refunded',
            "Refunded" => 'refunded',
            "Voided" => 'refunded',
            "Void" => 'refunded',
        );

        /**
         * Modify the default status when there's no status match.
         *
         * @param string $default Default payment status for EDD ("pending" or "publish") (Default: "pending")
         * @param string $status The status of the Gravity Forms entry, set in `$entry['payment_status']`
         */
        $default = apply_filters('edd_gf_default_status', 'pending', $status);

        $return = $default;

        if (isset($gf_payment_statuses["{$status}"])) {

            /**
             * Override the status for a purchase.
             *
             * @param string $edd_status The EDD status
             * @param string $gf_status The GF status used to fetch the EDD status
             */
            $return = apply_filters('edd_gf_payment_status', $gf_payment_statuses["{$status}"], $status);
        }

        return $return;
    }

    /**
     * Returns an `options` array for a download with variations.
     *
     * This takes the submitted entry, the original GF form, and the EDD price
     * variations and searches for matches to the price ID and the related amounts.
     *
     * @param  array $entry       GF Entry array
     * @param  array $field       GF Field array
     * @param  int $download_id The Download ID of the parent EDD download
     * @param  float|int $field_id    The ID of the current field being processed
     * @return array              An associative array with `amount` and `price_id` keys.
     */
    function get_download_options_from_entry($entry, $field, $download_id, $product, $option_name = '', $option_price = 0) {

        if (!function_exists('edd_get_variable_prices')) {
            return null;
        }

        $options = null;

        // Get the variations for the product
        if ($prices = edd_get_variable_prices($download_id)) {

            $this->r($prices, false, '$prices for EDD product ID #' . $download_id . ' , line ' . __LINE__);

            $options = array(); // Default options array
            // The default Price ID is 0, like in EDD.
            $options['price_id'] = 0;

            // Use the submitted price instead of any other.
            $options['amount'] = GFCommon::to_number($option_price);

            // We loop through the download variable prices from EDD
            foreach ($prices as $price_id => $price_details) {

                // If the $price_id matches the value, we're good.
                if (is_numeric($option_name) && intval($option_name) === intval($price_id)) {
                    $options['price_id'] = $price_id;
                    break; // Stop looking
                }

                if (!empty($field['choices'])) {
                    // If the name is the same, then we're good too.
                    foreach ($field['choices'] as $choice) {

                        // If the EDD variation name is in the Gravity Forms choice array,
                        // that means that it's either in the `text` or `value` fields, so
                        // we go with that.
                        if (in_array($price_details['name'], $choice)) {
                            $options['price_id'] = $price_id;
                            break; // Stop looking
                        }
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Get a field array from a Gravity Forms form by the ID of the field
     * @param  string|int $id   Number of the field ID
     * @param  array $form Gravity Forms form array
     * @return array|boolean       Field array, if exists. False if not.
     */
    function get_form_field_by_id($id, $form) {

        foreach ($form['fields'] as $field) {
            if ($field['id'] === $id) {
                return $field;
            }
        }

        return false;
    }

    /**
     * Take the submitted GF entry and form and generate an array of data for a new EDD order
     *
     * This is the work horse for the plugin. It processes an array with the keys: `cart_details`, `user_info`, `downloads`.
     *
     * @link http://support.katz.co/article/334-override-user-data Learn about how not to use logged-in user data
     * @param  array $entry GF Entry array
     * @param  array $form  GF Form array
     * @todo More user info for logged-in users.
     * @return array        Associative array with keys `cart_details`, `user_info`, `downloads`
     */
    function get_edd_data_array_from_entry($entry, $form) {

        $data = $downloads = $user_info = $cart_details = $coupon_details = array();

        // Get the products for the entry
        $product_info = GFCommon::get_product_fields($form, $entry);

        $this->r($product_info, false, 'The products in the entry, from GFCommon::get_product_fields()');

        if (empty($product_info['products'])) {
            $this->r($product_info, false, 'There are no products in the entry.');
            return array();
        }

        // get applied coupon details to entry
        $coupon_details = $this->get_entry_coupon_details($form, $entry, $product_info['products']);

        foreach ($product_info['products'] as $product_field_id => $product) {

            $field = $this->get_form_field_by_id($product_field_id, $form);

            // Only process connected products that don't have variable prices.
            if (empty($field['eddDownload'])) {
                continue;
            }

            $edd_product_id = (int) $field['eddDownload'];

            $this->r($field, false, '$field');
            $this->r($product, false, '$product');

            $download_item = array(
                'id' => $field['eddDownload'],
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'price' => GFCommon::to_number($product['price']),
                'product_field_id' => $product_field_id,
            );

            if (!empty($field['eddHasVariables'])) {

                /**
                 * Also include a link to download the base product for variable purchases
                 *
                 * @param boolean $include True: Yes, include base. False: no, don't. Default: false
                 */
                $include_base_product = apply_filters('edd_gf_variable_products_include_base', false);

                // If the product was submitted with options chosen
                if (!empty($product['options'])) {

                    if ($include_base_product) {
                        $downloads[] = $download_item;
                    }

                    // We want to add a purchase item for each option
                    foreach ($product['options'] as $key => $option) {

                        $option_name = $product['options'][$key]['option_name'];
                        $option_price = $product['options'][$key]['price'];

                        $download_item['quantity'] = 1;
                        $download_item['price'] = GFCommon::to_number($product['price'] + $option_price);

                        $download_item['options'] = $this->get_download_options_from_entry($entry, $field, $edd_product_id, $product, $option_name, $option_price);

                        // Create an additional download for each option
                        $downloads[] = $download_item;
                    }
                } else {
                    $option_price = $product['price'];
                    $option_name = $product['name'];

                    $download_item['options'] = $this->get_download_options_from_entry($entry, $field, $edd_product_id, $product, $option_name, $option_price);

                    $downloads[] = $download_item;
                }
            } else {

                $this->r($download_item, false, 'Download item when empty $field[\'eddHasVariables\']');

                $downloads[] = $download_item;
            }
        }

        $this->r($downloads, false, 'Downloads after product info, before removing empty downloads');

        // Clean up the downloads and remove items with no quantity.
        foreach ($downloads as $key => $download) {

            // If the quantity is 0, get rid of the download.
            if (empty($download['quantity'])) {
                unset($downloads[$key]);
            }
        }

	    /**
	     * Get product subscription data for an entry, if exists.
	     *
	     * @used-by KWS_GF_EDD_Subscriptions::get_entry_subscription_data
	     * @see GFCommon::get_product_fields for $product_info array
	     * @since 2.0
	     *
	     * @param array $subscription Array of {
	     * @type string $trial_prod
	     * }
	     * @param array $form Gravity Forms form object
	     * @param array $entry Gravity Forms entry object
	     * @param array $product_info['products'] The products returned from {@see GFCommon::get_product_fields}
	     */
        $subscription = apply_filters( 'edd_gf_get_entry_subscription_data', array(), $form, $entry, $product_info['products'] );

	    // initial total variable
	    $total = 0;

        foreach ($downloads as $download) {

            // When buying multiple products with price variants,
            // we pass the download id as "{$download_id}.{$gf_input_id}" so
            // the array key doesn't get overwritten.
            $download_id = absint($download['id']);

            $quantity = ( absint($download['quantity']) > 0 ? absint($download['quantity']) : 1 );

            /** @see edd_update_payment_details() in functions.php */
            $item = array(
                'id' => $download_id,
                'quantity' => 1,
            );

            // If there's price ID data, use it
            if (isset($download['options']) && !empty($download['options'])) {
                $item['options'] = $download['options'];
            }

            $item_price = isset($download['price']) ? GFCommon::to_number($download['price']) : GFCommon::to_number($item['options']['amount']);

            $i = 0;
            while ($quantity > $i) {
                $item_discount = 0;
                // update cart total with coupon code val
                if (isset($coupon_details['percentage']) && $coupon_details['percentage']) {
                    $item_discount = $item_price * ( $coupon_details['percentage'] / 100 );
                } else if (isset($coupon_details['flat']) && $coupon_details['flat']) {
                    $item_discount = $coupon_details['flat'];
                }

                if( $subscription ) {
	                // update cart total with subscription trial product
	                if ( ! empty( $subscription['trial_prod'] ) && $subscription['trial_prod'] == $download['product_field_id'] ) {
		                $item_discount = $item_price;
	                } else if ( ! empty( $subscription['trial_amount'] ) ) {
		                $item_discount += $subscription['trial_amount'];
	                }
                }

	            $cart_details[] = array(
                    'name' => get_the_title($download_id),
                    'id' => $download_id,
                    'item_number' => $item,
                    'price' => ( rgar( $subscription, 'is_trial' ) ? '0.00' : $item_price ),
                    '_item_price' => $item_price,
                    'tax' => null,
                    'quantity' => 1,
                    'discount' => $item_discount,
                    'product_field_id' => $download['product_field_id'],
                );
                $i++;
            }

            $total += ( $item_price * $quantity );
        }



        $this->r($downloads, false, 'Downloads after generating Cart Details (Line ' . __LINE__ . ')');

        $data['downloads'] = $downloads;
        $data['user_info'] = $this->get_user_info($form, $entry);
        $data['cart_details'] = $cart_details;
        $data['total'] = GFCommon::to_number($total);
	    $data['gateway'] = $this->get_edd_gateway_from_entry( $entry );

        if ($data['total'] < 0) {

            $this->r($data, false, '$data[total] was negative (' . $data['total'] . ') - resetting to $0.00 (Line ' . __LINE__ . ')');

            $data['total'] = 0;
        }

        $this->r($data, false, '$data returned from get_edd_data_array_from_entry() (Line ' . __LINE__ . ')');

        return $data;
    }

	/**
	 * Get the payment gateway used for an entry, and convert to EDD gateway
	 *
	 * @since 2.0
	 *
	 * @param $entry
	 *
	 * @return false|string FALSE if gateway isn't found; EDD gateway slug otherwise
	 */
	public function get_edd_gateway_from_entry( $entry ) {

    	$original_gateway = gform_get_meta( $entry['id'], 'payment_gateway' );

		$gateway = $original_gateway;

		switch ( $original_gateway ) {
			case 'gravityformspaypalpaymentspro':
			case 'paypalpaymentspro':
				$gateway = 'paypalpro';
				break;

			case 'gravityformsstripe':
				$gateway = 'stripe';
				break;

			case 'gravityformsauthorizenet':
			case 'authorize.net':
				$gateway = 'authorize';
				break;

			case 'gravityformspaypal':
				$gateway = 'paypal';
				break;
		}

		/**
		 * Override the gateway slug stored for a purchase generated via Gravity Forms
		 * @since 2.0
		 * @param string|false $gateway Gateway slug passed to EDD
		 */
		$gateway = apply_filters( 'edd_gf_gateway_slug', $gateway, $original_gateway );

		return $gateway;
	}

	/**
	 * Get the field ID from the first field of type $type
	 *
	 * @uses GFAPI::get_fields_by_type
	 *
	 * @param array $form GF Form object
	 * @param string $type Field type
	 *
	 * @return int|bool The field ID from the first field of type $type, if exists. If no fields of $type exist, false
	 */
	private function get_first_field_id_by_type( $form = array(), $type = 'name' ) {

    	$fields = GFAPI::get_fields_by_type( $form, array( $type ) );

		if ( empty( $fields ) ) {
			$this->r( 'No fields of type: ' . $type );
			return false;
		}

		$field = array_shift( $fields );

		$this->r( $field, false,__METHOD__ . ': Using field as default for ' . $type );

		return $field ? $field->id : false;
	}

    /**
     * Get user information (name and email) from entry 
     * 
     * @param  array $form  Gravity Forms form array
     * @param  array $entry Gravity Forms entry array
     *
     * @return array $user_info
     */
    function get_user_info_from_submission( $form, $entry ) {

        $user_info = array();

	    $field_configuration = eddUserFields::get_instance()->get_form_settings( $form );

	    $name_field_id = rgar( $field_configuration, 'name', false );

	    if ( ! $name_field_id ) {
		    $name_field_id = $this->get_first_field_id_by_type( $form, 'name' );
	    }

	    if( $name_field_id ) {
		    $user_info['display_name'] = rgar( $entry, $name_field_id, false );
		    $user_info['first_name'] = rgar( $entry, $name_field_id . '.3', false );
		    $user_info['last_name'] = rgar( $entry, $name_field_id . '.6', false );
	    }

	    $email_field_id = rgar( $field_configuration, 'email', false );


	    if ( ! $email_field_id ) {
		    $email_field_id = $this->get_first_field_id_by_type( $form, 'email' );
	    }

	    if( $email_field_id ) {
		    $user_info['email'] = rgar( $entry, $email_field_id, false );
	    }

	    $address_field_id = rgar( $field_configuration, 'address', false );

	    if ( ! $address_field_id ) {
		    $address_field_id = $this->get_first_field_id_by_type( $form, 'address' );
	    }

	    if( $address_field_id ) {
		    $user_info['address'] = array(
	    	    'line1' => rgar( $entry, $address_field_id . '.1', false ),
		        'line2' => rgar( $entry, $address_field_id . '.2', false ),
		        'city' => rgar( $entry, $address_field_id . '.3', false ),
		        'state' => rgar( $entry, $address_field_id . '.4', false ),
		        'zip' => rgar( $entry, $address_field_id . '.5', false ),
		        'country' => GFCommon::get_country_code( rgar( $entry, $address_field_id . '.6', false ) ),
		    );
	    }

	    $this->r( $user_info, false, 'User info mapping' );

        return array_filter( $user_info );
    }

    /**
     * Get user info from the entry
     *
     * @param  array $form  Gravity Forms form array
     * @param  array $entry Gravity Forms entry array
     * @param  null|WP_User $wp_user User object
     *
     * @return array        array with user data. Keys include: 'id' (int user ID), 'email' (string user email), 'first_name', 'last_name', 'discount' (empty)
     */
    function get_user_info( $form = array(), $entry = array() ) {

        $wp_user = false;

        $user_info_from_entry = $this->get_user_info_from_submission($form, $entry);

	    /**
	     * @filter `edd_gf_use_details_from_logged_in_user` Whether to use details from the logged-in user if the information is not in the form
	     * @see http://support.katz.co/article/334-override-user-data
	     * @param bool $use_logged_in_user_details True: use user defaults, false: don't [Default: true]
	     * @param array $entry Gravity Forms Entry object
	     * @param array $form Gravity Forms
	     */
	    $use_logged_in_user_details = apply_filters( 'edd_gf_use_details_from_logged_in_user', true, $entry, $form );

        // Get the $current_user WP_User object
        if ( $use_logged_in_user_details && is_user_logged_in() ) {
	        $wp_user = wp_get_current_user();
        }
        // User is not logged in, but the email exists
        else if ( ! empty( $user_info_from_entry['email'] ) ) {
            $wp_user = get_user_by( 'email', $user_info['email'] );
        }

        $default_user_info = array(
        	'id' => ( $wp_user ? $wp_user->ID : -1 ),
            'email' => ( $wp_user ? $wp_user->user_email : null ),
            'first_name' => ( $wp_user ? $wp_user->user_firstname : null ),
            'last_name' => ( $wp_user ? $wp_user->user_lastname : null ),
            'display_name' => ( $wp_user ? $wp_user->display_name : null ),
            'address' => array(
	            'line1' => null,
	            'line2' => null,
	            'city' => null,
	            'state' => null,
	            'zip' => null,
	            'country' => null,
            ),
            'discount' => 0,
        );

        $user_info = wp_parse_args( $user_info_from_entry, $default_user_info );

        // get entry coupon codes to set user data array
        $user_info_discount = $this->get_entry_discount($form, $entry);

        if ( ! empty( $user_info_discount ) ) {
            $user_info['discount'] = $user_info_discount;
        }

        return $user_info;
    }

    /**
     * Take a GF submission and add a purchase to EDD.
     *
     * This converts the GF submission to an EDD order.
     *
     * @uses GFFormsModel::get_lead()
     * @uses  KWS_GF_EDD::get_edd_data_array_from_entry()
     * @uses  KWS_GF_EDD::get_payment_status_from_gf_status()
     * @uses  edd_update_payment_status()
     * @uses  GFCommon::to_number()
     * @uses  edd_insert_payment()
     * @uses  edd_insert_payment_note()
     * @param  array $entry GF Entry array
     * @param  array $form GF Form array
     */
    public function send_purchase_to_edd($entry = null, $form) {

        // EDD not active
        if (!function_exists('edd_insert_payment')) {
            return;
        }

        // Do an initial check to make sure there are downloads connected to the form.
        $has_edd_download = false;
        foreach ($form['fields'] as $field) {
            if (!empty($field['eddDownload'])) {
                $has_edd_download = true;
            }
        }

        // If there are no EDD downloads connected, get outta here.
        if (empty($has_edd_download)) {
	        $this->log_debug( 'No EDD-connected downloads' );
            return;
        }

        // We need to re-fetch the entry since the payment gateways
        // will have modified the entry since submitted by the user
        $entry = GFAPI::get_entry( $entry['id'] );

        $this->r(array('$entry' => $entry), false, '$entry in `send_purchase_to_edd`, (' . __LINE__ . ')');

        // Prevent double-logging
        if (function_exists('gform_update_meta')) {
            $entry_payment_id_meta = gform_get_meta($entry['id'], 'edd_payment_id');
            if (!empty($entry_payment_id_meta)) {
                $this->log_debug( 'Payment already recorded for entry in `send_purchase_to_edd`, (' . __LINE__ . ')', array('$entry' => $entry, '$entry_payment_id_meta' => $entry_payment_id_meta) );
                return;
            }
        }

	    $data = $this->get_edd_data_array_from_entry( $entry, $form );

        // If there are no downloads connected, get outta here.
        if (empty($data['downloads'])) {
	        $this->log_debug( 'No downloads in the data' );
            return;
        }

        $price = isset($entry['payment_amount']) ? GFCommon::to_number($entry['payment_amount']) : $data['total'];

        // Create the purchase array
        $purchase_data = array(
            'price' => $price, // Remove currency, commas
            'purchase_key' => strtolower(md5(uniqid())), // Random key
            'user_id' => $data['user_info']['id'],
            'user_email' => $data['user_info']['email'],
            'user_info' => $data['user_info'],
            'currency' => $entry['currency'],
            'downloads' => $data['downloads'],
            'cart_details' => $data['cart_details'],
            'gateway' => $data['gateway'],
            'status' => 'pending' // start with pending so we can call the update function, which logs all stats
        );

		// TODO: If it's a free trial, don't charge any money
		// #'transaction_type' => $entry['transaction_type'],

        // Add the payment
        $payment_id = edd_insert_payment($purchase_data);

        add_post_meta($payment_id, '_edd_gf_entry_id', $entry['id']);

	    // TODO: Store API mode so that the links to Stripe are correct (include the /test/ path). Requires Gravity Forms core update.

        // Was there a transaction ID to add to `edd_insert_payment_note()`?
        $transaction_id_note = empty($entry['transaction_id']) ? '' : sprintf(__('Transaction ID: %s - ', 'edd-gf'), $entry['transaction_id']);

        // Set transaction ID
	    edd_set_payment_transaction_id( $payment_id, rgar( $entry, 'transaction_id' ) );

        // Record the GF Entry
        edd_insert_payment_note($payment_id, sprintf(__('%s%sView Gravity Forms Entry%s', 'edd-gf'), $transaction_id_note, '<a href="' . admin_url(sprintf('admin.php?page=gf_entries&amp;view=entry&amp;id=%d&amp;lid=%d', $form['id'], $entry['id'])) . '">', '</a>'));

        // Record the EDD purchase in GF
        if (class_exists('GFFormsModel') && is_callable('GFFormsModel::add_note')) {
            GFFormsModel::add_note($entry['id'], -1, __('Easy Digital Downloads', 'edd-gf'), sprintf(__('Created Payment ID %d in Easy Digital Downloads', 'edd-gf'), $payment_id));
        }

        // Add Gravity Forms entry meta
        if (function_exists('gform_update_meta')) {
            gform_update_meta($entry['id'], 'edd_payment_id', $payment_id);
        }

        // Make sure GF and EDD have statuses that mean the same things.
        $status = $this->get_payment_status_from_gf_status($entry['payment_status']);

        // If a purchase was free, set status to Active
        $status = $this->set_free_payment_status($status, $purchase_data);

        // increase stats and log earnings
        edd_update_payment_status($payment_id, $status);

        // Set session purchase data, so redirecting to the confirmation page works properly
        edd_set_purchase_session($purchase_data);

        $this->r($purchase_data, false, 'Purchase Data (Line ' . __LINE__ . ')');

        $this->r(get_post($payment_id), false, 'Payment Object (Line ' . __LINE__ . ')');

	    /**
	     * @used-by KWS_GF_EDD_Subscriptions::maybe_start_subscription
	     */
        do_action( 'edd_gf_payment_added', $entry, $payment_id, $purchase_data );

    }

    /**
     * Check if the purchase was free. If so, set status to `publish`
     * @param string $status        Existing EDD status
     * @param array $purchase_data Purchase data array
     * @return  string Purchase status; `publish` if free purchase. Previous status otherwise.
     */
    private function set_free_payment_status($status, $purchase_data) {

        if (empty($purchase_data['price'])) {
            return 'publish';
        }

        return $status;
    }

    /**
     * Process payment for older payment addons. Alias for KWS_GF_EDD::post_payment_callback()
     *
     * @see KWS_GF_EDD::post_payment_callback() Alias
     *
     * @param  array $feed           Feed settings
     * @param  array $entry          Gravity Forms entry array
     * @param  string $status         Payment status
     * @return void
     */
    public function gform_post_payment_status($feed, $entry, $status) {

        $this->post_payment_callback($entry, array('payment_status' => $status));
    }

    /**
     * Update the payment status after payment is modified in Gravity Forms
     *
     * $action = array(
     *     'type' => 'cancel_subscription',     // required
     *     'transaction_id' => '',              // required (if payment)
     *     'subscription_id' => '',             // required (if subscription)
     *     'amount' => '0.00',                  // required (some exceptions)
     *     'entry_id' => 1,                     // required (some exceptions)
     *     'transaction_type' => '',
     *     'payment_status' => '',
     *     'note' => ''
     * );
     *
     * @param  array $entry  Gravity Forms entry array
     * @param  array $action Array describing the action (see method description above)
     * @uses  edd_update_payment_status()
     * @see  GFPaymentAddOn::process_callback_action()
     * @return void
     */
    public function post_payment_callback($entry = array(), $action = array()) {
        global $wpdb;

        // EDD not active
        if (!function_exists('edd_update_payment_status')) {

            $this->r('edd_update_payment_status not available');

            return;
        }

        // Get the payment ID from the entry ID
        $payment_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_edd_gf_entry_id' AND meta_value = %s LIMIT 1", $entry['id']));

        // Payment's not been officially inserted yet
        if (empty($payment_id)) {

            $this->r('_edd_gf_entry_id not yet set; send_purchase_to_edd() has not run. Wait for it!');

            return;
        }

        // Make sure GF and EDD have statuses that mean the same things.
        $payment_status = $this->get_payment_status_from_gf_status($action['payment_status']);

        $this->log_debug( sprintf('Setting $payment_id to %s and $payment_status to %s.', $payment_id, $payment_status));

        $this->log_debug( 'Data passed to post_payment_callback', array('$entry' => $entry, '$payment_status' => $payment_status, '$action' => $action) );

        // Update the payment status
        edd_update_payment_status($payment_id, $payment_status);
    }

    /**
     * function to update edd transaction id 
     * 
     * @param array $entry The Entry Object
     * @param array $action The Action Object
     * @param array mixed $result The Result Object
     */
    public function update_edd_transaction_id($entry, $action, $result) {

        // add transaction id in complete payment or start subscription payment
        if ($action['type'] === 'complete_payment' || $action['type'] === 'create_subscription') {
            // get download id for entry
            $payment_id = gform_get_meta($entry['id'], 'edd_payment_id', true);
            $transaction_id = (!empty($action['transaction_id'])) ? $action['transaction_id'] : $action['subscription_id'];
            if (!empty($payment_id) && !empty($transaction_id)) {
                edd_set_payment_transaction_id($payment_id, $transaction_id);
            }
        }
    }

	/**
	 * Push errors messages to the Gravity Forms Logging Tool
	 * @param string $title
	 * @param null $data
	 */
    public function log_error( $title = '', $value = null ) {

	    $data = '';
	    if( null !== $value ) {
	    	$data = print_r($value, true );
	    }

	    if ( defined( 'DOING_EDD_GF_TESTS' ) && defined('EDD_GF_TESTS_DEBUG') && EDD_GF_TESTS_DEBUG ) {
		    echo "\n" . $title . "\n";
		    var_dump( $data );
		    echo "\n";
	    }

	    $this->logger->log_error( $title . "\n" . $data );
    }

	/**
	 * Push errors messages to the Gravity Forms Logging Tool
	 * @param string $title
	 * @param null $data
	 */
	public function log_debug( $title = '', $data = null ) {

		if( null !== $data ) {
			$data = print_r( $data, true );
		}

		$this->logger->log_debug( $title . "\n" . $data );
	}

    /**
     * @deprecated 2.0 Use log_debug or log_error instead
     *
     * Print debug output if the self::debug CONST is set to true
     *
     * @param  mixed  $data The output you would like to print
     * @param  boolean $die   Exit after outputting
     * @param  string $title The label to give to the output
     */
    public function r( $data, $die = false, $title = null) {

    	$this->log_debug( $title, $data );

    }

    /**
     * Return numbers of product in an entry
     *
     * @param array $products entry products 
     * @param array $coupons entry coupons
     * 
     * @return int number of products in entry
     */
    public function entry_num_products($products, $coupons) {

	    $products_num = 0;

	    if ( $coupons && $products ) {
		    foreach ( $products as $product_key => $product ) {
			    if ( ! in_array( $product_key, $coupons ) ) {
				    $products_num += intval( $product['quantity'] );
			    }
		    }
	    } else if ( $products ) {
		    foreach ( $products as $product_key => $product ) {
			    $products_num += intval( $product['quantity'] );
		    }
	    }

	    return $products_num;
    }

    /**
     * function to return available coupons for entry
     *
     * @param  array $entry GF Entry array
     * @param  array $form  GF Form array
     * 
     * @return array of available coupons
     */
    public function get_entry_coupons($form, $entry) {

	    $entry_coupons = array();
	    // check if gravity form coupons class exist
	    if ( class_exists( 'GFCoupons' ) ) {
		    // get coupons for entry
		    $coupon_obj    = function_exists( 'gf_coupons' ) ? gf_coupons() : new GFCoupons();
		    $entry_coupons = $coupon_obj->get_submitted_coupon_codes( $form, $entry );
	    }

	    return (array) $entry_coupons;
    }

    /**
     * function to get entry coupon details precent and flat values
     * 
     * @param  array $entry GF Entry array
     * @param  array $entry_coupons  GF Form Available Coupons array
     * @param  int $products_num  Number of products in form
     *
     * @return array of applied coupons to gravity form entry
     */
    public function get_entry_coupon_details($form, $entry, $form_prods) {

	    $coupon_details = array();

	    // get coupons for entry
	    $entry_coupons = $this->get_entry_coupons( $form, $entry );

	    // get number of products in entry
	    $products_num = $this->entry_num_products( $form_prods, $entry_coupons );

	    // check if there are available coupons
	    if ( $entry_coupons && class_exists( 'GFCoupons' ) ) {
		    $coupon_obj  = new GFCoupons();
		    $coupon_data = $coupon_obj->get_coupons_by_codes( $entry_coupons, $form );
		    if ( $coupon_data ) {
			    foreach ( $coupon_data as $coupon_meta ) {
				    // save percentage coupon data
				    if ( $coupon_meta['type'] == 'percentage' ) {
					    $coupon_details['percentage'] = ( isset( $coupon_details['percentage'] ) && $coupon_details['percentage'] ) ? $coupon_details['percentage'] + $coupon_meta['amount'] : $coupon_meta['amount'];
				    } else {
					    if ( $products_num ) {
						    $coupon_details['flat'] = ( isset( $coupon_details['flat'] ) && $coupon_details['flat'] ) ? $coupon_details['flat'] + ( $coupon_meta['amount'] / $products_num ) : ( $coupon_meta['amount'] / $products_num );
					    } else {
						    $coupon_details['flat'] = ( isset( $coupon_details['flat'] ) && $coupon_details['flat'] ) ? $coupon_details['flat'] + $coupon_meta['amount'] : $coupon_meta['amount'];
					    }
				    }
			    }
		    }
	    }

	    return $coupon_details;
    }

    /**
     * Get discount codes used in an entry
     *
     * @param  array $entry GF Entry array
     * @param  array $form  GF Form array
     * 
     * @return array Array of coupon codes in a submission
     */
    function get_entry_discount( $form, $entry ) {

	    $user_info_discount = array();

	    if ( class_exists( 'GFCoupons' ) ) {
		    // get entry coupon codes to set user data array
		    $entry_coupons      = $this->get_entry_coupons( $form, $entry );
		    $user_info_discount = ( isset( $entry_coupons ) && $entry_coupons ) ? $entry_coupons : array();
	    }

	    $user_info_discount = array_filter( $user_info_discount );

	    return $user_info_discount;
    }

}

new KWS_GF_EDD;