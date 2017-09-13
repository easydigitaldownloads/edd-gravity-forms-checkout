<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-05-02
 * Purpose: Test that a field shown/hidden based on a calculated field, works correctly if a field in the calculation is shown/hidden
 */
// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a field shown/hidden based on a calculated field, works correctly if a field in the calculation is shown/hidden' );

$I->amOnPage( '/CLFD014-Field-Shown-Hidden-Based-on-Calculation-Works-When-Calculation-Impacted/' );

//fill out fields to see number field with calculation and field based on it to show if value greater than 5
$I->amGoingTo( 'Fill out fields to see number field with calculation and field based on it to show if value greater than 5' );
$I->fillField( 'Field 1', '4' );
$I->fillField( 'Field 2', '6' );
$I->waitForElementVisible( 'input[name="input_1"]', 1 );
$I->seeInField( 'Final Calculation', '10' );

//hide field used in calculation and verify number adjusts correctly and field hidden
$I->amGoingTo( 'Hide a field used in calculation and verify the Number adjusts correctly.' );
$I->fillField( 'Untitled', 'hideme' );
$I->waitForElementNotVisible( 'input[name="input_1"]', 1 );
$I->seeInField( 'Final Calculation', '4');

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD014 Field Shown Hidden Based on Calculation Works When Calculation Impacted' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );
$I->click( 'Test' );

//the hidden field should have no value and the calculation should only be the one value
$I->amGoingTo( 'Make sure that Field 2 is not 6 and that the calculation does not equal 10.' );
$I->dontSeeInSource( '<td colspan="2" class="entry-view-field-value">10</td>' );
$I->dontSeeInSource( '<td colspan="2" class="entry-view-field-value">6</td>' );