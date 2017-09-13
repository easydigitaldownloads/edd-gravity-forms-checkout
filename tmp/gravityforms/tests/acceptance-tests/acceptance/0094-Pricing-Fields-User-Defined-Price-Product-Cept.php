<?php
/*
Author: Raquel Kohler
Date Created: 2017-07-14
Purpose: Test that the form total of a User Defined Price Product field along with associated quantity fields and option fields updates appropriately and that the confirmation page displays results correctly.
*/

// @group PRFD
// @group Pricing-Fields
// 		@group Product
// 		@group Quantity
// 		@group Option
// 		@group Shipping
// 		@group Total
// @group Standard-Fields
// 		@group Drop-Down
// 		@group Checkboxes
// 		@group Radio-Buttons

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that the form total of a User Defined Price Product field along with associated quantity fields and option fields updates appropriately and that the confirmation page displays results correctly.' );

$I->amOnPage( '/0094-Pricing-Fields-User-Defined-Price-Product/' );


//User Defined product with non-numeric values
$I->amGoingTo( 'Test that setting user defined product with non-numeric characters will not cause an issue; all non-numeric values are stripped out.');
$I->fillField('input[name="input_2"]', '@$!$1 ,');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the user defined product A is set has a value of 1.00' );
$I->seeInField( 'input[name="input_2"]', '$1.00' );


//User Defined price product with user defined quantity (fraction)
$I->amGoingTo( 'Test that setting the quantity of a user defined price product with user defined quantity to a fraction number and submitting the form will warn the user the input is invalid.');
$I->fillField('input[name="input_4"]', '1/2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Submit the form and wait for the invalid submission message.' );
$I->click( 'Submit' );
$I->waitForText('There was a problem with your submission. Errors have been highlighted below.',1);
$I->see('Please enter a valid number' );


//User Defined price product with default quantity set to 1
//User Defined price product with shipping field
$I->amGoingTo( 'Test that user defined price product with default quantity set to 1 and shipping price field adjust the form total as expected.');
$I->fillField('input[name="input_2"]', '2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 12.00' );
$I->seeInField( 'input[name="input_13"]', '12' );


//User Defined price product with quantity field linked to it
$I->amGoingTo( 'Test that user defined price product with quantity field linked to it adjusts the form total as expected.');
$I->fillField('input[name="input_3"]', '5');
$I->fillField('input[name="input_4"]', '2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 22.00' );
$I->seeInField( 'input[name="input_13"]', '22' );


//User Defined price product with quantity field linked to it and drop down option
$I->amGoingTo( 'Test that user defined price product with quantity field linked to it and dropdown option adjusts the form total as expected.');
$I->fillField('input[name="input_6"]', '3');
$I->fillField('input[name="input_7"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 26.00' );
$I->seeInField( 'input[name="input_13"]', '26' );
$I->amGoingTo( 'Select the third option in the Option C-1 drop down and verify that the form total adjusts as expected.' );
$I->selectOption( 'form select[name=input_8]', 'Option C-1-2|2' );
$I->amGoingTo( 'Verify that the total is 27.00' );
$I->seeInField( 'input[name="input_13"]', '27' );


//User Defined price product with quantity field linked to it and checkboxes option
$I->amGoingTo( 'Test that user defined price product with quantity field linked to it and checkboxes option adjusts the form total as expected.');
$I->fillField('input[name="input_9"]', '2');
$I->fillField('input[name="input_15"]', '2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 31.00' );
$I->seeInField( 'input[name="input_13"]', '31' );
$I->amGoingTo( 'Select the second option in the Option D-1 check box and verify that the form total adjusts as expected.' );
$I->checkOption('Option D-1-1');
$I->amGoingTo( 'Verify that the total is 41.00' );
$I->seeInField( 'input[name="input_13"]', '41' );


//User Defined price product with quantity field linked to it and radio buttons option
$I->amGoingTo( 'Test that user defined price product with quantity field linked to it and radio buttons option adjusts the form total as expected.');
$I->fillField('input[name="input_11"]', '8');
$I->fillField('input[name="input_14"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 49.00' );
$I->seeInField( 'input[name="input_13"]', '49' );
$I->amGoingTo( 'Select the first option in the Option E-1 radio button and verify that the form total adjusts as expected.' );
$I->checkOption('Option E-1-3');
$I->amGoingTo( 'Verify that the total is 55.00' );
$I->seeInField( 'input[name="input_13"]', '55' );

//Submit form and check confirmation page
$I->amGoingTo( 'Submit the form and review the confirmation page.' );
$I->click( 'Submit' );

$I->amGoingTo( 'Check that the name, quantity, unit price, and price for Product A are displayed correctly in the confirmation page.' );
$I->see( 'Product A', ['xpath' => '//tbody/tr/td/strong'] );
$I->see( '1', ['xpath' => '//tbody/tr/td[2]'] );
$I->see( '$2.00', ['xpath' => '//tbody/tr/td[3]'] );
$I->see( '$2.00', ['xpath' => '//tbody/tr/td[4]'] );

$I->amGoingTo( 'Check that the name, quantity, unit price, and price for Product B are displayed correctly in the confirmation page.' );
$I->see( 'Product B', ['xpath' => '//tbody/tr[2]/td/strong'] );
$I->see( '2', ['xpath' => '//tbody/tr[2]/td[2]'] );
$I->see( '$5.00', ['xpath' => '//tbody/tr[2]/td[3]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[2]/td[4]'] );

$I->amGoingTo( 'Check that the name, quantity, unit price, and price for Product C are displayed correctly in the confirmation page.' );
$I->see( 'Product C', ['xpath' => '//tbody/tr[3]/td/strong'] );
$I->see( 'Option C-1: Option C-1-2', ['xpath'=>'//tbody/tr[3]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[3]/td[2]'] );
$I->see( '$5.00', ['xpath' => '//tbody/tr[3]/td[3]'] );
$I->see( '$5.00', ['xpath' => '//tbody/tr[3]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product D are displayed correctly in the confirmation page.' );
$I->see( 'Product D', ['xpath' => '//tbody/tr[4]/td/strong'] );
$I->see( 'Option D-1: Option D-1-1', ['xpath'=>'//tbody/tr[4]/td/ul/li'] );
$I->see( '2', ['xpath' => '//tbody/tr[4]/td[2]'] );
$I->see( '$7.00', ['xpath' => '//tbody/tr[4]/td[3]'] );
$I->see( '$14.00', ['xpath' => '//tbody/tr[4]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product E are displayed correctly in the confirmation page.' );
$I->see( 'Product E', ['xpath' => '//tbody/tr[5]/td/strong'] );
$I->see( 'Option E-1: Option E-1-3', ['xpath'=>'//tbody/tr[5]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[5]/td[2]'] );
$I->see( '$14.00', ['xpath' => '//tbody/tr[5]/td[3]'] );
$I->see( '$14.00', ['xpath' => '//tbody/tr[5]/td[4]'] );

$I->amGoingTo( 'Check the Shipping Price' );
$I->see( '$10.00', ['xpath'=>'//tfoot/tr/td[3]'] );

$I->amGoingTo( 'Check the Total' );
$I->see( '$55.00', ['xpath'=>'//tfoot/tr[2]/td[2]'] );






