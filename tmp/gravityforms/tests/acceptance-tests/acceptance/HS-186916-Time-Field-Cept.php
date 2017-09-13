<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test time field with zeros' );

$I->amOnPage( '/hs-186916-time-field/' );

$I->fillField( '2Z-HH', '00' );
$I->fillField( '2Z-MM', '00' );
$I->fillField( '1Z-HH', '0' );
$I->fillField( '1Z-MM', '0' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get form ID
$form_id = GFFormsModel::get_form_id( 'HS-186916 - Time Field' );

// Go to entry list page
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );
$I->waitForText( '00:00' );

// Go to entry detail page
$I->click( '00:00' );
$I->waitForElement( '.entry-detail-view' );
$I->see( 'Two Zeros', '.entry-view-field-name' );
$I->see( 'One Zero', '.entry-view-field-name' );
$I->dontSee( 'Empty', '.entry-view-field-name' );