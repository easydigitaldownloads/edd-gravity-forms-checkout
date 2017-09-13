<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formating dollar display' );
/**
 * Set US Dollar currency
 */
update_option( 'rg_gforms_currency', 'USD' );


$I->amOnPage( '/test-number-formatting-no-ajax/' );
$I->waitForText( 'Decimal Comma', 3 );
$I->fillField( 'Decimal Comma', '1,11' );
$I->fillField( 'Decimal Dot', '2.22' );
$I->fillField( 'Currency', '3.33' );
// Used for focusing out currency field
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'Currency', '$3.33' );
$I->seeInField( 'Calculated', '$503.33' );

$I->click( 'Submit' );
$I->dontSee( 'Please enter a valid number' );

$I->see( '1,11' );
$I->see( '2.22' );
$I->see( '$3.33' );
$I->see( '$503.33' );


$I->amOnPage( '/test-number-formatting-no-ajax/' );
$I->fillField( 'Decimal Comma', '1.100' );
$I->fillField( 'Decimal Dot', '2,200' );

$I->click( 'Submit' );
$I->dontSee( 'Please enter a valid number' );

$I->see( '1.100' );
$I->see( '2,200' );


$I->amOnPage( '/test-number-formatting-no-ajax/' );
$I->fillField( 'Decimal Comma', '1.11' );
$I->fillField( 'Decimal Dot', '2,22' );
$I->fillField( 'Currency', '3,100' );
// Used for focusing out currency field
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'Currency', '$3,100.00' );
$I->seeInField( 'Calculated', '$3,600.00' );

$I->click( 'Submit' );
$I->see( 'Please enter a valid number' );
