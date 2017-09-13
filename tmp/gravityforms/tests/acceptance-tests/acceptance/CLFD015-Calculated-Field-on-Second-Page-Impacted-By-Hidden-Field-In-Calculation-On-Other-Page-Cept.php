<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-05-04
 * Purpose: Test that a calculated field on the second page works correctly when a field on the first page used in the calculation is hidden
 */
 // @group CLFD

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a calculated field on the second page works correctly when a field on the first page used in the calculation is hidden' );

$I->amOnPage( '/CLFD015-Calculated-Field-on-Second-Page-Impacted-By-Hidden-Field-In-Calculation-On-Other-Page/' );

//fill out fields used in calculation and go to next page
$I->fillField( 'Number 1', '4' );
$I->fillField( 'Number 2', '7' );
$I->click( 'Next' );

//the calculated field should show 9
$I->seeInField( 'Number', '11' );

//hide field used in calculation
$I->fillField( 'Untitled', 'hideme');
$I->wait( 1 );
$I->seeInField( 'Number', '4' );