<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test field submit form with no fields validation' );

$I->amOnPage( '/all-fields-no-validation-no-ajax' );
$form_id = GFFormsModel::get_form_id( 'All fields NO validation' );

// Make sure we can see the visible field
$I->fillField( 'Single Line Text visible', 'First test' );

$I->fillField( 'Paragraph Text visible', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Non potes, nisi retexueris illa. Tenesne igitur, inquam, Hieronymus Rhodius quid dicat esse summum bonum, quo putet omnia referri oportere? Mihi quidem Antiochum, quem audis, satis belle videris attendere. Et harum quidem rerum facilis est et expedita distinctio. At iam decimum annum in spelunca iacet.' );

$I->selectOption( 'Drop Down visible', 'Drop Down Second Choice' );

$I->selectOption( 'Multi Select visible', 'Multi Select First Choice' );

$I->fillField( 'Number visible', '12345' );

$I->checkOption( 'Checkboxes First Choice' );

// Radio button
$I->scrollTo( [ 'class' => 'ginput_container_number' ] );
$I->selectOption( 'input[name=input_7]', 'Radio Boxes First Choice' );

$I->see( 'Section Break visible' );

$I->selectOption( 'Prefix', 'Mr.' );

$I->fillField( 'input[aria-label="First name"]', 'First name' );
$I->fillField( 'input[aria-label="Middle name"]', 'Middle name' );
$I->fillField( 'input[aria-label="Last name"]', 'Last name' );
$I->fillField( 'input[aria-label="Name suffix"]', 'Suffix name' );

// Show the datepicker
// we can use doubleClick ;)
// $I->doubleClick( '.hasDatepicker' );
// or execute JS on DatePicker
$I->executeJS( 'jQuery( ".hasDatepicker" ).datepicker( "show" );' );

$I->click( 'td.ui-datepicker-today a' );

$I->fillField( 'HH', '7' );
$I->fillField( 'MM', '25' );
$I->selectOption( '.gfield_time_ampm select', 'PM' );

$I->fillField( 'Phone visible', '1234567890' );

$I->fillField( 'Street Address', 'Street Address' );
$I->fillField( 'Address Line 2', 'Address Line 2' );
$I->fillField( 'City', 'City' );
$I->fillField( 'State / Province / Region', 'State / Province / Region' );
$I->fillField( 'ZIP / Postal Code', 'ZIP / Postal Code' );
$I->selectOption( 'Country', 'Albania' );

$I->fillField( 'Website visible', 'http://www.test.com' );

$I->fillField( 'Email visible', 'a@a.com' );

// file is stored in 'tests/_data/gravityforms.png'
$I->attachFile( '.ginput_container_fileupload input[type="file"]', 'gravityforms.png' );

$I->fillField( '.gfield_list_cell input[name="input_21[]"]', 'List1' );

$I->fillField( 'Post Title visible', 'Post Title' );

$I->fillField( 'Post Body visible', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Non potes, nisi retexueris illa. Tenesne igitur, inquam, Hieronymus Rhodius quid dicat esse summum bonum, quo putet omnia referri oportere? Mihi quidem Antiochum, quem audis, satis belle videris attendere. Et harum quidem rerum facilis est et expedita distinctio. At iam decimum annum in spelunca iacet.' );

$I->fillField( 'Post Excerpt visible', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' );

$I->fillField( 'Post Tags visible', 'PostTags' );

$I->selectOption( 'Post Category visible', 'Uncategorized' );

// file is stored in 'tests/_data/gravityforms.png'
$I->attachFile( '.ginput_container_post_image input[type="file"]', 'gravityforms.png' );

$I->fillField( 'Post Custom Field visible', 'post_custom_field_visible' );

$I->see( 'Product Name visible' );
$I->see( 'Price:' );

$I->fillField( 'Quantity visible', '2' );

$I->selectOption( 'Option visible', 'Second Option' );

$I->see( 'Shipping visible' );
$I->see( '$1.00' );

$I->see( 'Total visible' );
$I->see( '$11.00' );

$I->seeElementInDOM( '#field_' . $form_id . '_36 input[type=password]' );
$I->fillField( 'Password', 'password' );

// Scroll to bottom to see the Submit button
$I->scrollTo( [ 'css' => '.gform_button' ] );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 5 );
