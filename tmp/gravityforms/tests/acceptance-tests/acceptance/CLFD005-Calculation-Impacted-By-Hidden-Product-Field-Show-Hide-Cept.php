<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-04-24
 * Purpose: Test calculation using the hidden product field with separate quantity field works correctly when product field or quantity is shown/hidden
 */
// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test calculation using the hidden product field with separate quantity field works correctly when product field or quantity is shown/hidden' );

$I->amOnPage( '/CLFD005-Calculation-Impacted-By-Hidden-Product-Field-Show-Hide/' );

$I->amGoingTo( 'Fill out the text field so the Quantity field displays' );
$I->fillField( 'Untitled', 'showme' );
$I->waitForText( 'Quantity', 1 );

$I->amGoingTo( 'Add a quantity to see the Number field display the value 50' );
$I->fillField( 'Quantity', '5' );
$I->wait( 1 );
$I->seeInField( 'input[name="input_4"]', '50' );

$I->amGoingTo( 'Hide the quantity field again and make sure the Number field adjusts to zero' );
$I->fillField( 'Untitled', '' );
$I->waitForElementNotVisible( '#input_5', 1 );
$I->seeInField( 'input[name="input_4"]', '0' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD005 Calculation Impacted By Hidden Product Field Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );

// Edit entry
$I->click( 'Test' );

//since Qty was hidden, the default Qty of 1 is used and the price should be $10
$I->see( '$10.00' );
