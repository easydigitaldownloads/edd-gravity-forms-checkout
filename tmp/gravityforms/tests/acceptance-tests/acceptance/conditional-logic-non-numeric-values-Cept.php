<?php
/**
 * Author: Steve Henty
 * Date Created: 2017-07-17
 * Purpose: Test conditional logic greater than and less than rules work as expected for fields with non-numeric values.
 */

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic greater than and less than rules work as expected for fields with non-numeric values.' );

$I->amOnPage( '/conditional-logic-non-numeric-values-no-ajax/' );


$I->fillField( 'single line', 'test' );

// dropdown contains a mix of numeric and non-numeric values.

$I->selectOption( 'dropdown', 'Second Choice' );

// Field ID 4 will only display if dropdown value > zero

$I->expect( 'Field ID 4 is not visible' );
$I->cantSeeElement( 'input[name=input_4]' );

$I->selectOption( 'dropdown', '2' );

$I->expect( 'Field ID 4 is visible' );
$I->seeElement( 'input[name=input_4]' );

$I->selectOption( 'dropdown', 'Second Choice' );

$I->cantSeeElement( 'input[name=input_4]' );

// Submitting should not trigger a validation error.
$I->expect( 'the form to pass validation and submit' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );
$I->see( 'Thanks for contacting us!' );
