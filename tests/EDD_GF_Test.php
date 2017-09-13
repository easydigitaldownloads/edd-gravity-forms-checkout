<?php

defined( 'DOING_EDD_GF_TESTS' ) || exit;

class EDD_GF_Test extends KWS_EDD_GF_UnitTestCase {

	/**
	 * @var array
	 */
	var $entry;

	/**
	 * @var array
	 */
	var $form;

	public function setUp() {
		parent::setUp();

		$this->is_everything_loaded();

		$this->setup_form_and_entry();
	}

	function setup_form( $form_overrides = array() ) {

		$defined_form_json = '{"title":"Test Multiple Names","description":"","labelPlacement":"top_label","descriptionPlacement":"below","button":{"type":"text","text":"Submit","imageUrl":""},"fields":[{"type":"name","id":19,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"19.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"19.3","label":"First","name":""},{"id":"19.4","label":"Middle","name":"","isHidden":true},{"id":"19.6","label":"Last","name":""},{"id":"19.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","is_field_hidden":"","enablePrice":"","multipleFiles":""},{"type":"name","id":18,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"18.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"18.3","label":"First","name":""},{"id":"18.4","label":"Middle","name":"","isHidden":true},{"id":"18.6","label":"Last","name":""},{"id":"18.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"name","id":17,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"17.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"17.3","label":"First","name":""},{"id":"17.4","label":"Middle","name":"","isHidden":true},{"id":"17.6","label":"Last","name":""},{"id":"17.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"email","id":8,"label":"Email 1","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"visible","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","emailConfirmEnabled":"","pageNumber":1,"displayOnly":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"eddDownload":"","eddHasVariables":false,"failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":""},{"type":"email","id":16,"label":"Email","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","emailConfirmEnabled":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"address","id":11,"label":"Address 1","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","addressType":"international","inputs":[{"id":"11.1","label":"Street Address","name":""},{"id":"11.2","label":"Address Line 2","name":""},{"id":"11.3","label":"City","name":""},{"id":"11.4","label":"State \/ Province","name":""},{"id":"11.5","label":"ZIP \/ Postal Code","name":""},{"id":"11.6","label":"Country","name":""}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"visible","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","defaultCountry":"","defaultProvince":"","productField":"","defaultState":"","enableCopyValuesOption":"","copyValuesOptionDefault":"","copyValuesOptionLabel":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"eddDownload":"","eddHasVariables":false,"pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enablePrice":""},{"type":"address","id":15,"label":"Address","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","addressType":"international","inputs":[{"id":"15.1","label":"Street Address","name":""},{"id":"15.2","label":"Address Line 2","name":""},{"id":"15.3","label":"City","name":""},{"id":"15.4","label":"State \/ Province","name":""},{"id":"15.5","label":"ZIP \/ Postal Code","name":""},{"id":"15.6","label":"Country","name":""}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","defaultCountry":"","defaultProvince":"","productField":"","hideCountry":"","defaultState":"","hideState":"","hideAddress2":"","enableCopyValuesOption":"","copyValuesOptionDefault":"","copyValuesOptionLabel":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enablePrice":"","multipleFiles":""},{"type":"product","id":3,"label":"The Download Product","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":[{"id":"3.1","label":"Name","name":""},{"id":"3.2","label":"Price","name":""},{"id":"3.3","label":"Quantity","name":""}],"inputType":"singleproduct","enablePrice":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","basePrice":"$10.00","disableQuantity":false,"multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":"","eddDownload":"8","eddHasVariables":false,"visibility":"visible","failed_validation":false,"validation_message":"","enableCopyValuesOption":""},{"type":"name","id":13,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"13.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"13.3","label":"First","name":""},{"id":"13.4","label":"Middle","name":"","isHidden":true},{"id":"13.6","label":"Last","name":""},{"id":"13.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""}],"version":"2.2.1.1","id":1,"useCurrentUserAsAuthor":true,"postContentTemplateEnabled":false,"postTitleTemplateEnabled":false,"postTitleTemplate":"","postContentTemplate":"","lastPageButton":null,"pagination":null,"firstPageCssClass":null,"edd-fields":{"name":"17","email":"8","address":"11"},"edd-gf":{"name_field":"6","email_field":"5"},"notifications":{"581cb798ed079":{"id":"581cb798ed079","to":"{admin_email}","name":"Admin Notification","event":"form_submission","toType":"email","subject":"New submission from {form_title}","message":"{all_fields}"}},"confirmations":{"581cb798ed5ad":{"id":"581cb798ed5ad","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}},"is_active":"1","date_created":"2016-11-04 16:30:16","is_trash":"0","confirmation":{"id":"581cb798ed5ad","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}}';

		$defined_form = json_decode( $defined_form_json, true );

		$form = wp_parse_args( $form_overrides, $defined_form );

		return $this->factory->form->create_and_get( $form );
	}

