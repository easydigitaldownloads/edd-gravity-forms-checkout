<?php
/** * Author: Raquel Kohler
 * Date Created: 2017-05-05
 * Purpose: Test conditional logic for field display based on Paragraph Text field selections
 *          Testing for is, is not, starts with, ends with, and contains for string and number values
 *          Testing for greater than, less than for number values; not supported for string values
 */
 
// @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional logic for field display based on Paragraph Text field selections' );
$I->amOnPage( '/clfd101-no-ajax/' );

$I->amGoingTo( 'Test field display by conditional logic based on IS number' );
$I->fillField( 'Paragraph text', '555' );
$I->waitForText('IS 555',1);

$I->amGoingTo( 'Test field display by conditional logic based on IS NOT text' );
$I->fillField( 'Paragraph text', 'test' );
$I->waitForText('IS NOT gravity',1);

$I->amGoingTo( 'Test field display by conditional logic based on GREATER THAN number' );
$I->fillField( 'Paragraph text', '9001' );
$I->waitForText('GREATER THAN 9000',1);

$I->amGoingTo( 'Test field display by conditional logic based on LESS THAN number' );
$I->fillField( 'Paragraph text', '40' );
$I->waitForText('LESS THAN 100',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH number' );
$I->fillField( 'Paragraph text', '330' );
$I->waitForText('STARTS WITH 33',1);

$I->amGoingTo( 'Test field display by conditional logic based on STARTS WITH text' );
$I->fillField( 'Paragraph text', 'code' );
$I->waitForText('STARTS WITH cod',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS number' );
$I->fillField( 'Paragraph text', '1221' );
$I->waitForText('CONTAINS 22',1);

$I->amGoingTo( 'Test field display by conditional logic based on CONTAINS text' );
$I->fillField( 'Paragraph text', 'form' );
$I->waitForText('CONTAINS or',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH number' );
$I->fillField( 'Paragraph text', '189' );
$I->waitForText('ENDS WITH 89',1);

$I->amGoingTo( 'Test field display by conditional logic based on ENDS WITH text' );
$I->fillField( 'Paragraph text', 'genius' );
$I->waitForText('ENDS WITH us',1);