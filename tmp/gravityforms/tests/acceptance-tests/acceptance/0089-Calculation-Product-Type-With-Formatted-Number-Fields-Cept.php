<?php
/*
Author: Dana Cobb
Date Created: 2017-06-27
Purpose: Test the calculation product type with numbers in different formats
*/

// @group PRFD
// @group Pricing-Fields
// 		@group Product
// 		@group Quantity
// @group Calculation
// @group Standard-Fields
// 		@group Number

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test the calculation product type with numbers in different formats to make sure the calculation is correct' );

$I->amOnPage( '0089-Calculation-Product-Type-With-Formatted-Number-Fields' );
$I->amGoingTo( 'Fill out the quantity so the total increases as numbers are filled' );
$I->fillField( 'input[name="input_3.3"]', 2 );

$I->amGoingTo( 'Enter a US currency formatted number' );
$I->expectTo( 'See the total increase to 10.10 after entering a US currency formatted number.');
$I->fillField( 'input[name="input_1"]', '5.05' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'input[name="input_5"]', '10.1' );

$I->amGoingTo( 'Enter a non-US currency formatted number' );
$I->fillField( 'input[name="input_4"]', '4,95' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'input[name="input_5"]', '20' );

$I->amGoingTo( 'Enter a US currency in the currency format field' );
$I->fillField( 'input[name="input_2"]', '4.99' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'input[name="input_5"]', '29.98' );