	/**
	 * @param array $entry_overrides
	 *
	 * @return mixed
	 */
	public function setup_entry( $entry_overrides = array() ) {
		$entry_json = '{"date_created":"2017-08-14 19:28:54","is_starred":0,"is_read":0,"ip":"172.17.0.1","source_url":"http:\/\/edd-gf.dev\/?gf_page=preview&id=1","post_id":null,"currency":"USD","payment_status":null,"payment_date":null,"transaction_id":null,"payment_amount":null,"payment_method":null,"is_fulfilled":null,"created_by":"1","transaction_type":null,"user_agent":"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit\/603.3.8 (KHTML, like Gecko) Version\/10.1.2 Safari\/603.3.8","status":"active","19.3":"Zeek 1","19.6":"Katz","18.3":"Zeek 2","18.6":"Katz","17.3":"Zeek 3","17.6":"Katz","8":"zeek+email1@katz.co","16":"zeek+email2@katz.co","11.1":"Address 1","11.3":"Denver","11.4":"co","11.5":"80205","11.6":"United States","15.1":"Address 2","15.3":"denver","15.4":"co","15.5":"80205","15.6":"United States","3.1":"The Download Product","3.2":"$10.00","3.3":"2","13.3":"Zack","13.6":"Katz","19.2":"","19.4":"","19.8":"","18.2":"","18.4":"","18.8":"","17.2":"","17.4":"","17.8":"","11.2":"","15.2":"","13.2":"","13.4":"","13.8":""}';

		$defined_entry = json_decode( $entry_json, true );

		$entry_overrides = array_filter( $entry_overrides );

		$entry = $defined_entry;

		if ( $entry_overrides ) {
			foreach ( $entry_overrides as $key => $value ) {
				$entry[ $key ] = $value;
			}
		}

		if( isset( $this->form ) && isset( $this->form['id'] ) && empty( $entry['form_id'] ) ) {
			$entry['form_id'] = $this->form['id'];
		}

		return $this->factory->entry->create_and_get( $entry );
	}

	function setup_form_and_entry( $form_overrides = array(), $entry_overrides = array() ) {

		$this->form = $this->setup_form( $form_overrides );


		$entry_overrides['form_id'] = $this->form['id'];

		$this->entry = $this->setup_entry( $entry_overrides );
	}

	/**
	 * Sanity check: everything's loaded!
	 *
	 * @covers My Ass
	 */
	public function is_everything_loaded() {
		$this->assertTrue( function_exists( 'EDD' ) );
		$this->assertTrue( function_exists( 'edd_recurring' ) );
		$this->assertTrue( class_exists( 'GFForms' ) );
		$this->assertTrue( class_exists( 'KWS_GF_EDD' ) );
	}

