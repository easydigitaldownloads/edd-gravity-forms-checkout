<?php
/*
Author: Dana Cobb
Date Created: 2017-04-03
Purpose: Test that a calculation based on a single product field updates appropriately when the product field is shown/hidden
*/

// @group CLFD
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a calculation based on a single product field updates appropriately when the product field is shown/hidden' );

$I->amOnPage( '/CLFD001-Single-Product-Field-Show-Hide-Impacts-Calculation/' );

$I->amGoingTo( 'Make sure the single product field is hidden upon form load and the value 6 appears in the number field' );
$I->dontSee( 'Product Name' );
$I->seeInField( 'input[name="input_2"]', '6' );

$I->amGoingTo( 'Fill out the text field so the single product field displays' );
$I->fillField( 'Untitled', 'showme' );
$I->waitForText( 'Price', 2 );
$I->seeInField( 'input[name="input_2"]', '11' );

$I->amGoingTo( 'Hide the product field again' );
$I->fillField( 'Untitled', '' );
$I->waitForElementNotVisible( '#ginput_product_price_label', 1 );
$I->seeInField( 'input[name="input_2"]', '6' );




