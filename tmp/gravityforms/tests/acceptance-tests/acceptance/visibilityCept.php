<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test fields visibility' );

$I->amOnPage( '/visibility-no-ajax' );

// Make sure we can see the visible field
$I->see( 'Single line - visible' );

// Make sure the hidden Paragraph is not visible on the page but the input exists.
$I->dontSee( 'Paragraph' );
$I->dontSeeElement( 'form textarea' );
$I->seeElementInDOM( 'form textarea' );

// Make sure the Administrative field is not visible and not hidden
$I->dontSee( 'Dropdown' );
$I->dontSeeElement( 'select[name=input_3]' );
$I->dontSeeElementInDOM( 'select[name=input_2]' );
$I->fillField( 'Single line - visible', 'Testing123' );
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'Visibility' );

// Got to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->see( 'Testing123' );
$I->click( 'Testing123' );

// edit entry
$I->click( 'Edit' );

// Make sure we can see all the fields
$I->seeElement( 'input[name=input_1]' );
$I->seeElement( 'textarea[name=input_2]' );
$I->seeElement( 'select[name=input_3]' );