	public function _test_purchase() {

		$defined_form_json = '{"title":"Test Multiple Names","description":"","labelPlacement":"top_label","descriptionPlacement":"below","button":{"type":"text","text":"Submit","imageUrl":""},"fields":[{"type":"name","id":19,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"19.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"19.3","label":"First","name":""},{"id":"19.4","label":"Middle","name":"","isHidden":true},{"id":"19.6","label":"Last","name":""},{"id":"19.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","is_field_hidden":"","enablePrice":"","multipleFiles":""},{"type":"name","id":18,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"18.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"18.3","label":"First","name":""},{"id":"18.4","label":"Middle","name":"","isHidden":true},{"id":"18.6","label":"Last","name":""},{"id":"18.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"name","id":17,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"17.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"17.3","label":"First","name":""},{"id":"17.4","label":"Middle","name":"","isHidden":true},{"id":"17.6","label":"Last","name":""},{"id":"17.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"email","id":8,"label":"Email 1","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"visible","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","emailConfirmEnabled":"","pageNumber":1,"displayOnly":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"eddDownload":"","eddHasVariables":false,"failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":""},{"type":"email","id":16,"label":"Email","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","emailConfirmEnabled":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"address","id":11,"label":"Address 1","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","addressType":"international","inputs":[{"id":"11.1","label":"Street Address","name":""},{"id":"11.2","label":"Address Line 2","name":""},{"id":"11.3","label":"City","name":""},{"id":"11.4","label":"State \/ Province","name":""},{"id":"11.5","label":"ZIP \/ Postal Code","name":""},{"id":"11.6","label":"Country","name":""}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"visible","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","defaultCountry":"","defaultProvince":"","productField":"","defaultState":"","enableCopyValuesOption":"","copyValuesOptionDefault":"","copyValuesOptionLabel":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"eddDownload":"","eddHasVariables":false,"pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enablePrice":""},{"type":"address","id":15,"label":"Address","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","addressType":"international","inputs":[{"id":"15.1","label":"Street Address","name":""},{"id":"15.2","label":"Address Line 2","name":""},{"id":"15.3","label":"City","name":""},{"id":"15.4","label":"State \/ Province","name":""},{"id":"15.5","label":"ZIP \/ Postal Code","name":""},{"id":"15.6","label":"Country","name":""}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","defaultCountry":"","defaultProvince":"","productField":"","hideCountry":"","defaultState":"","hideState":"","hideAddress2":"","enableCopyValuesOption":"","copyValuesOptionDefault":"","copyValuesOptionLabel":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enablePrice":"","multipleFiles":""},{"type":"product","id":3,"label":"The Download Product","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":[{"id":"3.1","label":"Name","name":""},{"id":"3.2","label":"Price","name":""},{"id":"3.3","label":"Quantity","name":""}],"inputType":"singleproduct","enablePrice":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","basePrice":"$10.00","disableQuantity":false,"multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":"","eddDownload":"8","eddHasVariables":false,"visibility":"visible","failed_validation":false,"validation_message":"","enableCopyValuesOption":""},{"type":"name","id":13,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"13.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"13.3","label":"First","name":""},{"id":"13.4","label":"Middle","name":"","isHidden":true},{"id":"13.6","label":"Last","name":""},{"id":"13.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""}],"version":"2.2.1.1","id":1,"useCurrentUserAsAuthor":true,"postContentTemplateEnabled":false,"postTitleTemplateEnabled":false,"postTitleTemplate":"","postContentTemplate":"","lastPageButton":null,"pagination":null,"firstPageCssClass":null,"edd-fields":{"name":"17","email":"8","address":"11"},"edd-gf":{"name_field":"6","email_field":"5"},"notifications":{"581cb798ed079":{"id":"581cb798ed079","to":"{admin_email}","name":"Admin Notification","event":"form_submission","toType":"email","subject":"New submission from {form_title}","message":"{all_fields}"}},"confirmations":{"581cb798ed5ad":{"id":"581cb798ed5ad","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}},"is_active":"1","date_created":"2016-11-04 16:30:16","is_trash":"0","confirmation":{"id":"581cb798ed5ad","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}}';

		$defined_form = json_decode( $defined_form_json, true );

		$form = $this->factory->form->create_and_get( $defined_form );

		$this->assertNotEmpty( $form );
		$this->assertNotEmpty( $form['id'] );
		$this->assertEquals( 'Test Multiple Names', $form['title'] );

		$entry_json = '{"date_created":"2017-08-14 19:28:54","is_starred":0,"is_read":0,"ip":"172.17.0.1","source_url":"http:\/\/edd-gf.dev\/?gf_page=preview&id=1","post_id":null,"currency":"USD","payment_status":null,"payment_date":null,"transaction_id":null,"payment_amount":null,"payment_method":null,"is_fulfilled":null,"created_by":"1","transaction_type":null,"user_agent":"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit\/603.3.8 (KHTML, like Gecko) Version\/10.1.2 Safari\/603.3.8","status":"active","19.3":"Zeek 1","19.6":"Katz","18.3":"Zeek 2","18.6":"Katz","17.3":"Zeek 3","17.6":"Katz","8":"zeek+email1@katz.co","16":"zeek+email2@katz.co","11.1":"Address 1","11.3":"Denver","11.4":"co","11.5":"80205","11.6":"United States","15.1":"Address 2","15.3":"denver","15.4":"co","15.5":"80205","15.6":"United States","3.1":"The Download Product","3.2":"$10.00","3.3":"2","13.3":"Zack","13.6":"Katz","19.2":"","19.4":"","19.8":"","18.2":"","18.4":"","18.8":"","17.2":"","17.4":"","17.8":"","11.2":"","15.2":"","13.2":"","13.4":"","13.8":""}';

		$user = $this->wp_factory->user->create_and_get( array(
			'user_firstname' => 'Unit',
			'user_lastname' => 'Katz',
			'display_name' => 'Unit Test',
		));

		wp_set_current_user( $user->ID );

		$this->assertEquals( wp_get_current_user(), $user );

		$defined_entry = json_decode( $entry_json, true );
		$defined_entry['created_by'] = $user->ID;
		$defined_entry['form_id'] = $form['id'];


		$entry = $this->factory->entry->create_and_get( $defined_entry );

		$this->assertTrue( is_array( $entry ) );
		$this->assertNotEmpty( $entry['id'] );
		$this->assertEquals( 'Katz', $entry['13.6'] );

		$expected = '{"downloads":[{"id":"8","name":"The Download Product","quantity":"2","price":10.0,"product_field_id":3}],"user_info":{"id":1,"email":"zeek+email1@katz.co","first_name":"Zeek 3","last_name":"Katz","display_name":"Adminer Katzerooni","address":{"line1":"Address 1","line2":false,"city":"Denver","state":"co","zip":"80205","country":"US"}},"cart_details":[{"name":"Basic Download","id":8,"item_number":{"id":8,"quantity":1},"price":10.0,"_item_price":10.0,"tax":null,"quantity":1,"discount":0,"product_field_id":3},{"name":"Basic Download","id":8,"item_number":{"id":8,"quantity":1},"price":10.0,"_item_price":10.0,"tax":null,"quantity":1,"discount":0,"product_field_id":3}],"total":20.0,"gateway":false}';

		$expected_array = json_decode( $expected, true );
		$expected_array['user_info']['display_name'] = $user->display_name;
		$expected_array['user_info']['id'] = $user->ID;

		add_filter( 'edd_gf_use_details_from_logged_in_user', '__return_false' );
		$actual = $this->KWS_GF_EDD->get_edd_data_array_from_entry( $entry, $form );
		remove_filter( 'edd_gf_use_details_from_logged_in_user', '__return_false' );

		$this->assertEquals( $expected_array, $actual );

	}

