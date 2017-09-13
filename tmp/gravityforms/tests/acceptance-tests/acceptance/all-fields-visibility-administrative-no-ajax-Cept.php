<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test field administrative Visibility' );

$I->amOnPage( '/all-fields-visibility-administrative-no-ajax/' );

$form_id = GFFormsModel::get_form_id( 'All fields visibility administrative' );

$I->seeElement( 'input[name="input_1"]' );
$I->fillField( 'Single line visible', 'Testing' );

$I->dontSeeElement( 'input[name=input_2]' );
$I->dontSeeElementInDOM( 'input[name=input_2]' );
$I->dontSeeElement( 'textarea[name=input_3]' );
$I->dontSeeElementInDOM( 'textarea[name=input_3]' );
$I->dontSeeElement( 'select[name=input_4]' );
$I->dontSeeElementInDOM( 'select[name=input_4]' );
$I->dontSeeElement( 'select[id=input_' . $form_id . '_5]' );
$I->dontSeeElementInDOM( 'select[id=input_' . $form_id . '_5]' );
$I->dontSeeElement( 'input[name=input_6]' );
$I->dontSeeElementInDOM( 'input[name=input_6]' );

for ( $i = 1; $i <= 3; $i ++ ) {
	$I->dontSeeElement( 'input[id=choice_' . $form_id . '_7_' . $i . ']' );
	$I->dontSeeElementInDOM( 'input[id=choice_' . $form_id . '_7_' . $i . ']' );
}

for ( $i = 0; $i <= 2; $i ++ ) {
	$I->dontSeeElement( 'input[id=choice_' . $form_id . '_8_' . $i . ']' );
	$I->dontSeeElementInDOM( 'input[id=choice_' . $form_id . '_8_' . $i . ']' );
}

$I->dontSeeElement( 'input[name=input_9]' );
$I->dontSeeElementInDOM( 'input[name=input_9]' );

$I->dontSeeElement( 'select[id=input_' . $form_id . '_10_2]' );
$I->dontSeeElementInDOM( 'select[id=input_' . $form_id . '_10_2]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_3]' );
$I->dontSeeElementInDOM( 'input[id=input_' . $form_id . '_10_3]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_4]' );
$I->dontSeeElementInDOM( 'input[id=input_' . $form_id . '_10_4]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_6]' );
$I->dontSeeElementInDOM( 'input[id=input_' . $form_id . '_10_6]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_8]' );
$I->dontSeeElementInDOM( 'input[id=input_' . $form_id . '_10_8]' );

$I->dontSeeElement( 'input[name=input_11]' );
$I->dontSeeElementInDOM( 'input[name=input_11]' );

for ( $i = 1; $i <= 2; $i ++ ) {
	$I->dontSeeElement( 'input[id=input_' . $form_id . '_12_' . $i . ']' );
	$I->dontSeeElementInDOM( 'input[id=input_' . $form_id . '_12_' . $i . ']' );
}
$I->dontSeeElement( 'select[id=input_' . $form_id . '_12_3]' );
$I->dontSeeElementInDOM( 'select[id=input_' . $form_id . '_12_3]' );

$I->dontSeeElement( 'input[name=input_13]' );
$I->dontSeeElementInDOM( 'input[name=input_13]' );

for ( $i = 1; $i <= 5; $i ++ ) {
	$I->dontSeeElement( 'input[id=input_' . $form_id . '_14_' . $i . ']' );
	$I->dontSeeElementInDOM( 'input[id=input_' . $form_id . '_14_' . $i . ']' );
}

$I->dontSeeElement( 'input[name=input_15]' );
$I->dontSeeElementInDOM( 'input[name=input_15]' );
$I->dontSeeElement( 'input[name=input_16]' );
$I->dontSeeElementInDOM( 'input[name=input_16]' );
$I->dontSeeElement( 'input[name=input_17]' );
$I->dontSeeElementInDOM( 'input[name=input_17]' );

$I->dontSeeElement( 'li#field_' . $form_id . '_18' );
$I->dontSeeElementInDOM( 'li#field_' . $form_id . '_18' );

$I->dontSeeElement( 'input[name=input_19]' );
$I->dontSeeElementInDOM( 'input[name=input_19]' );
$I->dontSeeElement( 'textarea[name=input_20]' );
$I->dontSeeElementInDOM( 'textarea[name=input_20]' );
$I->dontSeeElement( 'textarea[name=input_21]' );
$I->dontSeeElementInDOM( 'textarea[name=input_21]' );
$I->dontSeeElement( 'input[name=input_22]' );
$I->dontSeeElementInDOM( 'input[name=input_22]' );
$I->dontSeeElement( 'select[name=input_23]' );
$I->dontSeeElementInDOM( 'select[name=input_23]' );
$I->dontSeeElement( 'input[name=input_25]' );
$I->dontSeeElementInDOM( 'input[name=input_25]' );
$I->dontSeeElement( 'li#field_' . $form_id . '_26' );
$I->dontSeeElementInDOM( 'li#field_' . $form_id . '_26' );

$I->scrollTo( [ 'css' => '.gform_footer' ], 20, 50 );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 5 );

$I->loginAsAdmin();

$I->amOnPage( '/wp-admin' );
$I->click( 'Forms' );
$I->amOnPage( '/wp-admin/admin.php?page=gf_edit_forms' );
$I->click( 'All fields visibility administrative' );
$I->see( 'Entries' );
$I->click( 'Entries', '#gf_form_toolbar_links' );
$I->see( 'Testing' );
$I->click( 'Testing' );

$I->checkOption( 'show empty fields' );

$I->see( 'Single line visible' );
$I->see( 'Single line administrative' );
$I->see( 'Textarea administrative' );
$I->see( 'Select administrative' );
$I->see( 'Multiselect administrative' );
$I->see( 'Number administrative' );
$I->see( 'Checkbox administrative' );
$I->see( 'Radio button administrative' );
$I->see( 'Name' );
$I->see( 'Date' );
$I->see( 'Time' );
$I->see( 'Phone' );
$I->see( 'Address' );
$I->see( 'Website' );
$I->see( 'Email' );
$I->see( 'File' );
$I->see( 'List' );
$I->see( 'Post Title' );
$I->see( 'Post Body' );
$I->see( 'Post Excerpt' );
$I->see( 'Post Tags' );
$I->see( 'Post Category' );
$I->see( 'Post Custom Field' );
$I->see( 'Section Break' );
