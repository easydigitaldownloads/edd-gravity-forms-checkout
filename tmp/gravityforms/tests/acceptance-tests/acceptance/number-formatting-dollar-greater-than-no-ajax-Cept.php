<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formatting display message for dollar greater than' );
/**
 * Set US Dollar currency
 */
update_option( 'rg_gforms_currency', 'USD' );

$I->amOnPage( '/test-number-formatting-decimals-no-ajax/' );

$I->dontSeeElement( 'input', [ 'name' => 'input_7' ] );
$I->dontSeeElement( 'input', [ 'name' => 'input_10' ] );
$I->dontSeeElement( 'input', [ 'name' => 'input_11' ] );

$I->fillField( 'Decimal Comma', '2,11' );
$I->waitForElementVisible( 'input[name=input_7]', 3 );

$I->fillField( 'Decimal Dot', '2.22' );
$I->waitForElementVisible( 'input[name=input_10]', 3 );

$I->fillField( 'Currency', '2.20' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->waitForElementVisible( 'input[name=input_11]', 3 );

$I->click( 'Submit' );

$I->waitForText( 'Decimal Comma is greater than 2,0', 3 );
$I->see( 'Decimal Dot is greater than 2.0' );
$I->see( 'Currency (dollar) is greater than 2.0' );
