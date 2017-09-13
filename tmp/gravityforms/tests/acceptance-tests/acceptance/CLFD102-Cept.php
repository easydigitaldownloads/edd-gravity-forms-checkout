<?php
/** * Author: Raquel Kohler
 * Date Created: 2017-05-05
 * Purpose: Test conditional logic for field display based on Dropdown field selections
 *          Testing for is, is not, starts with, ends with, and contains for string and number values
 *          Testing for greater than, less than for number values; not supported for string values
 */

// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic for field display based on Dropdown field selections' );
$I->amOnPage( '/clfd102-no-ajax/' );

$I->amGoingTo( 'Test field display by conditional logic based on IS dropdown selection' );
$I->selectOption( 'Dropdown', 'First Choice' );
$I->waitForText('IS First Choice',1);

$I->amGoingTo( 'Test field display by conditional logic based on IS NOT dropdown selection' );
$I->selectOption( 'Dropdown', 'Second Choice' );
$I->waitForText('IS NOT First Choice',1);

$I->amGoingTo( 'Test field display by conditional logic based on GREATER THAN number' );
$I->selectOption( 'Dropdown', 'Third Choice' );
$I->waitForText('GREATER THAN 9000',1);

$I->amGoingTo( 'Test field display by conditional logic based on LESS THAN number' );
$I->selectOption( 'Dropdown', 'Fourth Choice' );
$I->waitForText('LESS THAN 100',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH number' );
$I->selectOption( 'Dropdown', 'Fifth Choice' );
$I->waitForText('STARTS WITH 33',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH text' );
$I->selectOption( 'Dropdown', 'Sixth Choice' );
$I->waitForText('STARTS WITH cod',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS number' );
$I->selectOption( 'Dropdown', 'Seventh Choice' );
$I->waitForText('CONTAINS 22',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS text' );
$I->selectOption( 'Dropdown', 'Eighth Choice' );
$I->waitForText('CONTAINS or',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH number' );
$I->selectOption( 'Dropdown', 'Ninth Choice' );
$I->waitForText('ENDS WITH 89',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH text' );
$I->selectOption( 'Dropdown', 'Tenth Choice' );
$I->waitForText('ENDS WITH us',1);