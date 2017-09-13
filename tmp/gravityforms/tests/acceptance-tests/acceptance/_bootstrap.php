<?php

global $wp_rewrite, $wpdb;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
$wp_rewrite->flush_rules();

GFFormsModel::drop_tables();

gf_upgrade()->maybe_upgrade();


function setup_gravity_forms_pages() {
	require_once( GFCommon::get_base_path() . '/export.php' );

	$form_filenames = glob( dirname( __FILE__ ) . '/../_data/forms/*.json' );

	$entry_count = 0;
	foreach ( $form_filenames as $filename ) {

		$forms = null;
		GFExport::import_file( $filename, $forms );

		// Getting Form ID. Assuming one form per file.
		$form_id = $forms[0]['id'];

		// Importing entries. Look for file with the same name, but in the entries folder
		$entry_file_name = str_replace( '/_data/forms/', '/_data/entries/', $filename );
		if ( file_exists( $entry_file_name ) ) {

			// File is a JSON containing an array of entries to be imported
			$entries = json_decode( file_get_contents( $entry_file_name ), true );
			if ( $entries && is_array( $entries ) ) {
				$entry_ids = GFAPI::add_entries( $entries, $form_id );
				$entry_count += count( $entry_ids );
			}
		}
	}

	$forms = GFAPI::get_forms();

	echo '  Forms: ' . count( $forms ) . "\n";
	echo '  Entries: ' . $entry_count . "\n\n";

	foreach ( $forms as $form ) {
		GFFormsModel::update_form_active( $form['id'], true );

		// Create page without ajax
		$page = array(
			'post_type'    => 'page',
			'post_content' => '[gravityform id=' . $form['id'] . ']',
			'post_name'    => sanitize_title_with_dashes( $form['title'] ) . ' - NO Ajax',
			'post_parent'  => 0,
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_title'   => $form['title'] . ' - NO Ajax',
		);

		wp_insert_post( $page );

		// Create page with ajax
		$page = array(
			'post_type'    => 'page',
			'post_content' => '[gravityform id=' . $form['id'] . ' ajax="true"]',
			'post_name'    => sanitize_title_with_dashes( $form['title'] ) . ' - WITH Ajax',
			'post_parent'  => 0,
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_title'   => $form['title'] . ' - WITH Ajax',
		);

		wp_insert_post( $page );
	}
}

setup_gravity_forms_pages();

wp_create_category( 'First test category', 0 );
wp_create_category( 'Second test category', 0 );

// add admins
function tests_create_testing_users() {
	$users = array( 'admin1', 'admin2', 'admin3' );
	foreach ( $users as $user ) {
		$userData = array(
			'user_login' => $user,
			'first_name' => 'First',
			'last_name'  => $user,
			'user_pass'  => $user,
			'user_email' => $user . '@mail.com',
			'user_url'   => '',
			'role'       => 'administrator'
		);
		wp_insert_user( $userData );
	}
}

tests_create_testing_users();

update_option( 'gform_pending_installation', false );