	/**
	 * @covers KWS_GF_EDD::get_first_field_id_by_type
	 */
	public function test_get_first_field_id_by_type() {

		$form = $this->factory->form->create_and_get();

		$form['fields'] = array(
			new GF_Field_Text( array( 'id' => 1, 'label' => 'Textarea' )),
			new GF_Field_Name( array( 'id' => 2, 'label' => 'Name 1' )),
			new GF_Field_Name( array( 'id' => 3, 'label' => 'Name 2' )),
			new GF_Field_Name( array( 'id' => 4, 'label' => 'Name 3' )),
			new GF_Field_Address( array( 'id' => 5, 'label' => 'Address 1' )),
			new GF_Field_Address( array( 'id' => 6, 'label' => 'Address 2' )),
			new GF_Field_Address( array( 'id' => 7, 'label' => 'Address 3' )),
			new GF_Field_Textarea( array( 'id' => 8, 'label' => 'Textarea' )),
		);

		$this->factory->form->update_object( $form['id'], $form );

		$get_first_field_id_by_type = new ReflectionMethod( 'KWS_GF_EDD', 'get_first_field_id_by_type' );

		// It was private; let's make it public
		$get_first_field_id_by_type->setAccessible( true );

		// Required: $from, $to, $bcc, $replyTo, $subject, $message
		// Optional: $from_name, $message_format, $attachments, $lead, $notification
		$name_field_id = $get_first_field_id_by_type->invoke( $this->KWS_GF_EDD, $form, 'name' );

		$address_field_id = $get_first_field_id_by_type->invoke( $this->KWS_GF_EDD, $form, 'address' );

		$this->assertEquals( 2, $name_field_id );
		$this->assertEquals( 5, $address_field_id );
	}

