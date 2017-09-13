<?php
/** * Author: Raquel Kohler
 * Date Created: 2017-05-18
 * Purpose: Test conditional logic for field display based on Product Radio Buttons field selections
 *          Testing for is, is not, starts with, ends with, and contains for number values
 */

// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic for field display based on Product Radio Buttons field selections' );
$I->amOnPage( '/clfd107-no-ajax/' );

$I->amGoingTo( 'Test field display by conditional logic based on IS product radio button selection' );
$I->selectOption( 'input[name=input_20]', 'First Choice' );
$I->waitForText('IS First Choice',1);

$I->amGoingTo( 'Test field display by conditional logic based on IS NOT product radio button selection' );
$I->selectOption( 'input[name=input_20]', 'Second Choice' );
$I->waitForText('IS NOT First Choice',1);

$I->amGoingTo( 'Test field display by conditional logic based on GREATER THAN number' );
$I->selectOption( 'input[name=input_20]', 'Third Choice' );
$I->waitForText('GREATER THAN 9000',1);

$I->amGoingTo( 'Test field display by conditional logic based on LESS THAN number' );
$I->selectOption( 'input[name=input_20]', 'Fourth Choice' );
$I->waitForText('LESS THAN 100',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH number' );
$I->selectOption( 'input[name=input_20]', 'Fifth Choice' );
$I->waitForText('STARTS WITH 33',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS number' );
$I->selectOption( 'input[name=input_20]', 'Sixth Choice' );
$I->waitForText('CONTAINS 22',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH number' );
$I->selectOption( 'input[name=input_20]', 'Seventh Choice' );
$I->waitForText('ENDS WITH 89',1);