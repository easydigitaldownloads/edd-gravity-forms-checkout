<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-05-01
 * Purpose: Test that a total field updates correctly when the quantity field tied to a product is shown/hidden
 */
 // @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a total field updates correctly when the quantity field tied to a product is shown/hidden' );

$I->amOnPage( '/CLFD010-Total-Impacted-by-Option-Field-Show-Hide/' );

$I->amGoingTo( 'Show the Qty field, fill it and see that total has increased.' );
$I->fillField( 'Untitled', 'showme' );
$I->waitForElementVisible( 'input[name="input_6"]', 1 );
$I->fillField( 'Quantity', '2' );
$I->fillField( 'Test Form', 'Test');
$I->seeInField( 'input[name="input_4"]', '20' );

$I->amGoingTo( "Hide the Qty field and see total decrease" );
$I->fillField( 'Untitled', '' );
$I->fillField( 'Test Form', 'Test');
$I->seeInField( 'input[name="input_4"]', '0' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD010 Total Impacted by Option Field Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );

// Edit entry
$I->click( 'Test' );

//quantity should default to 1 so $20.00 should not appear anywhere but $10.00 should
$I->amGoingTo( 'Make sure the quantity defaulted to 1 with the total being $10.00 ');

$I->dontSee( '$20.00' );

$I->waitForText( '$10.00', 1 );
