<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formatting dollar display for Portugues do Brasil language' );
/**
 * Set US Dollar currency
 */
// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );
$I->click( 'Forms' );
$I->click( 'Settings' );
$I->see( 'General Settings' );
$I->selectOption( 'Currency', 'U.S. Dollar' );
$I->click( 'Save Settings' );
$I->amOnPage( '/wp-admin/options-general.php' );
$I->see( 'General Settings' );
$I->selectOption( 'Site Language', 'Português do Brasil' );


$I->amOnPage( '/test-number-formatting-no-ajax/' );
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