	/**
	 * @covers KWS_GF_EDD::get_entry_discount()
	 */
	public function test_coupon_codes() {
		$defined_form_json = '{"title":"Test Multiple Names","description":"","labelPlacement":"top_label","descriptionPlacement":"below","button":{"type":"text","text":"Submit","imageUrl":""},"fields":[{"type":"name","id":19,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"19.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"19.3","label":"First","name":""},{"id":"19.4","label":"Middle","name":"","isHidden":true},{"id":"19.6","label":"Last","name":""},{"id":"19.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","is_field_hidden":"","enablePrice":"","multipleFiles":""},{"type":"name","id":18,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"18.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"18.3","label":"First","name":""},{"id":"18.4","label":"Middle","name":"","isHidden":true},{"id":"18.6","label":"Last","name":""},{"id":"18.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"name","id":17,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"17.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"17.3","label":"First","name":""},{"id":"17.4","label":"Middle","name":"","isHidden":true},{"id":"17.6","label":"Last","name":""},{"id":"17.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"email","id":8,"label":"Email 1","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"visible","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","emailConfirmEnabled":"","pageNumber":1,"displayOnly":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"eddDownload":"","eddHasVariables":false,"failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":""},{"type":"email","id":16,"label":"Email","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","emailConfirmEnabled":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""},{"type":"address","id":11,"label":"Address 1","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","addressType":"international","inputs":[{"id":"11.1","label":"Street Address","name":""},{"id":"11.2","label":"Address Line 2","name":""},{"id":"11.3","label":"City","name":""},{"id":"11.4","label":"State \/ Province","name":""},{"id":"11.5","label":"ZIP \/ Postal Code","name":""},{"id":"11.6","label":"Country","name":""}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"visible","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","defaultCountry":"","defaultProvince":"","productField":"","defaultState":"","enableCopyValuesOption":"","copyValuesOptionDefault":"","copyValuesOptionLabel":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"eddDownload":"","eddHasVariables":false,"pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enablePrice":""},{"type":"address","id":15,"label":"Address","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","addressType":"international","inputs":[{"id":"15.1","label":"Street Address","name":""},{"id":"15.2","label":"Address Line 2","name":""},{"id":"15.3","label":"City","name":""},{"id":"15.4","label":"State \/ Province","name":""},{"id":"15.5","label":"ZIP \/ Postal Code","name":""},{"id":"15.6","label":"Country","name":""}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","defaultCountry":"","defaultProvince":"","productField":"","hideCountry":"","defaultState":"","hideState":"","hideAddress2":"","enableCopyValuesOption":"","copyValuesOptionDefault":"","copyValuesOptionLabel":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enablePrice":"","multipleFiles":""},{"type":"product","id":3,"label":"The Download Product","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":[{"id":"3.1","label":"Name","name":""},{"id":"3.2","label":"Price","name":""},{"id":"3.3","label":"Quantity","name":""}],"inputType":"singleproduct","enablePrice":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","basePrice":"$10.00","disableQuantity":false,"multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":"","eddDownload":"8","eddHasVariables":false,"visibility":"visible","failed_validation":false,"validation_message":"","enableCopyValuesOption":""},{"type":"name","id":13,"label":"Name","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","nameFormat":"advanced","inputs":[{"id":"13.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"13.3","label":"First","name":""},{"id":"13.4","label":"Middle","name":"","isHidden":true},{"id":"13.6","label":"Last","name":""},{"id":"13.8","label":"Suffix","name":"","isHidden":true}],"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","visibility":"","noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","productField":"","pageNumber":1,"displayOnly":"","failed_validation":false,"validation_message":"","enableCopyValuesOption":"","enablePrice":"","multipleFiles":""}],"version":"2.2.1.1","id":1,"useCurrentUserAsAuthor":true,"postContentTemplateEnabled":false,"postTitleTemplateEnabled":false,"postTitleTemplate":"","postContentTemplate":"","lastPageButton":null,"pagination":null,"firstPageCssClass":null,"edd-fields":{"name":"17","email":"8","address":"11"},"edd-gf":{"name_field":"6","email_field":"5"},"notifications":{"581cb798ed079":{"id":"581cb798ed079","to":"{admin_email}","name":"Admin Notification","event":"form_submission","toType":"email","subject":"New submission from {form_title}","message":"{all_fields}"}},"confirmations":{"581cb798ed5ad":{"id":"581cb798ed5ad","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}},"is_active":"1","date_created":"2016-11-04 16:30:16","is_trash":"0","confirmation":{"id":"581cb798ed5ad","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}}';

		$defined_form = json_decode( $defined_form_json, true );

		$form = $this->factory->form->create_and_get( $defined_form );

		$form['fields'][] = new GF_Field_Coupon(array( 'id' => 111, 'label' => 'Coupon FIELD!'));

		$this->factory->form->update_object( $form['id'], $form );

		$entry = array(
			'111' => 'Coupon 1,  Coupon 2',
		);

		$submitted_coupons = gf_coupons()->get_submitted_coupon_codes( $form, $entry );

		$this->assertEquals( $submitted_coupons, array( 'Coupon 1', 'Coupon 2' ) );

		$entry_coupons = $this->KWS_GF_EDD->get_entry_coupons( $form, $entry );

		$this->assertEquals( $entry_coupons, array( 'Coupon 1', 'Coupon 2' ) );
	}


