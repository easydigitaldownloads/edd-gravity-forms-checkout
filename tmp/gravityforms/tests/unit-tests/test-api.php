<?php

/**
 * Testing the Gravity Forms API Functions.
 *
 * Note: all the database operations are wrapped in a transaction and rolled back at teh end of each test.
 * So when debugging it's best not to stop the execution completely - best to let the tests run till the end.
 * This also means that if you check the database directly in the middle of debugging a test you won't see any changes - it'll appear empty.
 *
 * @group testsuite
 */
class Tests_GFAPI extends GF_UnitTestCase {

	/**
	 * @var GF_UnitTest_Factory
	 */
	protected $factory;

	/**
	 * @var int
	 */
	protected $form_id;

	function setUp() {
		parent::setUp();
		$this->form_id = $this->factory->form->create();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_add_form() {

		// checks the form added by default in the setup
		$form_id = $this->get_form_id();
		$this->assertfalse( is_wp_error( $form_id ) );
		$this->assertGreaterThan( 0, $form_id );

		// adds another form
		$form = $this->factory->form->create_and_get();
		$this->assertArrayHasKey( 'id', $form );
		$this->assertArrayHasKey( 'title', $form );
		$this->assertArrayHasKey( 'fields', $form );
	}
	
	function test_duplicate_form() {

		// Checks the form added by default in the setup.
		$form_id = $this->get_form_id();
		$this->assertfalse( is_wp_error( $form_id ) );
		$this->assertGreaterThan( 0, $form_id );

		// Get first form.
		$first_form = $this->factory->form->get_form_by_id( $form_id );

		// Duplicate form.
		$second_form_id = GFAPI::duplicate_form( $form_id );
		$second_form    = $this->factory->form->get_form_by_id( $second_form_id );

		// Check name.
		$this->assertEquals( $first_form['title'] . ' (1)', $second_form['title'] );

		// Duplicate form again.
		$third_form_id = GFAPI::duplicate_form( $form_id );
		$third_form    = $this->factory->form->get_form_by_id( $third_form_id );

		// Check name.
		$this->assertEquals( $first_form['title'] . ' (2)', $third_form['title'] );

	}

	function test_update_form() {

		$form_id       = $this->get_form_id();
		$form          = $this->factory->form->get_form_by_id( $form_id );
		$new_title     = 'New title';
		$form['title'] = $new_title;
		$this->factory->form->update_object( $form_id, $form );
		GFFormsModel::flush_current_forms();
		$updated_form = GFAPI::get_form( $form_id );
		$this->assertEquals( $new_title, $updated_form['title'] );
	}

	function test_update_form_not_found() {

		$form_id       = $this->get_form_id();
		$form          = $this->factory->form->get_form_by_id( $form_id );
		$new_title     = 'New title';
		$form['title'] = $new_title;
		$result        = GFAPI::update_form( $form, 999 );

		$this->assertWPError( $result );
	}

	function test_update_form_property() {

		$form_id = $this->get_form_id();
		GFAPI::update_form_property( $form_id, 'is_trash', 1 );
		GFFormsModel::flush_current_forms();
		$updated_form = GFAPI::get_form( $form_id );

		$this->assertEquals( 1, $updated_form['is_trash'] );
	}

	function test_update_forms_property() {

		$form_id = $this->get_form_id();
		GFAPI::update_forms_property( array( $form_id, $this->form_id ), 'is_trash', 1 );
		GFFormsModel::flush_current_forms();
		$updated_form = GFAPI::get_form( $this->form_id );

		$this->assertEquals( 1, $updated_form['is_trash'] );

		$updated_form2 = GFAPI::get_form( $form_id );

		$this->assertEquals( 1, $updated_form2['is_trash'] );
	}

	function test_add_entry() {
		$entry_id = $this->factory->entry->create( array( 'form_id' => $this->form_id, '1' => 'Second Choice', '5' => 'My text' ) );

		$this->assertfalse( is_wp_error( $entry_id ) );
		$this->assertGreaterThan( 0, $entry_id );
	}

	function test_add_entry_time_field() {
		$entry_id = GFAPI::add_entry( array( 'form_id' => $this->form_id, '11' => '11:11' ) );

		$entry = GFAPI::get_entry( $entry_id );

		$this->assertGreaterThan( 0, $entry_id );
		$this->assertEquals( '11:11', $entry['11'] );

	}

	function test_entry_count() {
		$form_id = $this->get_form_id();

		$this->factory->entry->create_many_random( 5, $form_id );

		$this->assertEquals( 5, GFAPI::count_entries( $form_id ) );
	}


	function test_simple_get_entries() {
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 5, $form_id );
		$entries = GFAPI::get_entries( $form_id );

		$this->assertEquals( 5, count( $entries ) );
		$this->assertArrayHasKey( 'id', $entries[0] );
		$this->assertArrayHasKey( 'form_id', $entries[1] );
		$this->assertArrayHasKey( 'ip', $entries[2] );
		$this->assertArrayHasKey( '1', $entries[0] );
	}


