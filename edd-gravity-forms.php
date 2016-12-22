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
            define('EDD_GF_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }

        // Load the default language files
        load_plugin_textdomain('edd-gf', false, dirname(plugin_basename(EDD_GF_PLUGIN_FILE)) . '/languages/');

        $this->init();
    }

    /**
     * Include the admin script and non-admin hooks
     *
     * @todo Check for whether Gravity Forms exists.
     */
    private function init() {

        include( EDD_GF_PLUGIN_DIR . 'logging.php' );
        include( EDD_GF_PLUGIN_DIR . 'admin.php' );
        // add gravity form edd user fields 
        include( EDD_GF_PLUGIN_DIR . 'edd-user-fields.php' );

        // Run the EDD functionality
        add_action("gform_after_submission", array($this, 'send_purchase_to_edd'), PHP_INT_MAX, 2);

        // Backward compatibility
        add_action('gform_post_payment_status', array($this, 'gform_post_payment_status'), 10, 3);

        // Update whenever GF updates payment statii
        add_action('gform_post_payment_completed', array($this, 'post_payment_callback'), 10, 2);
        add_action('gform_post_payment_refunded', array($this, 'post_payment_callback'), 10, 2);

        // add edd subscription when GF subscription complete
        add_action('gform_post_subscription_started', array($this, 'edd_subscription_started'), 10, 2);
        add_action('gform_post_add_subscription_payment', array($this, 'edd_subscription_payment'), 10, 2);

        // action to set edd transaction id 
        add_action('gform_post_payment_callback', array($this, 'update_edd_transaction_id'), 10, 3);

        /**
         * Check for plugin updates. Built into EDD version 1.9+
         */
        if (class_exists('EDD_License')) {
            new EDD_License(EDD_GF_PLUGIN_FILE, self::name, self::version, 'Katz Web Services, Inc.');
        }
    }

    /**
     * Make GF and EDD statuses match
     * @return array Existing statuses
     * @filter edd_gf_default_status Modify the default status when there's no status match. Default: `pending`. Passes default and `$status` arguments.
     * @filter edd_gf_payment_status Override the status when there is a match. Passes matched value and `$status` arguments.
     * @todo  Ensure the default status is right when there's no status match.
     */
    public function get_payment_status_from_gf_status($status) {

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
            return NULL;
        }

        $options = NULL;

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

        // get coupons for entry 
        $coupon_obj = new GFCoupons();
        $entry_coupons = $coupon_obj->get_submitted_coupon_codes($form, $entry);
        // get number of products in entry
        $products_num = $this->entry_num_products($product_info['products'], $entry_coupons);

        foreach ($product_info['products'] as $product_field_id => $product) {

            $field = $this->get_form_field_by_id($product_field_id, $form);

            // If product in coupons array 
            if (in_array($product_field_id, $entry_coupons)) :
                // get coupon data
                $coupon_code = $product['name'];
                $coupon_data = $coupon_obj->get_coupons_by_codes(array($coupon_code), $form);
                if ($coupon_data):
                    foreach ($coupon_data as $coupon_code => $coupon_meta):
                        // save percentage coupon data
                        if ($coupon_meta['type'] == 'percentage'):
                            $coupon_details['percentage'] = (isset($coupon_details['percentage']) && $coupon_details['percentage']) ? $coupon_details['percentage'] + $coupon_meta['amount'] : $coupon_meta['amount'];
                        else:
                            if ($products_num):
                                $coupon_details['flat'] = (isset($coupon_details['flat']) && $coupon_details['flat']) ? $coupon_details['flat'] + ($coupon_meta['amount'] / $products_num) : ($coupon_meta['amount'] / $products_num);
                            else:
                                $coupon_details['flat'] = (isset($coupon_details['flat']) && $coupon_details['flat']) ? $coupon_details['flat'] + $coupon_meta['amount'] : $coupon_meta['amount'];
                            endif;
                        endif;
                    endforeach;
                endif;
            endif;

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

        $total = 0;

        foreach ($downloads as $download) {

            // When buying multiple products with price variants,
            // we pass the download id as "{$download_id}.{$gf_input_id}" so
            // the array key doesn't get overwritten.
            $download_id = absint($download['id']);

            $quantity = ( absint($download['quantity']) > 0 ? absint($download['quantity']) : 1 );

            $prod_id = $download['product_field_id'];

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

                // update cart total with coupon code val 
                if ($coupon_details['percentage']):
                    $item_discount = $item_price * ( $coupon_details['percentage'] / 100 );
                else:
                    $item_discount = $coupon_details['flat'];
                endif;

                $cart_details[] = array(
                    'name' => get_the_title($download_id),
                    'id' => $download_id,
                    'item_number' => $item,
                    'price' => $item_price,
                    'tax' => NULL,
                    'quantity' => 1,
                    'discount' => $item_discount,
                    'product_field_id' => $prod_id
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

        if ($data['total'] < 0) {

            $this->r($data, false, '$data[total] was negative (' . $data['total'] . ') - resetting to $0.00 (Line ' . __LINE__ . ')');

            $data['total'] = 0;
        }

        $this->r($data, false, '$data returned from get_edd_data_array_from_entry() (Line ' . __LINE__ . ')');

        return $data;
    }

    private function get_user_info_from_submission($form, $entry) {

        $user_info = array();

        $edd_fields = (isset($form["edd-fields"]) && $form["edd-fields"]) ? $form["edd-fields"] : array();

        // if not name or email fields settings selected then get them from from fields 
        if ((!isset($edd_fields['name_field']) || !$edd_fields['name_field'] ) || (!isset($edd_fields['email_field']) || !$edd_fields['email_field'] )):
            foreach ($form['fields'] as $field) {

                switch ($field['type']) {
                    case 'email':
                        $user_info['email'] = $field['id'];
                        break;
                    case 'name':
                        if (is_array($field['inputs']) && !empty($field['inputs'])) {
                            foreach ($field['inputs'] as $input) {
                                if (floatval($input['id']) === floatval($field['id'] . '.3')) {
                                    $user_info['first_name'] = $input['id'];
                                } else if (floatval($input['id']) === floatval($field['id'] . '.6')) {
                                    $user_info['last_name'] = $input['id'];
                                }
                            }
                        } else {
                            // For a Simple Name field, show full name 	
                            $user_info['display_name'] = $field['id'];
                        }
                        break;
                }
            }
        endif;

        // get name field from form settings
        if (isset($edd_fields['name_field']) && $edd_fields['name_field']):
            $name_field = $edd_fields['name_field'];
            if (isset($entry[$name_field . '.3']) && isset($entry[$name_field . '.6'])):
                $user_info['first_name'] = $name_field . '.3';
                $user_info['last_name'] = $name_field . '.6';
            else:
                $user_info['display_name'] = $name_field;
            endif;
        endif;

        // get email field from form settings
        if (isset($edd_fields['email_field']) && $edd_fields['email_field']):
            $user_info['email'] = $edd_fields['email_field'];
        endif;

        // 		
        // SET ADDITIONAL USER DETAILS FROM GRAVITY FORM SUBMISSION 		
        // 
        foreach ($user_info as $key => $entry_key) {

            // If somehow the $entry doesn't have the $entry_key item set, keep going 	
            if (empty($entry["{$entry_key}"])) {
                continue;
            }
            // Convert the Gravity Forms Field ID keys into Entry values.
            // See `$user_info['first_name']` code above for examples. 

            $user_info[$key] = $entry["{$entry_key}"];
        }
        return $user_info;
    }

    /**
     * Get user info from the entry
     * @param  array $form  Gravity Forms form array
     * @param  array $entry Gravity Forms entry array
     * @return array        array with user data. Keys include: 'id' (int user ID), 'email' (string user email), 'first_name', 'last_name', 'discount' (empty)
     */
    function get_user_info($form, $entry) {

        $user_id = -1;

        $user_info = $this->get_user_info_from_submission($form, $entry);

        if (is_user_logged_in()) {

            // Get the $current_user WP_User object
            $current_user = wp_get_current_user();

            $user_id = get_current_user_id();

            // If you enable `$overwrite`, the purchase will use data from the submitted form (if available), not from the WP_User object
            $user_info = array(
                'id' => $user_id, // Always use the user ID
                // If email, first & last name exist in Gravity Forms, use those
                'email' => !empty($user_info['email']) ? $user_info['email'] : $current_user->user_email,
                'first_name' => !empty($user_info['first_name']) ? $user_info['first_name'] : $current_user->user_firstname,
                'last_name' => !empty($user_info['last_name']) ? $user_info['last_name'] : $current_user->user_lastname,
                'display_name' => !empty($user_info['display_name']) ? $user_info['display_name'] : $current_user->display_name,
            );
        } else {

            $user_id = -1;

            //
            // User is not logged in, but the email exists
            //
            if (!empty($user_info['email'])) {

                $wp_user = get_user_by('email', $user_info['email']);

                // If the user email exists as a user
                if (!empty($wp_user)) {
                    $user_id = $wp_user->ID;
                    // If first & last name exist in Gravity Forms, use those
                    $user_info['first_name'] = !empty($user_info['first_name']) ? $user_info['first_name'] : $wp_user->user_firstname;
                    $user_info['last_name'] = !empty($user_info['last_name']) ? $user_info['last_name'] : $wp_user->user_firstname;
                    $user_info['display_name'] = !empty($user_info['display_name']) ? $user_info['display_name'] : $wp_user->display_name;
                }
            }
        }

        // get entry coupon codes to set user data array
        $coupon_obj = new GFCoupons();
        $entry_coupons = $coupon_obj->get_submitted_coupon_codes($form, $entry);
        $user_info_discount = (isset($entry_coupons) && $entry_coupons) ? $entry_coupons : 'none';

        $default_user_info = array(
            'email' => NULL,
            'first_name' => NULL,
            'last_name' => NULL,
            'display_name' => NULL,
        );

        $user_info = wp_parse_args($user_info, $default_user_info);

        // Set user data array
        $user_info = array(
            'id' => $user_id,
            'email' => $user_info['email'],
            'first_name' => $user_info['first_name'],
            'last_name' => $user_info['last_name'],
            'display_name' => $user_info['display_name'],
            'discount' => $user_info_discount,
        );

        return $user_info;
    }

    /**
     * Take a GF submission and add a purcase to EDD.
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
            return;
        }

        // We need to re-fetch the entry since the payment gateways
        // will have modified the entry since submitted by the user
        $entry = GFFormsModel::get_lead($entry['id']);

        $this->r(array('$entry' => $entry), false, '$entry in `send_purchase_to_edd`, (' . __LINE__ . ')');

        // Prevent double-logging
        if (function_exists('gform_update_meta')) {
            $entry_payment_id_meta = gform_get_meta($entry['id'], 'edd_payment_id');
            if (!empty($entry_payment_id_meta)) {
                $this->r(array('$entry' => $entry, '$entry_payment_id_meta' => $entry_payment_id_meta), true, 'Payment already recorded for entry in `send_purchase_to_edd`, (' . __LINE__ . ')');
                return;
            }
        }

        $data = $this->get_edd_data_array_from_entry($entry, $form);

        // If there are no downloads connected, get outta here.
        if (empty($data['downloads'])) {
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
            #'transaction_type' => $entry['transaction_type'],
            #'discount'	   => $data['discount_codes'], // TODO: Figure out discount code integration
            'status' => 'pending' // start with pending so we can call the update function, which logs all stats
        );

        // Add the payment
        $payment_id = edd_insert_payment($purchase_data);

        add_post_meta($payment_id, '_edd_gf_entry_id', $entry['id']);

        // Was there a transaction ID to add to `edd_insert_payment_note()`?
        $transaction_id_note = empty($entry['transaction_id']) ? '' : sprintf(__('Transaction ID: %s - ', 'edd-gf'), $entry['transaction_id']);

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

        $this->r(get_post($payment_id), true, 'Payment Object (Line ' . __LINE__ . ')');
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

        $this->r(sprintf('Setting $payment_id to %s and $payment_status to %s.', $payment_id, $payment_status));

        $this->r(array('$entry' => $entry, '$payment_status' => $payment_status, '$action' => $action), false, 'Data passed to post_payment_callback');

        // Update the payment status
        edd_update_payment_status($payment_id, $payment_status);
    }

    /**
     * Add edd subscription when GF subscription started 
     * 
     * @param array $entry Entry Object
     * @param array $subscription The new Subscription object
     */
    public function edd_subscription_started($entry, $subscription) {

        if (class_exists('GFPayPal')):
            // get form paypal configuraion
            $paypal_conf = GFPayPal::get_config_by_entry($entry);
            if (isset($paypal_conf['meta']['transactionType']) && $paypal_conf['meta']['transactionType'] == 'subscription'):
                // get entry payment id 
                $entry_payment = get_posts(array(
                    'post_type' => 'edd_payment',
                    'posts_per_page' => 1,
                    'meta_key' => '_edd_gf_entry_id',
                    'meta_value' => $entry['id']
                        )
                );
                if ($entry_payment):
                    $payment_id = $entry_payment[0]->ID;
                    // set subscription payment
                    $payment = new EDD_Payment($payment_id);

                    // Set subscription_payment
                    $user_id = get_current_user_id();
                    $payment->update_meta('_edd_subscription_payment', true);

                    // get customer id 
                    $customer_id = get_post_meta($payment_id, '_edd_payment_customer_id', TRUE);
                    $subscriber = new EDD_Recurring_Subscriber($customer_id);

                    // get GF by form id
                    $form = GFAPI::get_form($entry['form_id']);
                    // get entry data
                    $data = $this->get_edd_data_array_from_entry($entry, $form);
                    $cart_details = $data['cart_details'];

                    // get gf configuraion trial 
                    $trial_amount = $trial_prod = NULL;
                    $trial_subscription = FALSE;
                    $trial_period = '';

                    if (isset($paypal_conf['meta']['trial_enabled']) && $paypal_conf['meta']['trial_enabled'] == '1'):
                        $trial_subscription = TRUE;
                        // if trial amount is selected 
                        if (isset($paypal_conf['meta']['trial_product']) && $paypal_conf['meta']['trial_product'] == 'enter_amount'):
                            $trial_amount = intval(ereg_replace("[^0-9]", "", $paypal_conf['meta']['trial_amount']));
                        else:
                            $trial_prod = $paypal_conf['meta']['trial_product'];
                        endif;
                        // get trial period 
                        if (isset($paypal_conf['meta']['trialPeriod_length']) && $paypal_conf['meta']['trialPeriod_length'] && $paypal_conf['meta']['trialPeriod_unit'])
                            $trial_period = $paypal_conf['meta']['trialPeriod_length'] . ' ' . $paypal_conf['meta']['trialPeriod_unit'];
                    endif;

                    // get billing cycle 
                    $recurring_len = $paypal_conf['meta']['billingCycle_length'] . ' ' . $paypal_conf['meta']['billingCycle_unit'];
                    $exp_date = Date('Y-m-d', strtotime($recurring_len . 's'));
                    // get recurring times
                    $recurring_times = (intval($paypal_conf['meta']['recurringTimes'])) ? intval($paypal_conf['meta']['recurringTimes']) : '';

                    // add edd subscription
                    if ($cart_details):
                        foreach ($cart_details as $cart_detail) :
                            // get product discount 
                            $prod_discount = (isset($cart_detail['discount']) && $cart_detail['discount'] ) ? $cart_detail['discount'] : 0;
                            // get product price 
                            $prod_price = (isset($cart_detail['price']) && $cart_detail['price'] ) ? $cart_detail['price'] : 0;
                            $product_total = $prod_price - $prod_discount;

                            // check if trial product
                            if ($trial_prod && $trial_prod == intval($cart_detail['product_field_id'])):
                                $trial_amount = 0;
                            endif;

                            // get initial amount 
                            $initial_amount = ($trial_subscription && $trial_amount != NULL) ? $trial_amount : $product_total;

                            $args = array(
                                'product_id' => $cart_detail['item_number']['id'],
                                'user_id' => $customer_id,
                                'parent_payment_id' => $payment_id,
                                'status' => 'Active',
                                'period' => $recurring_times,
                                'initial_amount' => $initial_amount,
                                'recurring_amount' => $product_total,
                                'bill_times' => $recurring_times,
                                'expiration' => $exp_date,
                                'trial_period' => $trial_period,
                                'profile_id' => $customer_id,
                                'transaction_id' => $subscription['subscription_id'],
                            );

                            // set args 
                            $sub = $subscriber->add_subscription($args);

                            if ($trial_period) {
                                $subscriber->add_meta('edd_recurring_trials', $entry['id']);
                            }

                        endforeach;
                    endif;
                endif;
            endif;
        endif;
    }

    /**
     * Add edd subscription when GF subscription renew 
     * 
     * @param array $entry The Entry Object
     * @param array $action The Action Object
     */
    public function edd_subscription_payment($entry, $action) {

        // get download id for entry
        $payment_id = get_post_meta($entry['id'], 'edd_payment_id', TRUE);
        if ($payment_id):
            // get subscription id 
            global $wpdb;
            $sub_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}edd_subscriptions WHERE parent_payment_id=$payment_id ");
            if ($sub_id):
                // get amount and transaction id
                $amount = ( isset($action['amount']) ) ? edd_sanitize_amount($action['amount']) : '0.00';
                $txn_id = (!empty($action['transaction_id']) ) ? $action['transaction_id'] : $action['subscription_id'];

                // add edd subscription
                $sub = new EDD_Subscription($sub_id);
                $payment = $sub->add_payment(array(
                    'amount' => $amount,
                    'transaction_id' => $txn_id
                ));
                $sub->renew();
            endif;

        endif;
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
        if ($action['type'] == 'complete_payment' || $action['type'] == 'create_subscription'):
            // get download id for entry
            $payment_id = get_post_meta($entry['id'], 'edd_payment_id', TRUE);
            $transaction_id = (!empty($action['transaction_id'])) ? $action['transaction_id'] : $action['subscription_id'];

            edd_set_payment_transaction_id($payment_id, $transaction_id);
        endif;
    }

    /**
     * Print debug output if $this->debug is set to true
     * @param  mixed  $value The output you would like to print
     * @param  boolean $die   Exit after outputting
     */
    private function r($value, $die = false, $title = NULL) {

        // Push debug messages to the Gravity Forms Logging Tool
        do_action('edd_gf_log_debug', $title . "\n" . print_r($value, true));

        if (current_user_can('administrator') && self::debug) {

            // Output buffering fatal errors when seeing `print_r()`
            if (ob_get_level() > 0) {
                ob_end_flush();
            }

            if ($title) {
                echo '<h3>' . $title . '</h3>';
            }
            echo '<pre>';
            print_r($value);
            echo '</pre>';
            if ($die) {
                die();
            }
        }
    }

    /**
     * function to return numbers of product in entry 
     * @param array $products entry products 
     * @param array $coupons entry coupons
     * 
     * @return int number of products in entry
     */
    public function entry_num_products($products, $coupons) {

        $products_num = 0;

        if ($products):
            foreach ($products as $product_key => $product):
                if (!in_array($product_key, $coupons)):
                    $products_num += intval($product['quantity']);
                endif;
            endforeach;
        endif;

        return $products_num;
    }

}

new KWS_GF_EDD;
