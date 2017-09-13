<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-05-02
 * Purpose: Test that a number field with calculation, with calculation in fields in two different sections, works correctly when a section is hidden
 */
 // @group CLFD
 

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a number field with calculation, with calculated in fields in two different sections, works correctly when a section is hidden' );

$I->amOnPage( '/CLFD013-Number-Calculation-With-One-Field-in-Separate-Section-Shown-Hidden/' );

//fill out fields to see number field with calculation
$I->amGoingTo( 'Fill out Fields 1 and 2 to see the value 10 in the Number field.' );
$I->fillField( 'Field 1', '4' );
$I->fillField( 'Field 2', '6' );
$I->wait( 1 );
$I->seeInField( 'Number', '10' );

//hide field used in calculation and verify number adjusts correctly
$I->amGoingTo( 'Hide a field used in calculation and verify the Number adjusts correctly.' );
$I->fillField( 'Untitled', 'hideme' );
$I->waitForElementNotVisible( 'input[name="input_5"]', 1 );
$I->seeInField( 'Number', '4');

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD013 Number Calculation With One Field in Separate Section Shown Hidden' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );
$I->click( 'Test' );

//the hidden field should have no value and the calculation should only be the one value
$I->amGoingTo( 'Make sure that Field 2 is not 6 and that the calculation does not equal 10.' );
$I->dontSeeInSource( '<td colspan="2" class="entry-view-field-value">10</td>' );
$I->dontSeeInSource( '<td colspan="2" class="entry-view-field-value">6</td>' );