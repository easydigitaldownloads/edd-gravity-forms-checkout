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
$I->wantTo( 'Test the calculation product type and make sure the calculation works correctly with the quantity field disabled' );
$I->amOnPage( '0087-Calculation-Product-Type-With-Quantity-Field-Disabled' );
$I->expectTo( 'See 10.60 as the total, meaning a quantity of 1 was used.' );
$I->seeInField( 'input[name="input_3"]', '10.6' );
