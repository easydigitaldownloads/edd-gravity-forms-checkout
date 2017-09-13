<?php
/*
Author: Dana Cobb
Date Created: 2017-06-26
Purpose: Test options, confirmation page for product type hidden with various merge tags on confirmation page
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
$I->wantTo( 'Test that the options and confirmation page work correctly for a hidden product type with separate quantity field and various merge tags on confirmation page.' );

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

$I->amGoingTo( 'Select the second checkbox option and verify that the total adjusts. There will be no adjustment on fields since they may all be selected.' );
$I->checkOption( 'Option B-2-3.00' );
$I->amGoingTo( 'Verify that the total adjusted after selecting the second checkbox.' );
$I->see( '$13.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

$I->amGoingTo( 'Select the third checkbox option and verify that the total adjusts. There will be no adjustment on fields since they may all be selected.' );
$I->checkOption( 'Option B-3-4.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the third checkbox.' );
$I->see( '$17.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

//Product Option Radio Button Tests
$I->amGoingTo( 'See that the radio button options are what is expected' );
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

$I->amGoingTo( 'Select the second radio button and verify that the other values adjust as expected.' );
$I->selectOption('form input[name=input_7]' , 'Option C-2 radio|10' );
$I->expectTo( 'See the first button as Option C-1-5.00 -$5.00 after selecting the second radio button.' );
$I->see( 'Option C-1-5.00 -$5.00' );
$I->expectTo( 'See the second button with no +/- amount next to it after selecting the second radio button.' );
$I->see( 'Option C-2-10.00' );
$I->expectTo( 'See the third button as Option C-3-15.00 +$5.00 after selecting the second radio button.' );
$I->see( 'Option C-3-15.00 +$5.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the second radio button.' );
$I->see( '$27.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

$I->amGoingTo( 'Select the third radio button and verify that the other values adjust as expected' );
$I->selectOption('form input[name=input_7]' , 'Option C-3-15.00 +$5.00' );
$I->expectTo( 'See the first button as Option C-1-5.00 -$10.00 after selecting the third radio button.' );
$I->see( 'Option C-1-5.00 -$10.00' );
$I->expectTo( 'See the second button as Option C-2-10.00 -$5.00 after selecting the third radio button.' );
$I->see( 'Option C-2-10.00 -$5.00' );
$I->expectTo( 'See the third button with no +/- amount next to it after selecting the third radio button.' );
$I->see( 'Option C-3-15.00' );

$I->amGoingTo( 'Verify that the total adjusted after selecting the third radio button.' );
$I->see( '$32.97', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

//select quantity
$I->amGoingTo( 'Select 2 as the product quantity' );
$I->selectOption( 'form select[name=input_8]', '2' );

$I->expectTo( 'See the total double after setting the quantity to 2.' );
$I->see( '$60.94', ['xpath'=> '//span[contains(@class, "ginput_total")]']);

//select confirmation page type
$I->amGoingTo( 'Select Individual Fields from the drop down for confirmation page display' );
$I->selectOption( 'form select[name=input_5]', 'Individual Fields' );

$I->amGoingTo( 'Submit the form and review the confirmation page.' );
$I->click( 'Submit' );

$I->amGoingTo( 'Review the data on the confirmation page.' );

//review the selected product options
$I->expectTo( 'See Option A: Option A-3-1.97 as the drop down selection on the confirmation page.');
$I->see( 'Option A: Option A-3-1.97', ['xpath'=>'//table/tbody/tr/td'] );

$I->amGoingTo( 'Check the VALUE merge tag for the drop down option on the confirmation page.' );
$I->see( 'Option A Value: A3', ['xpath'=>'//table/tbody/tr[2]/td'] );

$I->amGoingTo( 'Check the PRICE merge tag for the drop down option on the confirmation page.' );
$I->see( 'Option A Price: 1.97', ['xpath'=>'//table/tbody/tr[3]/td'] );

$I->amGoingTo( 'Check the currency merge tag for the drop down option on the confirmation page.' );
$I->see( 'Option A Currency: $1.97', ['xpath'=>'//table/tbody/tr[4]/td'] );

//review the checkbox options
//main checkbox
$I->expectTo( 'See all three checkbox options in a comma-delimited list on the confirmation page.' );
$I->see( 'Option B Checkbox: Option B-1-2.00, Option B-2-3.00, Option B-3-4.00', ['xpath'=>'//table/tbody/tr[5]/td'] );

$I->amGoingTo( 'Check the VALUE modifier for the main checkbox on the confirmation page.' );
$I->expectTo( 'See Option B Checkbox Value: Option B-1 checkbox, Option B-2 checkbox, Option B-3 checkbox as the selected checkboxes' );
$I->see( 'Option B Checkbox Value: Option B-1 checkbox, Option B-2 checkbox, Option B-3 checkbox', ['xpath'=>'//table/tbody/tr[6]/td'] );

$I->amGoingTo( 'Check the PRICE modifier for the main checkbox on the confirmation page.' );
$I->expectTo( 'See Option B Checkbox Price: 2, 3, 4' );
$I->see( 'Option B Checkbox Price: 2, 3, 4', ['xpath'=>'//table/tbody/tr[7]/td'] );

$I->amGoingTo( 'Check the CURRENCY modifier for the main checkbox on the confirmation page.' );
$I->expectTo( 'See Option B Checkbox Currency: $2.00, $3.00, $4.00' );
$I->see( 'Option B Checkbox Currency: $2.00, $3.00, $4.00', ['xpath'=>'//table/tbody/tr[8]/td'] );

//first checkbox
$I->amGoingTo( 'Check the first checkbox on the confirmation page.' );
$I->expectTo( 'See Option B1 Checkbox: Option B-1-2.00 ');
$I->see( 'Option B1 Checkbox: Option B-1-2.00', ['xpath'=>'//table/tbody/tr[9]/td'] );

$I->amGoingTo( 'Check the VALUE modifier for the first checkbox on the confirmation page.' );
$I->expectTo( 'See Option B1 Checkbox Value: Option B-1 checkbox' );
$I->see( 'Option B1 Checkbox Value: Option B-1 checkbox', ['xpath'=>'//table/tbody/tr[10]/td'] );

$I->amGoingTo( 'Check the PRICE modifier for the first checkbox on the confirmation page.' );
$I->expectTo( 'See Option B1 Checkbox Price: 2');
$I->see( 'Option B1 Checkbox Price: 2', ['xpath'=>'//table/tbody/tr[11]/td'] );

$I->amGoingTo( 'Check the CURRENCY modifier for the first checkbox on the confirmation page.' );
$I->expectTo( 'See Option B1 Checkbox Currency: $2.00', ['xpath'=>'//table/tbody/tr[12]/td'] );
$I->see( 'Option B1 Checkbox Currency: $2.00', ['xpath'=>'//table/tbody/tr[12]/td'] );

//second checkbox
$I->amGoingTo( 'Check the second checkbox on the confirmation page.' );
$I->expectTo( 'See Option B2 Checkbox: Option B-2-3.00 ');
$I->see( 'Option B2 Checkbox: Option B-2-3.00', ['xpath'=>'//table/tbody/tr[13]/td'] );

$I->amGoingTo( 'Check the VALUE modifier for the second checkbox on the confirmation page.' );
$I->expectTo( 'See Option B2 Checkbox Value: Option B-2 checkbox' );
$I->see( 'Option B2 Checkbox Value: Option B-2 checkbox', ['xpath'=>'//table/tbody/tr[14]/td'] );

$I->amGoingTo( 'Check the PRICE modifier for the second checkbox on the confirmation page.' );
$I->expectTo( 'Option B2 Checkbox Price3');
$I->see( 'Option B2 Checkbox Price3', ['xpath'=>'//table/tbody/tr[15]/td'] );

$I->amGoingTo( 'Check the CURRENCY modifier for the second checkbox on the confirmation page.' );
$I->expectTo( 'Option B2 Checkbox Currency: $3.00' );
$I->see( 'Option B2 Checkbox Currency: $3.00', ['xpath'=>'//table/tbody/tr[16]/td'] );

//third checkbox
$I->amGoingTo( 'Check the third checkbox on the confirmation page.' );
$I->expectTo( 'See Option B3 Checkbox: Option B-3-4.00' );
$I->see( 'Option B3 Checkbox: Option B-3-4.00', ['xpath'=>'//table/tbody/tr[17]/td'] );

$I->amGoingTo( 'Check the VALUE modifier for the third checkbox on the confirmation page.' );
$I->expectTo( 'See Option B3 Checkbox Value: Option B-3 checkbox' );
$I->see( 'Option B3 Checkbox Value: Option B-3 checkbox', ['xpath'=>'//table/tbody/tr[18]/td'] );

$I->amGoingTo( 'Check the PRICE modifier for the third checkbox on the confirmation page.' );
$I->expectTo( 'Option B3 Checkbox Price: 4');
$I->see( 'Option B3 Checkbox Price: 4', ['xpath'=>'//table/tbody/tr[19]/td'] );

$I->amGoingTo( 'Check the CURRENCY modifier for the third checkbox on the confirmation page.' );
$I->expectTo( 'Option B3 Checkbox Currency: $4.00' );
$I->see( 'Option B3 Checkbox Currency: $4.00', ['xpath'=>'//table/tbody/tr[20]/td'] );

//check radio button
$I->amGoingTo( 'Check the radio button on the confirmation page.' );
$I->expectTo( 'See Option C Radio Button: Option C-3-15.00' );
$I->see( 'Option C Radio Button: Option C-3-15.00', ['xpath'=>'//table/tbody/tr[21]/td'] );

$I->amGoingTo( 'Check the VALUE modifier for the radio button on the confirmation page.' );
$I->expectTo( 'See Option C Radio Button Value: Option C-3 radio' );
$I->see( 'Option C Radio Button Value: Option C-3 radio', ['xpath'=>'//table/tbody/tr[22]/td'] );

$I->amGoingTo( 'Check the PRICE modifier for the radio button on the confirmation page.' );
$I->expectTo( 'Option C Radio Button Price: 15');
$I->see( 'Option C Radio Button Price: 15', ['xpath'=>'//table/tbody/tr[23]/td'] );

$I->amGoingTo( 'Check the CURRENCY modifier for the radio button on the confirmation page.' );
$I->expectTo( 'Option C Radio Button Currency: $15.00' );
$I->see( 'Option C Radio Button Currency: $15.00', ['xpath'=>'//table/tbody/tr[24]/td'] );

//check quantity
$I->amGoingTo( 'Check the quantity on the confirmation page.' );
$I->expectTo( 'See Quantity: two' );
$I->see( 'Quantity: two', ['xpath'=>'//table/tbody/tr[25]/td'] );

$I->amGoingTo( 'Check the VALUE modifier for the quantity on the confirmation page.' );
$I->expectTo( 'See Quantity Value: 2' );
$I->see( 'Quantity Value: 2', ['xpath'=>'//table/tbody/tr[26]/td'] );

//total
$I->amGoingTo( 'Check the Total on the confirmation page.' );
$I->expectTo( 'See Total: $60.94' );
$I->see( 'Total: $60.94', ['xpath'=>'//table/tbody/tr[27]/td'] );

$I->amGoingTo( 'Check the PRICE modifier for the total on the confirmation page.' );
$I->expectTo( 'See Total Price: 60.94' );
$I->see( 'Total Price: 60.94', ['xpath'=>'//table/tbody/tr[28]/td'] );

$I->amGoingTo( 'Check the CURRENCY modifier for the total on the confirmation page.' );
$I->expectTo( 'See Total Currency: $60.94' );
$I->see( 'Total Currency: $60.94', ['xpath'=>'//table/tbody/tr[29]/td'] );