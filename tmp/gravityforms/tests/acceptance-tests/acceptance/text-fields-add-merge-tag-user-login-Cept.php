<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test fill text fields with username by merge tag' );

$form_id = GFFormsModel::get_form_id( 'Text fields add Merge tag user login' );

$I->loginAs( 'admin2', 'admin2' );
$I->amOnPage( '/text-fields-add-merge-tag-user-login-no-ajax/' );

$I->seeInField( 'Single line text', 'admin2' );
$I->seeInField( 'Text', 'admin2' );
$I->seeOptionIsSelected( 'select[name=input_3]', 'admin2' );
$I->seeInField( 'input[type="hidden"]', 'admin2' );
$I->seeElement( '//*[@id="field_' . $form_id . '_5"][text() = \'admin2\']' );