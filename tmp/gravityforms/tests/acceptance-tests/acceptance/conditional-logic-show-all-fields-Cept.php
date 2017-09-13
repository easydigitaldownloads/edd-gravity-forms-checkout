<?php

// Passes in Chromedriver, fails in PhantomJS

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic show all fields by checkbox,radio button, select box, multiselect' );

$I->amOnPage( '/conditional-logic-show-all-fields-no-ajax/' );

$I->scrollTo( '.gform_title' );

$form_id = GFFormsModel::get_form_id( 'Conditional logic show all fields' );

function readElements( $formstatus, $form_id, $I ) {
	$missing = array( 8, 11, 20, 22, 30 );
	for ( $i = 1; $i <= 36; $i ++ ) {
		if ( in_array( $i, $missing ) ) {
			continue;
		}
		$I->$formstatus( '#field_' . $form_id . '_' . $i );
	}
}

$I->seeElement( '#field_' . $form_id . '_37' );
$I->seeElement( '#field_' . $form_id . '_40' );
$I->seeElement( '#field_' . $form_id . '_38' );
$I->seeElement( '#field_' . $form_id . '_39' );

readElements( 'dontSeeElement', $form_id, $I );

$I->checkOption( 'input[name="input_37.2"]' );

readElements( 'seeElement', $form_id, $I );

$I->uncheckOption( 'input[name="input_37.2"]' );
$I->checkOption( 'input[name="input_37.1"]' );

readElements( 'dontSeeElement', $form_id, $I );

$I->checkOption( '#choice_' . $form_id . '_40_1' );

readElements( 'seeElement', $form_id, $I );

$I->checkOption( '#choice_' . $form_id . '_40_0' );

readElements( 'dontSeeElement', $form_id, $I );

$I->selectOption( 'select[name=input_38]', 'Second Choice' );

readElements( 'seeElement', $form_id, $I );

$I->selectOption( 'select[name=input_38]', 'First Choice' );

readElements( 'dontSeeElement', $form_id, $I );

$I->selectOption( 'select#input_' . $form_id . '_39', 'Second Choice' );

readElements( 'seeElement', $form_id, $I );
