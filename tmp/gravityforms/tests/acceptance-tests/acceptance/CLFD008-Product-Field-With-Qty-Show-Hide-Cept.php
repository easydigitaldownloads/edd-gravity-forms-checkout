<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-04-25
 * Purpose: Test that a product field with a separate Quantity field works correctly when the quantity is shown/hidden
 */
 // @group CLFD
 

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a product field with a separate Quantity field works correctly when the quantity is shown/hidden' );

$I->amOnPage( '/CLFD008-Product-Field-With-Qty-Show-Hide/' );

//Show the Quantity field and fill it out
$I->fillField( 'Untitled', 'showme' );
$I->waitForText( 'Quantity', 1 );
$I->fillField( 'input[name="input_2"]', '2' );

//fill a text field so the focus moves off of the quantity field and the total updates
$I->fillField ( 'Test', 'test' );
$I->seeInField( 'input[name="input_4"]', '30' );

//re-hide quantity so make sure the value doesn't carry through on the product
$I->fillField( 'Untitled', '' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD008 Product Field With Qty Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Product Name', 1 );

// Edit entry
$I->click( 'Product Name' );

//since Quantity was hidden, a default of 1 should have been used and $15 is the total, should not see price for a qty of 2
$I->dontSee( '$30.00' );
