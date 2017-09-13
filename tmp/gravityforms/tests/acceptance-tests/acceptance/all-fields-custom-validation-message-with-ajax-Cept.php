<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test custom validation message added to all fields' );
$form_id = GFFormsModel::get_form_id( 'All fields Custom Validation Message' );

$I->amOnPage( '/all-fields-custom-validation-message-with-ajax/' );
$I->click( 'Submit' );

for ( $i = 1; $i <= 18; $i ++ ) {
	if ( $i == 17 ) {
		continue;
	}
	$I->waitForText( 'Please fill ' . $i, 3 );
}

$I->amOnPage( '/all-fields-custom-validation-message-with-ajax/' );

$I->executeJS( "jQuery('input[type=text]').each(function(){
	jQuery(this).val('Test')});
" );

$I->fillField( 'textarea[name="input_2"]', 'Test' );
$I->selectOption( 'select[name=input_3]', 'First Choice' );
$I->selectOption( 'select[id=input_' . $form_id . '_4]', 'First Choice' );
$I->fillField( 'input[name="input_5"]', '5' );
$I->checkOption( '#choice_' . $form_id . '_6_1' );
$I->selectOption( 'input[name=input_7]', 'First Choice' );
$I->fillField( 'input[name="input_9"]', '02/24/2017' );

// Make sure the date picker has closed
$I->click( 'body' );

$I->fillField( 'input[id="input_' . $form_id . '_10_1"]', '10' );
$I->fillField( 'input[id="input_' . $form_id . '_10_2"]', '20' );
$I->fillField( 'input[name="input_11"]', '0000000000' );
$I->selectOption( 'select[id=input_' . $form_id . '_12_6]', 'Canada' );
$I->fillField( 'input[name="input_13"]', 'http://www.google.com' );
$I->fillField( 'input[name="input_14"]', 'test@test.com' );
$I->attachFile( '.ginput_container_fileupload input[type="file"]', 'gravityforms.png' );
$I->fillField( 'input[name="input_18"]', '5' );

$I->scrollTo( [ 'css' => '.gform_footer' ], 20, 50 );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );
