<?php
/** * Author: Raquel Kohler
 * Date Created: 2017-05-05
 * Purpose: Test conditional logic for field display based on Single Line Text field selections
 *          Testing for is, is not, starts with, ends with, and contains for string and number values
 *          Testing for greater than, less than for number values; not supported for string values
 */

// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic for field display based on Single Line Text field selections' );
$I->amOnPage( '/clfd100-no-ajax/' );

$I->amGoingTo( 'Test field display by conditional logic based on IS number' );
$I->fillField( 'Single line text', '555' );
$I->waitForText('IS 555',1);

$I->amGoingTo( 'Test field display by conditional logic based on IS NOT text' );
$I->fillField( 'Single line text', 'test' );
$I->waitForText('IS NOT gravity',1);

$I->amGoingTo( 'Test field display by conditional logic based on GREATER THAN number' );
$I->fillField( 'Single line text', '9001' );
$I->waitForText('GREATER THAN 9000',1);

$I->amGoingTo( 'Test field display by conditional logic based on LESS THAN number' );
$I->fillField( 'Single line text', '40' );
$I->waitForText('LESS THAN 100',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH number' );
$I->fillField( 'Single line text', '330' );
$I->waitForText('STARTS WITH 33',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH text' );
$I->fillField( 'Single line text', 'code' );
$I->waitForText('STARTS WITH cod',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS number' );
$I->fillField( 'Single line text', '1221' );
$I->waitForText('CONTAINS 22',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS text' );
$I->fillField( 'Single line text', 'form' );
$I->waitForText('CONTAINS or',2);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH number' );
$I->fillField( 'Single line text', '189' );
$I->waitForText('ENDS WITH 89',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH text' );
$I->fillField( 'Single line text', 'genius' );
$I->waitForText('ENDS WITH us',1);