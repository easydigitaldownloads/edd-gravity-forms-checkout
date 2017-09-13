<?php
/*
Author: Raquel Kohler
Date Created: 2017-07-14
Purpose: Test that the form total of a Single Product field along with associated quantity fields and option fields updates appropriately and that the confirmation page displays results correctly.
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
$I->wantTo( 'Test that the form total of a Single Product field along with associated quantity fields and option fields updates appropriately and that the confirmation page displays results correctly.' );

$I->amOnPage( '/0091-Pricing-Fields-Single-Product/' );


//Single product with default quantity set to 1
//Single product with shipping field
$I->amGoingTo( 'Test that single product with default quantity set to 1 and that the shipping field adjust the form total as expected.');
$I->amGoingTo( 'Verify that the total is 12.00' );
$I->seeInField( 'input[name="input_11"]', '12' );

//Single product with user defined quantity (fraction)
$I->amGoingTo( 'Test that setting the quantity of a single product with user defined quantity to a fraction number and submitting the form will warn the user the input is invalid.');
$I->fillField('input[name="input_22.3"]', '1/2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Submit the form and wait for the invalid submission message.' );
$I->click( 'Submit' );
$I->waitForText('There was a problem with your submission. Errors have been highlighted below.',1);
$I->see('Please enter a valid quantity' );

// Single product with user defined quantity
$I->amGoingTo( 'Test that single product with user defined quantity adjusts the form total as expected.');
$I->fillField('input[name="input_22.3"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 14.00' );
$I->seeInField( 'input[name="input_11"]', '14' );


//Single product with quantity field linked to it
$I->amGoingTo( 'Test that single product with quantity field linked to it adjusts the form total as expected.');
$I->fillField('input[name="input_4"]', '2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 20.00' );
$I->seeInField( 'input[name="input_11"]', '20' );


//Single product with user defined quantity and drop down option
$I->amGoingTo( 'Test that single product with quantity field linked to it and dropdown option adjusts the form total as expected.');
$I->fillField('input[name="input_6.3"]', '2');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 30.00' );
$I->seeInField( 'input[name="input_11"]', '30' );
$I->amGoingTo( 'Select the second option in the Option D-1 drop down adjusts the form total as expected.' );
$I->selectOption( 'form select[name=input_7]', 'Choice D-1-2|2' );
$I->amGoingTo( 'Verify that the total is 32.00' );
$I->seeInField( 'input[name="input_11"]', '32' );


//Single product with default quantity set to 1 and drop down option
$I->amGoingTo( 'Test that single product with quantity field linked to it and dropdown option adjusts the form total as expected.');
$I->fillField('input[name="input_9"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 38.00' );
$I->seeInField( 'input[name="input_11"]', '38' );
$I->amGoingTo( 'Select the third option in the Option E-1 drop down and verify that the form total adjusts as expected.' );
$I->selectOption( 'form select[name=input_10]', 'Choice E-1-3|3' );
$I->amGoingTo( 'Verify that the total is 40.00' );
$I->seeInField( 'input[name="input_11"]', '40' );


//Single product with user defined quantity and checkboxes option
$I->amGoingTo( 'Test that single product with user defined quantity and checkboxes option adjusts the form total as expected.');
$I->fillField('input[name="input_12.3"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 46.00' );
$I->seeInField( 'input[name="input_11"]', '46' );
$I->amGoingTo( 'Select the first option in the Option F-1 check box and verify that the form total adjusts as expected.' );
$I->checkOption('Choice F-1-1');
$I->amGoingTo( 'Verify that the total is 47.00' );
$I->seeInField( 'input[name="input_11"]', '47' );


//Single product with quantity field linked to it and checkboxes option
$I->amGoingTo( 'Test that single product with quantity field linked to it and checkboxes option adjusts the form total as expected.');
$I->fillField('input[name="input_14"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 54.00' );
$I->seeInField( 'input[name="input_11"]', '54' );
$I->amGoingTo( 'Select the third option in the Option G-1 check box and verify that the form total adjusts as expected.' );
$I->checkOption('Choice G-1-3');
$I->amGoingTo( 'Verify that the total is 57.00' );
$I->seeInField( 'input[name="input_11"]', '57' );


//Single product with user defined quantity and radio buttons option
$I->amGoingTo( 'Test that single product with user defined quantity and radio buttons option adjusts the form total as expected.');
$I->fillField('input[name="input_17.3"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 65.00' );
$I->seeInField( 'input[name="input_11"]', '65' );
$I->amGoingTo( 'Select the first option in the Option H-1 radio button and verify that the form total adjusts as expected.' );
$I->checkOption('Choice H-1-2');
$I->amGoingTo( 'Verify that the total is 67.00' );
$I->seeInField( 'input[name="input_11"]', '67' );


//Single product with quantity field linked to it and radio buttons option
$I->amGoingTo( 'Test that single product with quantity field linked to it and radio buttons option adjusts the form total as expected.');
$I->fillField('input[name="input_20"]', '1');
$I->clickWithLeftButton(['css' => '.gform_title']);
$I->amGoingTo( 'Verify that the total is 76.00' );
$I->seeInField( 'input[name="input_11"]', '76' );
$I->amGoingTo( 'Select the first option in the Option I-1 radio button and verify that the form total adjusts as expected.' );
$I->checkOption('Choice I-1-1');
$I->amGoingTo( 'Verify that the total is 77.00' );
$I->seeInField( 'input[name="input_11"]', '77' );



$I->amGoingTo( 'Submit the form and review the confirmation page.' );
$I->click( 'Submit' );

$I->amGoingTo( 'Check that the name, quantity, unit price, and price for Product A are displayed correctly in the confirmation page.' );
$I->see( 'Product A', ['xpath' => '//tbody/tr/td/strong'] );
$I->see( '1', ['xpath' => '//tbody/tr/td[2]'] );
$I->see( '$2.00', ['xpath' => '//tbody/tr/td[3]'] );
$I->see( '$2.00', ['xpath' => '//tbody/tr/td[4]'] );

$I->amGoingTo( 'Check that the name, quantity, unit price, and price for Product B are displayed correctly in the confirmation page.' );
$I->see( 'Product B', ['xpath' => '//tbody/tr[2]/td/strong'] );
$I->see( '1', ['xpath' => '//tbody/tr[2]/td[2]'] );
$I->see( '$2.00', ['xpath' => '//tbody/tr[2]/td[3]'] );
$I->see( '$2.00', ['xpath' => '//tbody/tr[2]/td[4]'] );

$I->amGoingTo( 'Check that the name, quantity, unit price, and price for Product C are displayed correctly in the confirmation page.' );
$I->see( 'Product C', ['xpath' => '//tbody/tr[3]/td/strong'] );
$I->see( '2', ['xpath' => '//tbody/tr[3]/td[2]'] );
$I->see( '$3.00', ['xpath' => '//tbody/tr[3]/td[3]'] );
$I->see( '$6.00', ['xpath' => '//tbody/tr[3]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product D are displayed correctly in the confirmation page.' );
$I->see( 'Product D', ['xpath' => '//tbody/tr[4]/td/strong'] );
$I->see( 'Option D-1: Choice D-1-2', ['xpath'=>'//tbody/tr[4]/td/ul/li'] );
$I->see( '2', ['xpath' => '//tbody/tr[4]/td[2]'] );
$I->see( '$6.00', ['xpath' => '//tbody/tr[4]/td[3]'] );
$I->see( '$12.00', ['xpath' => '//tbody/tr[4]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product E are displayed correctly in the confirmation page.' );
$I->see( 'Product E', ['xpath' => '//tbody/tr[5]/td/strong'] );
$I->see( 'Option E-1: Choice E-1-3', ['xpath'=>'//tbody/tr[5]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[5]/td[2]'] );
$I->see( '$8.00', ['xpath' => '//tbody/tr[5]/td[3]'] );
$I->see( '$8.00', ['xpath' => '//tbody/tr[5]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product F are displayed correctly in the confirmation page.' );
$I->see( 'Product F', ['xpath' => '//tbody/tr[6]/td/strong'] );
$I->see( 'Option F-1: Choice F-1-1', ['xpath'=>'//tbody/tr[6]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[6]/td[2]'] );
$I->see( '$7.00', ['xpath' => '//tbody/tr[6]/td[3]'] );
$I->see( '$7.00', ['xpath' => '//tbody/tr[6]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product G are displayed correctly in the confirmation page.' );
$I->see( 'Product G', ['xpath' => '//tbody/tr[7]/td/strong'] );
$I->see( 'Option G-1: Choice G-1-3', ['xpath'=>'//tbody/tr[7]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[7]/td[2]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[7]/td[3]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[7]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product H are displayed correctly in the confirmation page.' );
$I->see( 'Product H', ['xpath' => '//tbody/tr[8]/td/strong'] );
$I->see( 'Option H-1: Choice H-1-2', ['xpath'=>'//tbody/tr[8]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[8]/td[2]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[8]/td[3]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[8]/td[4]'] );

$I->amGoingTo( 'Check that the name, option chosen, quantity, unit price, and price for Product I are displayed correctly in the confirmation page.' );
$I->see( 'Product I', ['xpath' => '//tbody/tr[9]/td/strong'] );
$I->see( 'Option I-1: Choice I-1-1', ['xpath'=>'//tbody/tr[9]/td/ul/li'] );
$I->see( '1', ['xpath' => '//tbody/tr[9]/td[2]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[9]/td[3]'] );
$I->see( '$10.00', ['xpath' => '//tbody/tr[9]/td[4]'] );

$I->amGoingTo( 'Check the Shipping Price' );
$I->see( '$10.00', ['xpath'=>'//tfoot/tr/td[3]'] );

$I->amGoingTo( 'Check the Total' );
$I->see( '$77.00', ['xpath'=>'//tfoot/tr[2]/td[2]'] );






