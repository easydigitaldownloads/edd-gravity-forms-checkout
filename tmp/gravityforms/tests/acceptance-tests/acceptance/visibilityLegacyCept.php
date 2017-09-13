<?php
$I = new AcceptanceTester( $scenario );

$I->amOnPage( '/visibility-legacy-no-ajax' );

// Make sure we can see the visible field
$I->see( 'Single line - visible' );

// Make sure the admin-only Paragraph field is not visible on the page and the input is not present.
$I->dontSee( 'Paragraph' );
$I->dontSeeElement( 'textarea[name=input_2]' );
$I->dontSeeElementInDOM( 'textarea[name=input_2]' );

// Make sure the admin-only dropdown field is not visible and not hidden
$I->dontSee( 'Dropdown' );
$I->dontSeeElement( 'select[name=input_3]' );
$I->dontSeeElementInDOM( 'select[name=input_2]' );
$I->fillField( 'Single line - visible', 'Testing123' );
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us!' );

// Login to wp-admin
$I->wait( 2 );
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'Visibility-Legacy' );

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
