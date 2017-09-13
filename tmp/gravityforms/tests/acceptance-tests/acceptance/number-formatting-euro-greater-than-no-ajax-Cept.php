<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formatting display message for euro greater than' );
/**
 * Set EUR currency
 */
update_option( 'rg_gforms_currency', 'EUR' );

$I->amOnPage( '/test-number-formatting-decimals-no-ajax/' );

$I->dontSeeElement( 'input', [ 'name' => 'input_12' ] );

$I->fillField( 'Currency', '2,2' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->waitForElementVisible('input[name=input_12]', 3);

$I->click( 'Submit' );

$I->waitForText( 'Currency (euro) is greater than 2,0', 3 );

update_option( 'rg_gforms_currency', 'USD' );
