<?php
/** * Author: Raquel Kohler
 * Date Created: 2017-05-05
 * Purpose: Test conditional logic for field display based on Checkboxes field selections
 *          Testing for is, is not, starts with, ends with, and contains for string and number values
 *          Testing for greater than, less than for number values; not supported for string values
 */

// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic for field display based on Checkboxes field selections' );
$I->amOnPage( '/clfd104-no-ajax/' );

$I->amGoingTo( 'Test field display by conditional logic based on IS checkbox selection' );
$I->checkOption('First Choice');
$I->waitForText('IS First Choice',1);
$I->uncheckOption('First Choice');

$I->amGoingTo( 'Test field display by conditional logic based on IS NOT checkbox selection' );
$I->checkOption('Second Choice');
$I->waitForText('IS NOT First Choice',1);
$I->uncheckOption('Second Choice');

$I->amGoingTo( 'Test field display by conditional logic based on GREATER THAN number' );
$I->checkOption('Third Choice');
$I->waitForText('GREATER THAN 9000',1);
$I->uncheckOption('Third Choice');

$I->amGoingTo( 'Test field display by conditional logic based on LESS THAN number' );
$I->checkOption( 'Fourth Choice' );
$I->waitForText('LESS THAN 100',1);
$I->uncheckOption( 'Fourth Choice' );

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH number' );
$I->checkOption( 'Fifth Choice' );
$I->waitForText('STARTS WITH 33',1);
$I->uncheckOption( 'Fifth Choice' );

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH text' );
$I->checkOption( 'Sixth Choice' );
$I->waitForText('STARTS WITH cod',1);
$I->uncheckOption( 'Sixth Choice' );

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS number' );
$I->checkOption( 'Seventh Choice' );
$I->waitForText('CONTAINS 22',1);
$I->uncheckOption( 'Seventh Choice' );

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS text' );
$I->checkOption( 'Eighth Choice' );
$I->waitForText('CONTAINS or',1);
$I->uncheckOption( 'Eighth Choice' );

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH number' );
$I->checkOption( 'Ninth Choice' );
$I->waitForText('ENDS WITH 89',1);
$I->uncheckOption( 'Ninth Choice' );

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH text' );
$I->checkOption( 'Tenth Choice' );
$I->waitForText('ENDS WITH us',1);
$I->uncheckOption( 'Tenth Choice' );