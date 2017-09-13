<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-05-04
 * Purpose: Test that second page shown/hidden by calculated field works correctly if one of the fields in the calculation is hidden
 */
 // @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that second page shown/hidden by calculated field works correctly if one of the fields in the calculation is hidden' );

$I->amOnPage( '/CLFD016-Calculation-Controls-Second-Page-Visibility-With-Fields-Shown-Hidden/' );

$I->amGoingTo( 'Fill out fields used in calculation so Next button is visible' );
$I->fillField( 'Number 1', '20' );
$I->fillField( 'Number 2', '5' );

$I->waitForElementVisible(['css' => '.gform_next_button'], 1);

$I->amGoingTo( 'Hide the Number 2 field so the button hides as well.' );
$I->fillField( 'Show Hide', 'hideme' );
$I->waitForElementNotVisible( 'input[name="input_3"]', 1 );
$I->waitForElementNotVisible(['css' => '.gform_next_button'], 1);

$I->amGoingTo( 'Fill Number 1 with 25 so the Next button shows.' );
$I->fillField( 'Number 1', '25' );
$I->waitForElementVisible(['css' => '.gform_next_button'], 1);