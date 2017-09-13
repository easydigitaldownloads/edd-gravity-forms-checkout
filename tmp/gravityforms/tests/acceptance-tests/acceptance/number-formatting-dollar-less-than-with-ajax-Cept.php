<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formatting display message for dollar less than' );
/**
 * Set US Dollar currency
 */
update_option( 'rg_gforms_currency', 'USD' );

$I->amOnPage( '/test-number-formatting-decimals-with-ajax/' );

$I->dontSeeElement( 'input', [ 'name' => 'input_8' ] );
$I->dontSeeElement( 'input', [ 'name' => 'input_9' ] );

$I->fillField( 'Decimal Comma', '1,11' );
$I->waitForElementVisible('input[name=input_8]', 3);

$I->fillField( 'Decimal Dot', '1.22' );
$I->waitForElementVisible('input[name=input_9]', 3);

$I->fillField( 'Currency', '1.20' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );

$I->waitForElementVisible('input[name=input_13]', 3);

$I->click( 'Submit' );

$I->see( 'Decimal Comma conditional logic (less than)' );
$I->see( 'Decimal Dot conditional logic (less than)' );
$I->see( 'Calculated conditional logic (less than)' );
