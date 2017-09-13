<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test field Visibility hidden' );

$I->amOnPage( '/all-fields-visibility-hidden-no-ajax/' );

$form_id = GFFormsModel::get_form_id( 'All fields visibility hidden' );

$I->seeElement( 'input[name="input_1"]' );
$I->fillField( 'Single line visible', 'Testing' );

$I->dontSeeElement( 'input[name=input_2]' );
$I->seeElementInDOM( 'input[name=input_2]' );
$I->dontSeeElement( 'textarea[name=input_3]' );
$I->seeElementInDOM( 'textarea[name=input_3]' );
$I->dontSeeElement( 'select[name=input_4]' );
$I->seeElementInDOM( 'select[name=input_4]' );
$I->dontSeeElement( 'select[id=input_' . $form_id . '_5]' );
$I->seeElementInDOM( 'select[id=input_' . $form_id . '_5]' );
$I->dontSeeElement( 'input[name=input_6]' );
$I->seeElementInDOM( 'input[name=input_6]' );

for ( $i = 1; $i <= 3; $i ++ ) {
	$I->dontSeeElement( 'input[id=choice_' . $form_id . '_7_' . $i . ']' );
	$I->seeElementInDOM( 'input[id=choice_' . $form_id . '_7_' . $i . ']' );
}

for ( $i = 0; $i <= 2; $i ++ ) {
	$I->dontSeeElement( 'input[id=choice_' . $form_id . '_8_' . $i . ']' );
	$I->seeElementInDOM( 'input[id=choice_' . $form_id . '_8_' . $i . ']' );
}

$I->dontSeeElement( 'input[name=input_9]' );
$I->seeElementInDOM( 'input[name=input_9]' );

$I->dontSeeElement( 'select[id=input_' . $form_id . '_10_2]' );
$I->seeElementInDOM( 'select[id=input_' . $form_id . '_10_2]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_3]' );
$I->seeElementInDOM( 'input[id=input_' . $form_id . '_10_3]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_4]' );
$I->seeElementInDOM( 'input[id=input_' . $form_id . '_10_4]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_6]' );
$I->seeElementInDOM( 'input[id=input_' . $form_id . '_10_6]' );
$I->dontSeeElement( 'input[id=input_' . $form_id . '_10_8]' );
$I->seeElementInDOM( 'input[id=input_' . $form_id . '_10_8]' );

$I->dontSeeElement( 'input[name=input_11]' );
$I->seeElementInDOM( 'input[name=input_11]' );

for ( $i = 1; $i <= 2; $i ++ ) {
	$I->dontSeeElement( 'input[id=input_' . $form_id . '_12_' . $i . ']' );
	$I->seeElementInDOM( 'input[id=input_' . $form_id . '_12_' . $i . ']' );
}
$I->dontSeeElement( 'select[id=input_' . $form_id . '_12_3]' );
$I->seeElementInDOM( 'select[id=input_' . $form_id . '_12_3]' );

$I->dontSeeElement( 'input[name=input_13]' );
$I->seeElementInDOM( 'input[name=input_13]' );

for ( $i = 1; $i <= 5; $i ++ ) {
	$I->dontSeeElement( 'input[id=input_' . $form_id . '_14_' . $i . ']' );
	$I->seeElementInDOM( 'input[id=input_' . $form_id . '_14_' . $i . ']' );
}

$I->dontSeeElement( 'input[name=input_15]' );
$I->seeElementInDOM( 'input[name=input_15]' );
$I->dontSeeElement( 'input[name=input_16]' );
$I->seeElementInDOM( 'input[name=input_16]' );
$I->dontSeeElement( 'input[name=input_17]' );
$I->seeElementInDOM( 'input[name=input_17]' );

$I->dontSeeElement( 'li#field_' . $form_id . '_18' );
$I->seeElementInDOM( 'li#field_' . $form_id . '_18' );

$I->dontSeeElement( 'input[name=input_19]' );
$I->seeElementInDOM( 'input[name=input_19]' );
$I->dontSeeElement( 'textarea[name=input_20]' );
$I->seeElementInDOM( 'textarea[name=input_20]' );
$I->dontSeeElement( 'textarea[name=input_21]' );
$I->seeElementInDOM( 'textarea[name=input_21]' );
$I->dontSeeElement( 'input[name=input_22]' );
$I->seeElementInDOM( 'input[name=input_22]' );
$I->dontSeeElement( 'select[name=input_23]' );
$I->seeElementInDOM( 'select[name=input_23]' );
$I->dontSeeElement( 'input[name=input_25]' );
$I->seeElementInDOM( 'input[name=input_25]' );
$I->dontSeeElement( 'li#field_' . $form_id . '_26' );
$I->seeElementInDOM( 'li#field_' . $form_id . '_26' );