<?php
/*
Author: Dana Cobb
Date Created: 2017-06-19
Purpose: Test options, confirmation page for product type hidden with various merge tags on confirmation page
*/

// @group PRFD
// @group Pricing-Fields
// 		@group Product
// 		@group Quantity
// 		@group Option
// 		@group Shipping
// 		@group Total
// @group Merge-Tags
// @group Standard-Fields
// 		@group Drop-Down
// 		@group Checkboxes
// 		@group Radio-Buttons

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that the options and confirmation page work correctly for a hidden product type using various merge tags on confirmation page.' );

$I->amOnPage( '/0080-Pricing-Calculations-Hidden-Product-Type/' );

$I->amGoingTo( 'See that the product options in the drop down are what is expected.' );
$I->seeInField( 'select[name="input_2"]', 'Option A-1-1.75' );
$I->seeInSource( '<option value="A2|1.86" price=" +$0.11">Option A-2-1.86  +$0.11</option>' );
$I->seeInSource( '<option value="A3|1.97" price=" +$0.22">Option A-3-1.97  +$0.22</option>' );

$I->amGoingTo( 'Select the second option in the drop down and verify that the other values adjust as expected.' );
$I->selectOption( 'form select[name=input_2]', 'Option A-2-1.86  +$0.11' );
$I->seeInSource( '<option value="A1|1.75" price=" -$0.11">Option A-1-1.75   -$0.11</option>' );
$I->seeInSource( '<option value="A3|1.97" price=" +$0.11">Option A-3-1.97   +$0.11</option>' );

$I->amGoingTo( 'Select the third option in the drop down and verify that the other values adjust as expected.' );
$I->selectOption( 'form select[name=input_2]', 'Option A-3-1.97   +$0.11' );
$I->seeInSource( '<option value="A1|1.75" price=" -$0.22">Option A-1-1.75    -$0.22</option>' );
$I->seeInSource( '<option value="A2|1.86" price=" -$0.11">Option A-2-1.86    -$0.11</option>' );

$I->amGoingTo( 'Verify that the product total is 8.97' );
$I->seeInField( 'input[name="input_3"]', '8.97' );

$I->amGoingTo( 'Select Individual Fields Value from the drop down for confirmation page display' );
$I->selectOption( 'form select[name=input_5]', 'Individual Fields' );

$I->amGoingTo( 'Submit the form and review the confirmation page.' );
$I->click( 'Submit' );

$I->amGoingTo( 'Review the data on the confirmation page.' );

$I->amGoingTo( 'Check the name of the option that was chosen on the confirmation page.' );
$I->See( 'Option: Option A-3-1.97', ['xpath'=>'//table/tbody/tr/td'] );

$I->amGoingTo( 'Check the VALUE merge tag for the option on the confirmation page.' );
$I->see( 'Option Value: A3', ['xpath'=>'//table/tbody/tr[2]/td'] );

$I->amGoingTo( 'Check the PRICE merge tag for the option on the confirmation page' );
$I->see( 'Option Price: 1.97', ['xpath'=>'//table/tbody/tr[3]/td'] );

$I->amGoingTo( 'Check the CURRENCY merge tag for the option on the confirmation page' );
$I->see( 'Option Currency: $1.97', ['xpath'=>'//table/tbody/tr[4]/td'] );

$I->amGoingTo( 'Check the Total on the confirmation page.' );
$I->see( 'Total: $8.97', ['xpath'=>'//table/tbody/tr[6]/td'] );

$I->amGoingTo( 'Check the Total PRICE merge tag on the confirmation page.' );
$I->see( 'Total Price: 8.97', ['xpath'=>'//table/tbody/tr[7]/td'] );

$I->amGoingTo( 'Check the Total CURRENCY merge tag on the confirmation page.' );
$I->see( 'Total Currency: $8.97', ['xpath'=>'//table/tbody/tr[8]/td'] );

