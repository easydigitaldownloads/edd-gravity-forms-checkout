<?php
/*
Author: Dana Cobb
Date Created: 2017-06-15
Purpose: Test options, confirmation page for product type hidden with Value merge tag on confirmation page
*/

// @group PRFD
// @group Pricing-Fields
// 		@group Product
// 		@group Quantity
// 		@group Option
// 		@group Shipping
// 		@group Total
// @group Notifications
// 		@group Merge-Tags
// @group Standard-Fields
// 		@group Drop-Down
// 		@group Checkboxes
// 		@group Radio-Buttons

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that the options and confirmation page work correctly for a hidden product type and the Value merge tag on confirmation page.' );

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

$I->amGoingTo( 'Select Pricing Fields Value from the drop down for confirmation page display' );
$I->selectOption( 'form select[name=input_5]', 'Pricing Fields Value' );

$I->amGoingTo( 'Submit the form and review the confirmation page.' );
$I->click( 'Submit' );

$I->amGoingTo( 'Review the data on the confirmation page.' );

$I->amGoingTo( 'Check the name of the product on the confirmation page.' );
$I->see( 'Product A - 2', ['xpath' => '//tbody/tr/td/strong'] );

$I->amGoingTo( 'Check the name of the option that was chosen on the confirmation page.' );
$I->see( 'Option A: A3', ['xpath'=>'//tbody/tr/td/ul/li'] );

$I->amGoingTo( 'Check the product quantity on the confirmation page - should default to 1.' );
$I->see( '1', ['xpath'=>'//tbody/tr[2]/td[2]/table/tbody/tr/td[2]'] );

$I->amGoingTo( 'Check the Unit Price on the confirmation page.' );
$I->see( '$3.97', ['xpath'=>'//tbody/tr[2]/td[2]/table/tbody/tr/td[3]'] );

$I->amGoingTo( 'Check the Price on the confirmation page.' );
$I->see( '$3.97', ['xpath'=>'//tbody/tr[2]/td[2]/table/tbody/tr/td[4]'] );

$I->amGoingTo( 'Check the Shipping Price on the confirmation page.' );
$I->see( '$5.00', ['xpath'=>'//tbody/tr[2]/td[2]/table/tfoot/tr/td[3]'] );

$I->amGoingTo( 'Check the Total on the confirmation page.' );
$I->see( '$8.97', ['xpath'=>'//tbody/tr[2]/td[2]/table/tfoot/tr[2]/td[2]'] );