	/**
	 * @covers KWS_GF_EDD::get_payment_status_from_gf_status
	 */
	public function test_get_payment_status_from_gf_status() {

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

		foreach ( $gf_payment_statuses as $gf_status => $edd_status ) {
			$this->assertEquals( $edd_status, $this->KWS_GF_EDD->get_payment_status_from_gf_status( $gf_status ) );
		}

	// Test default status filter
		add_filter( 'edd_gf_default_status', function() {
			return 'WHERE IS IT?';
		});

		$this->assertEquals( 'WHERE IS IT?', $this->KWS_GF_EDD->get_payment_status_from_gf_status( "Not in the List" ) );

		remove_all_filters( 'edd_gf_default_status' );

	// Test the filter for found status
		add_filter( 'edd_gf_payment_status', function() {
			return 'asdasd';
		});

		$this->assertEquals( 'asdasd', $this->KWS_GF_EDD->get_payment_status_from_gf_status( "Processing" ) );

		remove_all_filters( 'edd_gf_payment_status' );
	}

	/**
	 * @covers KWS_GF_EDD::get_user_info_from_submission
	 */
	public function test_get_user_info_from_submission() {

		$user_info_from_submission = $this->KWS_GF_EDD->get_user_info_from_submission( $this->form, $this->entry );

		$expected = array(
			'first_name' => $this->entry['17.3'],
			'last_name' => $this->entry['17.6'],
			'email' => $this->entry['8'],
		    'address' => array(
			    'line1' => $this->entry['11.1'],
			    'line2' => rgar( $this->entry, '11.2', false ),
			    'city' => $this->entry['11.3'],
			    'state' => $this->entry['11.4'],
			    'zip' => $this->entry['11.5'],
			    'country' => GFCommon::get_country_code( $this->entry['11.6'] ),
		    ),
		);

		$this->assertEquals( $expected, $user_info_from_submission );

	}

