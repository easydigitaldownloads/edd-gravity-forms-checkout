<?php
/** * Author: Raquel Kohler
 * Date Created: 2017-05-05
 * Purpose: Test conditional logic for field display based on Multi Select field selections
 *          Testing for is, is not, starts with, ends with, and contains for string and number values
 *          Testing for greater than, less than for number values; not supported for string values
 */

// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic for field display based on Multi Select field selections' );
$I->amOnPage( '/clfd103-no-ajax/' );

$I->amGoingTo( 'Test field display by conditional logic based on IS multi select selection' );
$I->selectOption( 'Multi Select', 'First Choice' );
$I->waitForText('IS First Choice',1);

$I->amGoingTo( 'Test field display by conditional logic based on IS NOT multi select selection' );
$I->selectOption( 'Multi Select', 'Second Choice' );
$I->waitForText('IS NOT First Choice',1);

$I->amGoingTo( 'Test field display by conditional logic based on GREATER THAN number' );
$I->selectOption( 'Multi Select', 'Third Choice' );
$I->waitForText('GREATER THAN 9000',1);

$I->amGoingTo( 'Test field display by conditional logic based on LESS THAN number' );
$I->selectOption( 'Multi Select', 'Fourth Choice' );
$I->waitForText('LESS THAN 100',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH number' );
$I->selectOption( 'Multi Select', 'Fifth Choice' );
$I->waitForText('STARTS WITH 33',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH text' );
$I->selectOption( 'Multi Select', 'Sixth Choice' );
$I->waitForText('STARTS WITH cod',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS number' );
$I->selectOption( 'Multi Select', 'Seventh Choice' );
$I->waitForText('CONTAINS 22',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS text' );
$I->selectOption( 'Multi Select', 'Eighth Choice' );
$I->waitForText('CONTAINS or',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH number' );
$I->selectOption( 'Multi Select', 'Ninth Choice' );
$I->waitForText('ENDS WITH 89',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH text' );
$I->selectOption( 'Multi Select', 'Tenth Choice' );
$I->waitForText('ENDS WITH us',1);