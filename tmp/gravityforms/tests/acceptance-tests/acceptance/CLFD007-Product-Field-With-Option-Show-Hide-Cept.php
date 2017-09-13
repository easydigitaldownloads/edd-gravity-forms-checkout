<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-04-24
 * Purpose: Test that a product field with an Option works correctly when the option is shown/hidden
 */
// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a product field with an Option works correctly when the option is shown/hidden' );

$I->amOnPage( '/CLFD007-Product-Field-With-Option-Show-Hide/' );

$I->fillField( 'Untitled', 'showme' );
$I->waitForText( 'Option', 1 );
$I->selectOption( 'select[name="input_2"]', '3XL' );
$I->seeInField( 'input[name="input_3"]', '30' );

$I->fillField( 'Untitled', '' );
$I->waitForElementNotVisible( '#input_2', 1 );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD007 Product Field With Option Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Product Name', 1 );

// Edit entry
$I->click( 'Product Name' );

//since Shipping was hidden and part of the Product Price's calculation, the price should be $0
$I->waitForText( '$20.00', 1 );
