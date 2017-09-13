<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-05-01
 * Purpose: Test that the option field works correctly when tied to a product and the product is shown/hidden
 */
 // @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that the option field works correctly when tied to a product and the product is shown/hidden' );

$I->amOnPage( '/CLFD011-Option-Field-Tied-to-Product-Show-Hide/' );

//select the second option in the drop down
$I->selectOption( 'Option', 'Twice');
$I->fillField( 'Untitled','hideme');
$I->waitForElementNotVisible( 'Product Name', 1);

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD011 Option Field Tied to Product Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );

$I->waitForText( 'Twice ($2.00) ', 1 );
