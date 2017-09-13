<?php
/*
Author: Dana Cobb
Date Created: 2017-06-27
Purpose: Test the calculation product type and the values substituted in the calculation
*/

// @group PRFD
// @group Pricing-Fields
// 		@group Product
// 		@group Quantity
// @group Calculation


$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test the calculation product type and make sure the calculation works correctly' );
$I->amOnPage( '0086-Calculation-Product-Type-With-Quantity-Field-Enabled' );
$I->amGoingTo( 'Enter a quantity' );
$I->expect( 'See the total update with the value from the calculation' );
$I->fillField( 'input[name="input_2.3"]', 2 );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'input[name="input_3"]', '21.2' );
