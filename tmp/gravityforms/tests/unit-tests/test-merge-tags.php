<?php

/**
 * Testing the Gravity Forms Field Merge Tags.
 *
 * Note: all the database operations are wrapped in a transaction and rolled back at teh end of each test.
 * So when debugging it's best not to stop the execution completely - best to let the tests run till the end.
 * This also means that if you check the database directly in the middle of debugging a test you won't see any changes - it'll appear empty.
 *
 * @group testsuite
 */
class Tests_MergeTags extends GF_UnitTestCase {

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

	function test_name_merge_tags() {
		$field_config = array (
			'type' => 'name',
			'id' => 3,
			'label' => 'Name',
			'adminLabel' => '',
			'isRequired' => false,
			'size' => 'medium',
			'errorMessage' => '',
			'nameFormat' => 'advanced',
			'inputs' =>
				array (
					0 =>
						array (
							'id' => '3.2',
							'label' => 'Prefix',
							'name' => '',
							'choices' =>
								array (
									0 =>
										array (
											'text' => 'Mr.',
											'value' => 'Mr.',
											'isSelected' => false,
											'price' => '',
										),
									1 =>
										array (
											'text' => 'Mrs.',
											'value' => 'Mrs.',
											'isSelected' => false,
											'price' => '',
										),
									2 =>
										array (
											'text' => 'Miss',
											'value' => 'Miss',
											'isSelected' => false,
											'price' => '',
										),
									3 =>
										array (
											'text' => 'Ms.',
											'value' => 'Ms.',
											'isSelected' => false,
											'price' => '',
										),
									4 =>
										array (
											'text' => 'Dr.',
											'value' => 'Dr.',
											'isSelected' => false,
											'price' => '',
										),
									5 =>
										array (
											'text' => 'Prof.',
											'value' => 'Prof.',
											'isSelected' => false,
											'price' => '',
										),
									6 =>
										array (
											'text' => 'Rev.',
											'value' => 'Rev.',
											'isSelected' => false,
											'price' => '',
										),
								),
							'isHidden' => true,
							'inputType' => 'radio',
						),
					1 =>
						array (
							'id' => '3.3',
							'label' => 'First',
							'name' => '',
						),
					2 =>
						array (
							'id' => '3.4',
							'label' => 'Middle',
							'name' => '',
							'isHidden' => true,
						),
					3 =>
						array (
							'id' => '3.6',
							'label' => 'Last',
							'name' => '',
						),
					4 =>
						array (
							'id' => '3.8',
							'label' => 'Suffix',
							'name' => '',
							'isHidden' => true,
						),
				),
			'formId' => 84,
			'description' => '',
			'allowsPrepopulate' => false,
			'inputMask' => false,
			'inputMaskValue' => '',
			'inputType' => '',
			'labelPlacement' => '',
			'descriptionPlacement' => '',
			'subLabelPlacement' => '',
			'placeholder' => '',
			'cssClass' => '',
			'inputName' => '',
			'adminOnly' => false,
			'noDuplicates' => false,
			'defaultValue' => '',
			'choices' => '',
			'conditionalLogic' => '',
			'failed_validation' => '',
			'productField' => '',
			'displayOnly' => '',
			'multipleFiles' => false,
			'maxFiles' => '',
			'calculationFormula' => '',
			'calculationRounding' => '',
			'enableCalculation' => '',
			'disableQuantity' => false,
			'displayAllCategories' => false,
			'useRichTextEditor' => false,
		);
		$field = new GF_Field_Name(
			$field_config
		);

		$value = 'Steve';

		$raw_value = array(
			'3.2' => '',
			'3.3' => '<b>Steve</b>',
			'3.4' => '',
			'3.6' => 'Henty',
			'3.8' => '',
		);

		$merge_tag = $field->get_value_merge_tag( $value, '3.3', array(), array( 'id' => $this->get_form_id() ), '', $raw_value, false, true, 'html', true );
		$this->assertEquals( 'Steve', $merge_tag );

		// check sanitization returns the correct value when HTML is allowed.
		add_filter( 'gform_allowable_tags', array( $this, 'filter_allow_tags_for_name_field' ) );

		$raw_value = array(
			'3.2' => '',
			'3.3' => '<b>Steve</b>',
			'3.4' => '',
			'3.6' => 'Henty',
			'3.8' => '',
		);
		$merge_tag = $field->get_value_merge_tag( $value, '3.3', array(), array( 'id' => $this->get_form_id() ), '', $raw_value, false, true, 'html', true );
		$this->assertEquals( '<b>Steve</b>', $merge_tag );
	}

	function filter_allow_tags_for_name_field( $allowable_tags ) {
		return '<b>';
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