	function test_update_entry() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		$entries[0]['13.3'] = 'London';
		$entry_id           = $entries[0]['id'];
		$this->factory->entry->update_object( $entry_id, $entries[0] );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'London', $updated_entry['13.3'] );
		$this->assertEquals( $entries[0], $updated_entry );
	}

	function test_update_entry_field(){
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		$entry_id           = $entries[0]['id'];

		//Deleting
		GFAPI::update_entry_field( $entry_id, '13.3', '' );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( '', $updated_entry['13.3'] );

		//Inserting
		GFAPI::update_entry_field( $entry_id, '13.3', 'Belo Horizonte' );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'Belo Horizonte', $updated_entry['13.3'] );

		//Updating
		GFAPI::update_entry_field( $entry_id, '13.3', 'London' );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'London', $updated_entry['13.3'] );

	}

	function test_update_entry_time_field(){
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );
		$entry_id           = $entries[0]['id'];
		$entry           = $entries[0];

		$new_time = '11:11';
		$entry['11'] = $new_time;

		GFAPI::update_entry( $entry );
		GFFormsModel::flush_current_lead();
		$updated_entry = GFAPI::get_entry( $entry_id );

		$this->assertEquals( $new_time, $updated_entry['11'] );
	}

	function test_update_entry_field_html(){
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		$entry_id           = $entries[0]['id'];

		//Updating
		GFAPI::update_entry_field( $entry_id, '13.3', 'New <b>York</b><div>New York</div><script>alert("hi")</script>' );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'New <b>York</b><div>New York</div><script>alert("hi")</script>', $updated_entry['13.3'] );

		$updated_entry['13.3'] = 'Madrid <div>Pozuelo</div>';
		GFAPI::update_entry( $updated_entry );
		GFFormsModel::flush_current_lead();
		$new_updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'Madrid <div>Pozuelo</div>', $new_updated_entry['13.3'] );

	}

	function test_update_entry_field_high_field_id(){
		global $wpdb;

		$form_id = $this->get_form_id();
		$field_id = 1000;
		$form = GFAPI::get_form( $form_id );
		$form['fields'][1]->id = $field_id;
		$inputs = $form['fields'][1]->inputs;
		$inputs[0]['id'] = $field_id + '.1';
		$inputs[1]['id'] = $field_id + '.2';
		$inputs[2]['id'] = $field_id + '.3';
		$form['fields'][1]->inputs = $inputs;
		GFAPI::update_form( $form );
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		$new_val = 'testing_update_high_field_id';
		$entry_id = $entries[0]['id'];

		$field_id = '1000.1';

		$input_id_min = (float) $field_id - 0.0001;
		$input_id_max = (float) $field_id + 0.0001;

		$lead_details_table_name = GFFormsModel::get_lead_details_table_name();

		$lead_detail_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$lead_details_table_name} WHERE lead_id=%d AND field_number BETWEEN %s AND %s", $entry_id, $input_id_min, $input_id_max ) );

		$this->assertNotNull( $lead_detail_id );

		GFAPI::update_entry_field( $entry_id, $field_id, $new_val );

		$updated_entry = GFAPI::get_entry( $entry_id );

		$this->assertEquals( $new_val, $updated_entry[ $field_id ] );

	}

	function test_update_entry_different_id() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id ); // Creates entries with random values

		$entries = GFAPI::get_entries( $form_id );

		// Set some known values for both entries and make sure they update correctly
		$entries[0]['13.3'] = 'foo';
		$entries[0]['13.4'] = 'bar';
		$entries[0]['13.5'] = '';
		GFAPI::update_entry( $entries[0] );
		GFFormsModel::flush_current_lead();
		$updated_entry = GFAPI::get_entry( $entries[0]['id'] );
		$this->assertEquals( 'foo', $updated_entry['13.3'] );
		$this->assertEquals( 'bar', $updated_entry['13.4'] );
		$this->assertEquals( '', $updated_entry['13.5'] );

		$entries[1]['13.3'] = '';
		$entries[1]['13.4'] = 'alpha';
		$entries[1]['13.5'] = 'beta';
		GFAPI::update_entry( $entries[1] );
		$updated_entry = GFAPI::get_entry( $entries[1]['id'] );
		$this->assertEquals( '', $updated_entry['13.3'] );
		$this->assertEquals( 'alpha', $updated_entry['13.4'] );
		$this->assertEquals( 'beta', $updated_entry['13.5'] );


		// Change the entry ID of the first entry to the ID of the second
		$entry_id = $entries[1]['id'];
		// Update the second entry with the data from the first
		GFAPI::update_entry( $entries[0], $entry_id );
		GFFormsModel::flush_current_lead();

		$updated_entry = GFAPI::get_entry( $entry_id );


		$this->assertEquals( 'foo', $updated_entry['13.3'] );
		$this->assertEquals( 'bar', $updated_entry['13.4'] );
		$this->assertEquals( '', $updated_entry['13.5'] );


		// Double check the first entry remains intact
		$updated_entry = GFAPI::get_entry( $entries[0]['id'] );
		$this->assertEquals( 'foo', $updated_entry['13.3'] );
		$this->assertEquals( 'bar', $updated_entry['13.4'] );
		$this->assertEquals( '', $updated_entry['13.5'] );

	}

	function test_update_entry_missing_values_get_deleted() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		unset( $entries[0]['13.3'] );
		$entry_id = $entries[0]['id'];
		$this->factory->entry->update_object( $entry_id, $entries[0] );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( '', $updated_entry['13.3'] );
	}

	function test_trash_entry() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		$entries[0]['status'] = 'trash';
		$entry_id             = $entries[0]['id'];
		$this->factory->entry->update_object( $entry_id, $entries[0] );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'trash', $updated_entry['status'] );
	}

	function test_get_entries_by_status() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 10, $form_id );

		$entries                   = GFAPI::get_entries( $form_id );
		$entry_to_update           = $entries[5];
		$entry_to_update['status'] = 'trash';
		$entry_id                  = $entry_to_update['id'];
		$this->factory->entry->update_object( $entry_id, $entry_to_update );
		GFFormsModel::flush_current_lead();
		$search_criteria = array();
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );

		$search_criteria = array(
			'status' => 'active',
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 9, $total_count );
		$this->assertEquals( 9, count( $entries ) );

		$search_criteria = array(
			'status' => 'trash',
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );
		$this->assertEquals( 1, count( $entries ) );
	}

	function test_get_entries_by_isnot_status() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 10, $form_id );

		$entries                   = GFAPI::get_entries( $form_id );
		$entry_to_update           = $entries[5];
		$entry_to_update['status'] = 'trash';
		$entry_id                  = $entry_to_update['id'];
		$this->factory->entry->update_object( $entry_id, $entry_to_update );
		GFFormsModel::flush_current_lead();
		$search_criteria = array();
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );

		$search_criteria = array(
			'status' => 'active',
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 9, $total_count );
		$this->assertEquals( 9, count( $entries ) );

		$search_criteria = array(
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key'   => 'status',
					'operator' => 'isnot',
					'value' => 'active',
				),
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );
		$this->assertEquals( 1, count( $entries ) );
	}



	function test_update_entry_property() {

		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );

		$entries = GFAPI::get_entries( $form_id );

		$entry_id = $entries[0]['id'];
		GFAPI::update_entry_property( $entry_id, 'user_agent', 'Chrome' );
		GFFormsModel::flush_current_lead();
		$updated_entry = $this->factory->entry->get_object_by_id( $entry_id );
		$this->assertEquals( 'Chrome', $updated_entry['user_agent'] );
	}

	function test_search_criteria_any() {
		$this->_create_entries();

		// mode = any
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '1',
					'value' => 'Second Choice',
				),
				array(
					'key'   => '5',
					'value' => 'My text',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 40, $total_count );
	}

	function test_search_criteria_all() {
		$this->_create_entries();

		// mode = all
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key'   => '1',
					'value' => 'Second Choice',
				),
				array(
					'key'   => '5',
					'value' => 'My text',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_checkboxes_all() {
		$this->_create_entries();


		// checkboxes: all
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key'   => '2',
					'value' => 'First Choice',
				),
				array(
					'key'   => '2',
					'value' => 'Second Choice',
				)
			)
		);

		$sorting     = array();
		$paging      = array();
		$total_count = 0;
		$entries     = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_checkboxes_any() {
		$this->_create_entries();

		// checkboxes: any
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2',
					'value' => 'Second Choice',
				),
				array(
					'key'   => '2',
					'value' => 'Third Choice',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
	}

	function test_search_criteria_checkboxes_is() {
		$this->_create_entries();

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2',
					'operator' => '=',
					'value' => 'First Choice',
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2',
					'operator' => '=',
					'value' => 'Second Choice',
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );


		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2',
					'operator' => '=',
					'value' => 'Third Choice',
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_checkboxes_is_not() {
		$this->_create_entries();

		// 'is not' and '<>' will only work for specific inputs. They're not supported for integer field keys.

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2.1',
					'operator' => '<>',
					'value' => 'First Choice',
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );


		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key'   => '2.1',
					'operator' => '<>',
					'value' => 'First Choice',
				),
				array(
					'key'   => '2.2',
					'operator' => '<>',
					'value' => 'Second Choice',
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_checkboxes_in() {
		$this->_create_entries();

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2',
					'operator' => 'in',
					'value' => array( 'First Choice' ),
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => '2',
					'operator' => 'in',
					'value' => array( 'Second Choice' ),
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );


		// checkboxes: numeric key
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => 2,
					'operator' => 'in',
					'value' => array( 'Second Choice' ),
				),
			),
		);
		$sorting         = array();
		$paging = array( 'offset' => 0, 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );
	}

	function test_search_criteria_checkboxes_not_in() {
		// Not currently supported
	}

	function test_search_criteria_date_range_single_day() {
		$this->_create_entries();

		// date range: single day
		$search_criteria = array(
			'status'     => 'active',
			'start_date' => '2013-11-28',
			'end_date'   => '2013-11-28',
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20, count( $entries ) );
	}

	function test_search_criteria_date_range_with_times() {
		$this->_create_entries();

		// date range: with times
		$search_criteria = array(
			'status'     => 'active',
			'start_date' => '2013-11-29 11:30:00',
			'end_date'   => '2013-11-29 12:30:00',
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20,  count( $entries ) );
	}

	function test_search_criteria_date_range_with_utc_offset() {
		update_option( 'gmt_offset', -5 );

		$this->_create_entries();

		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];
		$entry['date_created'] = '2016-06-02 00:10:15'; // UTC
		$entry['1'] = 'Hello';
		GFAPI::update_entry( $entry );
		$entry = $entries[1];
		$entry['date_created'] = '2016-06-02 23:30:22'; // UTC
		$entry['1'] = 'Bye';
		GFAPI::update_entry( $entry );

		// date range: UTC -5
		$search_criteria = array(
			'status'     => 'active',
			'start_date' => '2016-06-02',
			'end_date'   => '2016-06-02',
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Bye', $entries[0]['1'] );
		$this->assertEquals( 1, count( $entries ) );
		$this->assertEquals( 1, $total_count );

		// date range: with times UTC -5
		$search_criteria = array(
			'status'     => 'active',
			'start_date' => '2016-06-01 00:10:00 ',
			'end_date'   => '2016-06-02 00:03:22',
		);

		$entries = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Hello', $entries[0]['1'] );
		$this->assertEquals( 1, count( $entries ) );
		$this->assertEquals( 1, $total_count );

	}

	function test_search_criteria_global_contains() {
		$this->_create_entries();

		// Global: contains
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'value'    => 'Different',
					'operator' => 'contains',
				)
			),
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_global_is() {
		$this->_create_entries();
		// Global: is
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'value' => 'Different text',
				)
			),
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_field_isnot() {
		$this->_create_entries();
		// Global: is
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key' => '1',
					'operator' => 'isnot', // accepts both <> and isnot
					'value' => 'First Choice',
				),
				array(
					'key' => '13.6',
					'operator' => '<>', // accepts both <> and isnot
					'value' => 'Spain',
				)
			)
		);
		$sorting         = array();
		$paging          = array( 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );
	}

	function test_search_criteria_radio_value_contains() {
		$this->_create_entries();

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '1',
					'value'    => 'first',
					'operator' => 'contains',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '1',
					'value'    => 'choi',
					'operator' => 'contains',
				)
			)
		);
		$sorting         = array();
		$paging          = array( 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 50, $total_count );
		$this->assertEquals( 50, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '1',
					'value'    => 'second',
					'operator' => 'contains',
				)
			)
		);
		$sorting         = array();
		$paging          = array( 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 40, $total_count );
		$this->assertEquals( 40, count( $entries ) );
	}

	function test_search_criteria_radio_value_contains_with_multiple_filters(){
		$this->_create_entries();
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '1',
					'value'    => 'second',
					'operator' => 'contains',
				),
				array(
					'key' => '1',
					'value'    => 'choice',
					'operator' => 'contains',
				),
			)
		);
		$sorting         = array();
		$paging          = array( 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 50, $total_count );
		$this->assertEquals( 50, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key' => '1',
					'value'    => 'first',
					'operator' => 'contains',
				),
				array(
					'key' => '1',
					'value'    => 'choice',
					'operator' => 'contains',
				),
			)
		);
		$sorting         = array();
		$paging          = array( 'page_size' => 100 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 10, $total_count );
		$this->assertEquals( 10, count( $entries ) );
	}

	function test_search_criteria_global_including_choice_texts() {

		$poll_form_json = file_get_contents( dirname( __FILE__ ) . '/../data/forms/poll.json' );
		$poll_form      = json_decode( $poll_form_json, true );
		$poll_form_id   = GFAPI::add_form( $poll_form );

		$entries_json = file_get_contents( dirname( __FILE__ ) . '/../data/forms/poll_entries.json' );
		$entries      = json_decode( $entries_json, true );

		GFAPI::add_entries( $entries, $poll_form_id );

		$this->_create_entries();

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'value' => 'Second Radio Choice',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $poll_form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 6, $total_count );
		$this->assertEquals( 6, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'value' => 'Second Drop down',
					'operator' => 'contains',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $poll_form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 3, $total_count );
		$this->assertEquals( 3, count( $entries ) );


		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'value' => 'Second Radio',
					'operator' => 'contains',
				),
				array(
					'value' => 'Second Drop down',
					'operator' => 'contains',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $poll_form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 7, $total_count );
		$this->assertEquals( 7, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'value' => 'Second Radio',
					'operator' => 'contains',
				),
				array(
					'value' => 'Second Drop down',
					'operator' => 'contains',
				)
			)
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $poll_form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );
	}

	function test_search_criteria_sorting_by_field() {
		$this->_create_entries();

		// Sorting DESC by the country input field of field 13 (address)
		$search_criteria = array();
		$paging          = array();
		$sorting         = array( 'key' => '13.6', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'United States', $entries[0]['13.6'] );

		// Sorting ASC by the country input field of field 13 (address)
		$search_criteria = array();
		$paging          = array();
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Brazil', $entries[0]['13.6'] );

		// paging
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 5 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Brazil', $entries[0]['13.6'] );

		// paging
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 15 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Canada', $entries[0]['13.6'] );

		// paging
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 0 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'United States', $entries[0]['13.6'] );

		// paging
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 15 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'United Kingdom', $entries[0]['13.6'] );

		// paging
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 25 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Spain', $entries[0]['13.6'] );


		// numerical sorting
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 0 );
		$sorting         = array( 'key' => '8', 'direction' => 'DESC', 'is_numeric' => true );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( '5', $entries[0]['8'] );

		// numerical sorting
		$search_criteria = array();
		$paging          = array( 'page_size' => 10, 'offset' => 0 );
		$sorting         = array( 'key' => '8', 'direction' => 'ASC', 'is_numeric' => true );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( '1', $entries[0]['8'] );
	}

	function test_search_criteria_sorting_by_entry_meta() {
		global $_entry_meta;

		// Not numeric
		$_entry_meta[ $this->form_id ] = array(
			'my_entry_meta' => array(
				'label'      => 'My Entry Meta',
				'is_numeric' => false,
			),
		);

		$this->_create_entries();

		$paging  = array( 'page_size' => 100, 'offset' => 0 );

		$entries = GFAPI::get_entries( $this->form_id, null, null, $paging );
		$total_count_entries = count( $entries );
		gform_update_meta( $entries[0]['id'], 'my_entry_meta', 'aaaa' );
		gform_update_meta( $entries[1]['id'], 'my_entry_meta', 'bbbb' );
		gform_update_meta( $entries[2]['id'], 'my_entry_meta', 'cccc' );
		gform_update_meta( $entries[3]['id'], 'my_entry_meta', 'dddd' );

		// Sorting DESC by my_entry_meta
		$search_criteria = array();
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array( 'key' => 'my_entry_meta', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries_found         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( $total_count_entries, count( $entries_found ) );
		$this->assertEquals( $total_count_entries, $total_count );
		$this->assertEquals( 'dddd', $entries_found[0]['my_entry_meta'] );
		$this->assertEquals( 'cccc', $entries_found[1]['my_entry_meta'] );
	}

	function test_search_criteria_status_sorting_by_property() {
		$this->factory->entry->create( array( 'form_id' => $this->form_id, 'date_created' => '2014-01-28 10:00', 'status' => 'active', '1' => 'Second Choice', '5' => 'text a' ) );
		$this->factory->entry->create( array( 'form_id' => $this->form_id, 'date_created' => '2014-01-28 11:00', 'status' => 'active', '1' => 'Second Choice', '5' => 'text b' ) );
		$entry_id = $this->factory->entry->create( array( 'form_id' => $this->form_id, 'date_created' => '2014-01-28 12:00', 'status' => 'trash', '1' => 'Second Choice', '5' => 'text c' ) );

		// double check the trashed entry was created successfully
		$entry = GFAPI::get_entry( $entry_id );
		$this->assertEquals( 'text c', $entry['5'] );


		$search_criteria = array( 'status' => 'active' );
		$paging          = array();
		$sorting         = array( 'key' => 'date_created', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );
		$this->assertEquals( 'text b', $entries[0]['5'] );


		$search_criteria = array( 'status' => 'active' );
		$paging          = array();
		$sorting         = array( 'key' => 'id', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );
		$this->assertEquals( 'text a', $entries[0]['5'] );

		$search_criteria = array( 'status' => 'trash' );
		$paging          = array();
		$sorting         = array( 'key' => 'id', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );
		$this->assertEquals( 1, count( $entries ) );
		$this->assertEquals( 'text c', $entries[0]['5'] );

	}

	function test_search_criteria_status_sorting_by_field() {
		$this->factory->entry->create( array( 'form_id' => $this->form_id, 'date_created' => '2014-01-28 10:00', 'status' => 'active', '1' => 'Second Choice', '5' => 'text a' ) );
		$this->factory->entry->create( array( 'form_id' => $this->form_id, 'date_created' => '2014-01-28 11:00', 'status' => 'active', '1' => 'Second Choice', '5' => 'text b' ) );
		$this->factory->entry->create( array( 'form_id' => $this->form_id, 'date_created' => '2014-01-28 12:00', 'status' => 'trash', '1' => 'Second Choice', '5' => 'text c' ) );

		$search_criteria = array( 'status' => 'active' );
		$paging          = array();
		$sorting         = array( 'key' => '5', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );
		$this->assertEquals( 'text b', $entries[0]['5'] );

		$search_criteria = array( 'status' => 'active' );
		$paging          = array();
		$sorting         = array( 'key' => '5', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );
		$this->assertEquals( 'text a', $entries[0]['5'] );
	}

	function test_search_criteria_numeric_field() {

		GFAPI::add_entry( array( 'form_id' => $this->form_id, '1' => 'test', '5' => '70', '8' => 7 ) );
		GFAPI::add_entry( array( 'form_id' => $this->form_id, '1' => 'test', '5' => '80', '8' => 8 ) );
		GFAPI::add_entry( array( 'form_id' => $this->form_id, '1' => 'test', '5' => '90', '8' => 9 ) );
		GFAPI::add_entry( array( 'form_id' => $this->form_id, '1' => 'test', '5' => '100', '8' => 10 ) );
		GFAPI::add_entry( array( 'form_id' => $this->form_id, '1' => 'test', '5' => '110', '8' => 11 ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '8', // Field ID 8 is a number field
					'operator' => '>',
					'value' => 8,
				),
			)
		);
		$paging          = array();
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 3, $total_count );
		$this->assertEquals( 3, count( $entries ) );
		$this->assertEquals( '10', $entries[1]['8'] );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '8',  // Field ID 8 is a number field
					'operator' => '=',
					'value' => 8,
				),
			)
		);
		$paging          = array();
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );
		$this->assertEquals( 1, count( $entries ) );


		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '8',
					'operator' => 'contains',  // treat like a string
					'value' => 1,
				),
			)
		);
		$paging          = array();
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '5', // Field ID 5 is a single line tect field
					'operator' => '>',
					'value' => 80,
					'is_numeric' => true,  // also supports treating any field as numeric
				),
			)
		);
		$paging          = array();
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 3, $total_count );
		$this->assertEquals( 3, count( $entries ) );

		// Double check that text fields don't get treated as numeric
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '5', // Field ID 5 is a single line tect field
					'operator' => '>',
					'value' => 80,
				),
			)
		);
		$paging          = array();
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );
		$this->assertEquals( 1, count( $entries ) );
	}

	function test_search_criteria_array_values_as_search_terms(){
		$this->_create_entries();

		// IN
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => '13.6',
					'operator' => 'in',
					'value' => array( 'Spain', 'United Kingdom', 'Brazil' ),
				),
			)
		);
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );

		// NOT IN
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => '13.6',
					'operator' => 'not in',
					'value' => array( 'Spain', 'United Kingdom', 'Brazil' ),
				),
			)
		);
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20, count( $entries ) );

		// Support for Checkboxes: IN
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => '2',
					'operator' => 'IN',
					'value' => array( 'Second Choice', 'Third Choice' ),
				),
			)
		);
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );

		// Support for Checkboxes: NOT IN
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => '2',
					'operator' => 'not in',
					'value' => array( 'Second Choice', 'Third Choice' ),
				),
			)
		);
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20, count( $entries ) );

		// mode = all
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'all',
				array(
					'key' => '13.6',
					'operator' => 'IN',
					'value' => array( 'Spain', 'Brazil' ),
				),
				array(
					'key' => '13.6',
					'operator' => 'NOT IN',
					'value' => array( 'United Kingdom', 'United States' ),
				),
			)
		);
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 20, $total_count );
		$this->assertEquals( 20, count( $entries ) );


		// mode = any
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key' => '13.6',
					'operator' => 'IN',
					'value' => array( 'Spain', 'Brazil' ),
				),
				array(
					'key' => '13.6',
					'operator' => 'NOT IN',
					'value' => array( 'United Kingdom', 'United States' ),
				),
			)
		);
		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 30, $total_count );
		$this->assertEquals( 30, count( $entries ) );


		// Set up entry meta

		gform_update_meta( $entries[0]['id'], 'test_meta', 'zaragoza' );
		gform_update_meta( $entries[1]['id'], 'test_meta', 'zaragoza' );
		gform_update_meta( $entries[2]['id'], 'test_meta', 'madrid' );
		gform_update_meta( $entries[3]['id'], 'test_meta', 'barcelona' );

		// Entry meta: IN

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 'test_meta',
					'operator' => 'IN',
					'value' => array( 'zaragoza', 'barcelona' ),
				),
			)
		);

		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 3, $total_count );
		$this->assertEquals( 3, count( $entries ) );

		// Entry meta: NOT IN

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 'test_meta',
					'operator' => 'NOT IN',
					'value' => array( 'barcelona', 'madrid' ),
				),
			)
		);

		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 2, $total_count );
		$this->assertEquals( 2, count( $entries ) );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 'test_meta',
					'operator' => 'NOT IN',
					'value' => array( 'zaragoza', 'madrid' ),
				),
			)
		);

		$paging          = array( 'page_size' => 100, 'offset' => 0 );
		$sorting         = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );
		$this->assertEquals( 1, count( $entries ) );
	}

	function test_search_criteria_sorting_by_property() {
		$this->_create_entries();

		$search_criteria = array();
		$paging          = array();
		$sorting         = array( 'key' => 'date_created', 'direction' => 'DESC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Canada', $entries[0]['13.6'] );

		$search_criteria = array();
		$paging          = array();
		$sorting         = array( 'key' => 'id', 'direction' => 'ASC' );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Spain', $entries[0]['13.6'] );
	}

	function test_search_criteria_list_field() {
		$this->_create_entries();

		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$list_field_val = array( 'red', 'green', 'blue', 'hyphenated-word', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa rocketgenius aa' );
		$list_field_val_serialized = serialize( $list_field_val );
		$entry[15] = $list_field_val_serialized;

		GFAPI::update_entry( $entry );

		$updated_entry = GFAPI::get_entry( $entry['id'] );
		$updated_list_val_seerialized = $updated_entry[15];

		$this->assertEquals( $list_field_val_serialized, $updated_list_val_seerialized );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 15,
					'operator' => 'contains',
					'value' => 'hyphenated-word',
				),
			)
		);
		$sorting = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );

		// Make sure long values being stored and retrieved. 'rocketgeius' is toward the end of the long string
		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 15,
					'operator' => 'contains',
					'value' => 'rocketgenius',
				),
			)
		);
		$sorting = array();
		$paging          = array();
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 1, $total_count );

	}

	function test_search_criteria_paging() {
		$this->_create_entries();

		// Paging
		$search_criteria = array();
		$sorting         = array();
		$paging          = array( 'offset' => 20, 'page_size' => 10 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'United Kingdom', $entries[0]['13.6'] );
		$this->assertEquals( 10, count( $entries ) );
		$this->assertEquals( 50, $total_count );
	}

	function test_search_criteria_sorting_with_paging() {
		$this->_create_entries();

		// Paging
		$search_criteria = array();
		$sorting         = array( 'key' => '13.6', 'direction' => 'ASC' );
		$paging          = array( 'offset' => 20, 'page_size' => 10 );
		$total_count     = 0;
		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Spain', $entries[0]['13.6'] );
		$this->assertEquals( 10, count( $entries ) );
		$this->assertEquals( 50, $total_count );
	}

	function test_search_criteria_date_created() {
		$this->_create_entries();

		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];
		$entry['date_created'] = '2016-06-02 00:10:12'; // UTC
		$entry['1'] = 'Hello';
		GFAPI::update_entry( $entry );
		$entry = $entries[1];
		$entry['date_created'] = '2016-06-02 23:30:00'; // UTC
		$entry['1'] = 'Bye';
		GFAPI::update_entry( $entry );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 'date_created',
					'value' => '2016-06-02',
				),
			),
		);
		$sorting         = array();
		$paging          = array();
		$total_count     = 0;

		// Time zone UTC+0

		$entries         = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Hello', $entries[0]['1'] );
		$this->assertEquals( 'Bye', $entries[1]['1'] );
		$this->assertEquals( 2, count( $entries ) );
		$this->assertEquals( 2, $total_count );

		// Time zone UTC-5

		update_option( 'gmt_offset', -5 );

		$entries = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Bye', $entries[0]['1'] );
		$this->assertEquals( 1, count( $entries ) );
		$this->assertEquals( 1, $total_count );

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array(
					'key' => 'date_created',
					'value' => '2016-06-01',
				),
			),
		);

		$entries = GFAPI::get_entries( $this->form_id, $search_criteria, $sorting, $paging, $total_count );
		$this->assertEquals( 'Hello', $entries[0]['1'] );
		$this->assertEquals( 1, count( $entries ) );
		$this->assertEquals( 1, $total_count );

	}

	function test_delete_entry() {
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many_random( 2, $form_id );
		$entries = GFAPI::get_entries( $form_id );
		GFAPI::delete_entry( $entries[0]['id'] );
		GFFormsModel::flush_current_lead();
		$this->assertEquals( 1, GFAPI::count_entries( $form_id ) );
	}


	function test_delete_form() {
		GFAPI::delete_form( $this->form_id );
		GFFormsModel::flush_current_forms();
		$form = GFAPI::get_form( $this->form_id );
		$this->assertFalse( $form );
	}

	function test_modify_notifications(){
		$form = GFAPI::get_form($this->form_id);

		$this->assertEquals('stevehenty@gmail.com', $form['notifications']['52246fd7af858']['to'] );
		$form['notifications']['52246fd7af858']['to'] = 'steve@rocketgenius.com';
		GFAPI::update_form($form);

		GFFormsModel::flush_current_forms();
		$form = GFAPI::get_form($this->form_id);
		$this->assertEquals('steve@rocketgenius.com', $form['notifications']['52246fd7af858']['to'] );
	}

	function test_modify_confirmations(){
		$form = GFAPI::get_form($this->form_id);

		$this->assertEquals('Default Confirmation', $form['confirmations']['514b78146ede6']['name'] );
		$form['confirmations']['514b78146ede6']['name'] = 'My new confirmation';
		GFAPI::update_form($form);

		GFFormsModel::flush_current_forms();
		$form = GFAPI::get_form($this->form_id);
		$this->assertEquals('My new confirmation', $form['confirmations']['514b78146ede6']['name'] );
	}

	function test_gform_get_meta_values_for_entries() {
		$this->_create_entries();

		$entries = GFAPI::get_entries( $this->form_id );

		$entry_id = $entries[0]['id'];

		// Test with hyphen and underscore
		$meta_key = 'my-meta_key';

		$meta_value = 'any value !"·$%&//()?';

		gform_add_meta( $entry_id, $meta_key, $meta_value );

		$values = gform_get_meta_values_for_entries( array( $entry_id ), array( $meta_key ) );

		$this->assertEquals( $meta_value, $values[0]->$meta_key );

	}

	/* HELPERS */
	function get_form_id() {
		return $this->form_id;
	}

	function _create_entries() {
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-28 11:00', '1' => 'Second Choice', '2.2' => 'Second Choice', '8' => '1', '13.6' => 'Spain' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-28 11:15', '1' => 'First Choice', '2.2' => 'Second Choice', '2.3' => 'Third Choice', '8' => '2', '13.6' => 'Brazil' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-29 12:00', '1' => 'Second Choice', '2.1' => 'First Choice', '8' => '3', '13.6' => 'United Kingdom' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-29 12:00', '1' => 'Second Choice', '2.1' => 'First Choice', '2.2' => 'Second Choice', '5' => 'My text', '8' => '4', '13.6' => 'United States' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-29 13:00', '1' => 'Second Choice', '5' => 'Different text', '8' => '5', '13.6' => 'Canada' ) );
	}

}