	public function test_get_user_info() {

		// When logged-in and the user info from submission has no email

		wp_set_current_user( 1 );

		$user = wp_get_current_user();

		$expected = array(
			'id' => $user->ID,
			'display_name' => $user->display_name,
			'first_name' => $this->entry['17.3'],
			'last_name' => $this->entry['17.6'],
			'email' => $this->entry['8'],
			'address' => array(
				'line1' => $this->entry['11.1'],
				'line2' => rgar( $this->entry, '11.2', false ),
				'city' => $this->entry['11.3'],
				'state' => $this->entry['11.4'],
				'zip' => $this->entry['11.5'],
				'country' => GFCommon::get_country_code( $this->entry['11.6'] ),
			),
			'discount' => 0,
		);

		$entry_without_email = $this->entry;

		unset( $entry_without_email['8'] );

		$no_passed_email = $this->KWS_GF_EDD->get_user_info( $this->form, $entry_without_email );

		$expected['email'] = $user->user_email;

		$this->assertEquals( $expected, $no_passed_email );


		// Logged-in user, with "use logged-in user details" enabled
		remove_all_filters( 'edd_gf_use_details_from_logged_in_user' );
		$user_logged_in_email = $this->KWS_GF_EDD->get_user_info( $this->form, $this->entry );
		$expected['email'] = $this->entry['8'];
		$this->assertEquals( $expected, $user_logged_in_email );

		// Logged-in user, with "use logged-in user details" disabled
		add_filter( 'edd_gf_use_details_from_logged_in_user', '__return_false' );

		$user_logged_in_email = $this->KWS_GF_EDD->get_user_info( $this->form, $this->entry );
		$expected_no_logged_in = $expected;
		$expected_no_logged_in['email'] = $this->entry['8'];
		$expected_no_logged_in['id'] = -1;
		$expected_no_logged_in['display_name'] = null;
		$this->assertEquals( $expected_no_logged_in, $user_logged_in_email );

		add_filter( 'edd_gf_use_details_from_logged_in_user', '__return_false' );

		wp_set_current_user( - 1 );

		$user = wp_get_current_user();

		// Make sure we have logged out user
		$this->assertEmpty( $user->ID );

		// Create user with the entry's email address
		$user_with_entry_email = $this->wp_factory->user->create_and_get(array(
			'user_email' => $this->entry['8'],
			'display_name' => 'asdasdasdsd',
		));

		$user_logged_out_entry_email = $this->KWS_GF_EDD->get_user_info( $this->form, $this->entry );

		$expected_logged_out_entry_email = $expected;
		$expected_logged_out_entry_email['email'] = $user_with_entry_email->user_email;
		$expected_logged_out_entry_email['display_name'] = $user_with_entry_email->display_name;
		$expected_logged_out_entry_email['id'] = $user_with_entry_email->ID;

		$this->assertEquals( $expected_logged_out_entry_email, $user_logged_out_entry_email );

	}


}