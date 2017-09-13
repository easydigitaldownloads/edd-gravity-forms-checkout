<?php

/**
 * Testing the Gravity Forms Field Object.
 *
 * Note: all the database operations are wrapped in a transaction and rolled back at teh end of each test.
 * So when debugging it's best not to stop the execution completely - best to let the tests run till the end.
 * This also means that if you check the database directly in the middle of debugging a test you won't see any changes - it'll appear empty.
 *
 * @group testsuite
 */
class Tests_GF_Field extends GF_UnitTestCase {

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

	function test_object_notation() {
		$form  = GFAPI::get_form( $this->form_id );
		$field = $form['fields'][0];

		$this->assertEquals( 'Radio', $field->label );

		$field->testProperty = 'Gravity';
		$this->assertEquals( 'Gravity', $field->testProperty );

		$field->myTestArray                     = array();
		$field->myTestArray['level1']           = array();
		$field->myTestArray['level1']['level2'] = array();
		$is_array                               = is_array( $field->myTestArray['level1']['level2'] );
		$this->assertTrue( $is_array );

		$field->myTestArray['level1']['level2']['name'] = 'Steve';
		$name                                           = $field->myTestArray['level1']['level2']['name'];
		$this->assertEquals( 'Steve', $name );

		unset( $field->myTestArray['level1']['level2']['name'] );
		$is_set = isset( $field->myTestArray['level1']['level2']['name'] );
		$this->assertFalse( $is_set );

		$field->myTestArray['level1']['level2']['name'] = 'Alex';
		$new_name                                       = $field->myTestArray['level1']['level2']['name'];
		$this->assertEquals( 'Alex', $new_name );

	}

	function test_array_notation() {
		$form  = GFAPI::get_form( $this->form_id );
		$field = $form['fields'][0];

		/**
		 * Some backwards compatibility is possible. For example:
		 */
		$this->assertEquals( 'Radio', $field['label'] );

		$field['testProperty'] = 'Gravity';
		$this->assertEquals( 'Gravity', $field['testProperty'] );

		$field['myTestArray'] = array();
		$is_array             = is_array( $field['myTestArray'] );
		$this->assertTrue( $is_array );

		$new_array            = array(
			'name' => 'Steve',
		);
		$field['myTestArray'] = $new_array;
		$name                 = $field['myTestArray']['name'];
		$this->assertEquals( 'Steve', $name );


		/**
		 * However, some backwards compatibility is not possible.
		 * Indirect modification of array will not work due to a bug in ArrayAccess until PHP 5.3.4.
		 * They both will trigger an "Indirect modification of overloaded element" notice and will have no effect on the property
		 */
		/*
		 * These tests will cause PHPUnit to bail if Xdebug is active.
		 *
		$field['myTestArray']['name'] = 'Alex'; // has no effect and generates a notice
		$name                         = $field['myTestArray']['name'];
		$this->assertEquals( 'Steve', $name );    // unchanged

		unset( $field['myTestArray']['name'] );
		$is_set = isset( $field['myTestArray']['name'] );     // has no effect and generates a notice
		$this->assertTrue( $is_set );                         // unchanged
		$this->assertEquals( 'Steve', $name );                // unchanged
		*/
		/**
		 * There is a way to force this to work by replacing the arrays with nested objects.
		 * However, is_array(), array_values() and array_keys() won't work.
		 * There's workaround for that too; special methods in GFField that perform the same functions.
		 * However, this is pointless because it's just as easy to change the property access from array to object notation
		 * The workaround also causes havoc with the JSON format as arrays are now objects.
		 */


		/**
		 * This problem doesn't exist with object notation
		 */

		$field->myTestArray['name'] = 'Alex';
		$name = $field['myTestArray']['name'];
		$this->assertEquals( 'Alex', $name );

		$field->myTestArray['name'] = array();
		$field->myTestArray['name']['surname'] = 'Cancado';
		$surname = $field['myTestArray']['name']['surname'];
		$this->assertEquals( 'Cancado', $surname );


		unset( $field->myTestArray['name'] );
		$is_set = isset( $field->myTestArray['name'] );
		$this->assertFalse( $is_set );
	}

	function test_non_existent_properties_object_notation() {
		$form  = GFAPI::get_form( $this->form_id );
		$field = $form['fields'][0];

		$is_set = isset( $field->testNonExistentProperty );
		$this->assertFalse( $is_set );

		$empty_string = $field->testNonExistentProperty2; // Accessing a non-existent property adds it automatically as empty string.
		$this->assertEquals( '', $empty_string );
	}

	function test_non_existent_properties_array_notation() {
		$form  = GFAPI::get_form( $this->form_id );
		$field = $form['fields'][0];

		$is_set = isset( $field['testNonExistentProperty'] );
		$this->assertFalse( $is_set );

		$empty_string = $field['testNonExistentProperty2']; // Accessing a non-existent property adds it automatically as empty string.
		$this->assertEquals( '', $empty_string );
	}

	function test_json_round_trip() {
		$form  = GFAPI::get_form( $this->form_id );
		$field = $form['fields'][0];

		$field_json       = json_encode( $field );
		$field_array      = json_decode( $field_json, true );
		$new_field_object = GF_Fields::create( $field_array );
		$this->assertEquals( $field, $new_field_object );

		$form_json  = json_encode( $form );
		$form_array = json_decode( $form_json, true );
		foreach ( $form_array['fields'] as &$field ) {
			$field = GF_Fields::create( $field );
		}
		$this->assertEquals( $form, $form_array );

	}
}
