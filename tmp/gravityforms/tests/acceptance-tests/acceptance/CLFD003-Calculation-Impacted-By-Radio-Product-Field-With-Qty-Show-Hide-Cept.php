<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-04-17
 * Purpose: Test calculation using the product radio field with separate quantity field works correctly when product field or quantity is shown/hidden
 */

// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test calculation using the product radio field with separate quantity field works correctly when product field or quantity is shown/hidden' );

$I->amOnPage( '/CLFD003-Calculation-Impacted-By-Radio-Product-Field-With-Qty-Show-Hide/' );

$I->dontSee( 'Product Name' );

$I->amGoingTo( 'Fill out the text field so the product field displays' );
$I->fillField( 'Untitled', 'showme' );
$I->waitForText( 'Product Name', 2 );

$I->amGoingTo( 'Add a quantity and select the first Product to see the Number field display the value 4' );
$I->fillField( 'Quantity', '2' );
$I->selectOption( 'input[name="input_6"]' , 'First Choice' );
$I->seeInField( 'input[name="input_2"]', '4' );

$I->amGoingTo( 'Hide the product field again and make sure the Number field adjusts to zero and the Quantity field remains the same' );
$I->fillField( 'Untitled', '' );
$I->waitForElementNotVisible( '#input_6', 1 );
$I->seeInField( 'input[name="input_2"]', '0' );
$I->seeInField( 'Quantity', 2);

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD003 Calculation Impacted By Radio Product Field With Qty Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );

// Edit entry
$I->click( 'Test' );

// make sure product does not show
$I->dontSee( 'Unit Price' );
