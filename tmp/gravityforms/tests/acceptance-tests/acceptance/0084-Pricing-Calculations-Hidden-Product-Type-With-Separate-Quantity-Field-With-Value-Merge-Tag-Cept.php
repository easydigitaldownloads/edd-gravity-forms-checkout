<?php
/*
Author: Dana Cobb
Date Created: 2017-06-19
Purpose: Test options, confirmation page for product type hidden with Value merge tags on confirmation page
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
$I->wantTo( 'Test that the options and confirmation page work correctly for a hidden product type with separate quantity field.' );

$I->amOnPage( '/0083-Pricing-Calculations-Hidden-Product-Type-With-Separate-Quantity-Field/' );

//Product Option Drop Down Tests
$I->amGoingTo( 'See that the product options in the drop down are what is expected.' );
$I->seeInField( 'select[name="input_2"]', 'Option A-1-1.75' );
$I->seeInSource( '<option value="A2|1.86" price=" +$0.11">Option A-2-1.86  +$0.11</option>' );
$I->seeInSource( '<option value="A3|1.97" price=" +$0.22">Option A-3-1.97  +$0.22</option>' );

$I->amGoingTo( 'Select the second product option in the drop down and verify that the other values adjust as expected.' );
$I->selectOption( 'form select[name=input_2]', 'Option A-2-1.86  +$0.11' );
$I->seeInSource( '<option value="A1|1.75" price=" -$0.11">Option A-1-1.75   -$0.11</option>' );
$I->seeInSource( '<option value="A3|1.97" price=" +$0.11">Option A-3-1.97   +$0.11</option>' );

$I->amGoingTo( 'Select the third product option in the drop down and verify that the other values adjust as expected.' );
$I->selectOption( 'form select[name=input_2]', 'Option A-3-1.97   +$0.11' );
$I->seeInSource( '<option value="A1|1.75" price=" -$0.22">Option A-1-1.75    -$0.22</option>' );
$I->seeInSource( '<option value="A2|1.86" price=" -$0.11">Option A-2-1.86    -$0.11</option>' );

$I->amGoingTo( 'Verify that the product total is 8.97' );
$I->seeInField( 'input[name="input_3"]', '8.97' );

//Product Option Checkbox Tests
$I->amGoingTo( 'See that the checkbox options are what is expected' );
$I->expectTo( 'See Option B-1-2.00 +$2.00 as the text for the first checkbox' );
$I->see( 'Option B-1-2.00 +$2.00' );
$I->expectTo( 'See Option B-2-3.00 +$3.00 as the text for the second checkbox' );
$I->see( 'Option B-2-3.00 +$3.00' );
$I->expectTo( 'See Option B-3-4.00 +$4.00 as the text for the third checkbox' );
$I->see( 'Option B-3-4.00 +$4.00' );

$I->amGoingTo( 'Select the first product checkbox option and verify that the total adjusts. There will be no adjustment on fields since they may all be selected.' );
$I->checkOption( 'Option B-1-2.00' );
$I->amGoingTo( 'Verify that the total adjusted after selecting the first checkbox.' );
$I->see( '$10.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

$I->amGoingTo( 'Select the second product checkbox option and verify that the total adjusts. There will be no adjustment on fields since they may all be selected.' );
$I->checkOption( 'Option B-2-3.00' );
$I->amGoingTo( 'Verify that the total adjusted after selecting the second checkbox.' );
$I->see( '$13.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

$I->amGoingTo( 'Select the third product checkbox option and verify that the total adjusts. There will be no adjustment on fields since they may all be selected.' );
$I->checkOption( 'Option B-3-4.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the third checkbox.' );
$I->see( '$17.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

//Product Option Radio Button Tests
$I->amGoingTo( 'See that the product radio button options are what is expected' );
$I->expectTo( 'See Option C-1-5.00 +$5.00 as the text for the first button' );
$I->see( 'Option C-1-5.00 +$5.00' );
$I->expectTo( 'See Option C-2-10.00 +$10.00 as the text for the second button' );
$I->see( 'Option C-2-10.00 +$10.00' );
$I->expectTo( 'See Option C-3-15.00 +$15.00 as the text for the third button' );
$I->see( 'Option C-3-15.00 +$15.00' );

$I->amGoingTo( 'Select the first product radio button and verify that the other values adjust as expected.' );
$I->selectOption('form input[name=input_7]', 'Option C-1 radio|5' );
$I->expectTo( 'See the first button with no +/- amount next to it after selecting it.' );
$I->see( 'Option C-1-5.00' );
$I->expectTo( 'See the second button as Option C-2-10.00 +$5.00 after selecting the first radio button.' );
$I->see( 'Option C-2-10.00 +$5.00' );
$I->expectTo( 'See the third button as Option C-3-15.00 +$10.00 after selecting the first radio button.' );
$I->see( 'Option C-3-15.00 +$10.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the first radio button.' );
$I->see( '$22.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

$I->amGoingTo( 'Select the second product radio button and verify that the other values adjust as expected.' );
$I->selectOption('form input[name=input_7]' , 'Option C-2 radio|10' );
$I->expectTo( 'See the first button as Option C-1-5.00 -$5.00 after selecting the second radio button.' );
$I->see( 'Option C-1-5.00 -$5.00' );
$I->expectTo( 'See the second button with no +/- amount next to it after selecting the second radio button.' );
$I->see( 'Option C-2-10.00' );
$I->expectTo( 'See the third button as Option C-3-15.00 +$5.00 after selecting the second radio button.' );
$I->see( 'Option C-3-15.00 +$5.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the second radio button.' );
$I->see( '$27.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

$I->amGoingTo( 'Select the third product radio button and verify that the other values adjust as expected' );
$I->selectOption('form input[name=input_7]' , 'Option C-3-15.00 +$5.00' );
$I->expectTo( 'See the first button as Option C-1-5.00 -$10.00 after selecting the third radio button.' );
$I->see( 'Option C-1-5.00 -$10.00' );
$I->expectTo( 'See the second button as Option C-2-10.00 -$5.00 after selecting the third radio button.' );
$I->see( 'Option C-2-10.00 -$5.00' );
$I->expectTo( 'See the third button with no +/- amount next to it after selecting the third radio button..' );
$I->see( 'Option C-3-15.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the third radio button.' );
$I->see( '$32.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

//select quantity
$I->amGoingTo( 'Select 2 as the product quantity.' );
$I->selectOption( 'form select[name=input_8]', '2' );

$I->expectTo( 'See the total double after setting the quantity to 2.' );
$I->see( '$60.94', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

//select confirmation page type
$I->amGoingTo( 'Select Pricing Fields Value from the drop down for confirmation page display' );
$I->selectOption( 'form select[name=input_5]', 'Pricing Fields Value' );

$I->amGoingTo( 'Submit the form and review the confirmation page.' );
$I->click( 'Submit' );

$I->amGoingTo( 'Review the data on the confirmation page.' );

//checking confirmation page data
$I->amGoingTo( 'Check the name of the product on the confirmation page.' );
$I->see( 'Product A - 2', ['xpath' => '//tbody/tr/td/strong'] );

$I->amGoingTo( 'Check that the chosen options are displayed and that if a value was set, the value shows on the confirmation page.' );

//check drop down option
$I->expectTo( 'See that the chosen drop down option displays Option A: A3 on the confirmation page.');
$I->see( 'Option A: A3', ['xpath'=>'//tbody/tr/td/ul/li'] );

//check checkbox options
$I->expectTo( 'See the first checkbox as Option B: Option B-1 checkbox on the confirmation page.' );
$I->see( 'Option B: Option B-1 checkbox', ['xpath'=>'//tbody/tr/td/ul/li[2]'] );

$I->expectTo( 'See the second checkbox as Option B: Option B-2 checkbox on the confirmation page.' );
$I->see( 'Option B: Option B-2 checkbox', ['xpath'=>'//tbody/tr/td/ul/li[3]'] );

$I->expectTo( 'See the third checkbox as Option B: Option B-3 checkbox on the confirmation page.' );
$I->see( 'Option B: Option B-3 checkbox', ['xpath'=>'//tbody/tr/td/ul/li[4]'] );

//check the radio button option
$I->expectTo( 'See the radio button as Option C: Option C-3 radio on the confirmation page.' );
$I->see( 'Option C: Option C-3 radio', ['xpath'=>'//tbody/tr/td/ul/li[5]'] );

//check the quantity
$I->expectTo( 'See a quantity of 2 on the confirmation page.' );
$I->see( '2', ['xpath'=>'//tbody/tr/td[2]'] );

//check unit price
$I->amGoingTo( 'Check the Unit Price on the confirmation page.' );
$I->see( '$27.97', ['xpath'=>'//tbody/tr/td[3]'] );

//check price
$I->amGoingTo( 'Check the Price on the confirmation page.' );
$I->see( '$55.94', ['xpath'=>'//tbody/tr/td[4]'] );

//check shipping
$I->amGoingTo( 'Check the Shipping Price on the confirmation page.' );
$I->expectTo( 'See shipping as $5.00' );
$I->see( '$5.00', ['xpath'=>'//tfoot/tr/td[3]/strong'] );

//check total
$I->amGoingTo( 'Check the Total on the confirmation page.' );
$I->expectTo( 'See the total as $60.94' );
$I->see( '$60.94', ['xpath'=>'//tfoot/tr[2]/td[2]/strong'] );