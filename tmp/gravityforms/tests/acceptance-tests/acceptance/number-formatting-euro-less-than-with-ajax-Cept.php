<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formating display message for euro less than' );
/**
 * Set EUR currency
 */
update_option( 'rg_gforms_currency', 'EUR' );

$I->amOnPage( '/test-number-formatting-decimals-with-ajax/' );

$I->dontSeeElement( 'input', [ 'name' => 'input_14' ] );

$I->fillField( 'Currency', '1,2' );
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->waitForElementVisible( 'input[name=input_14]', 3 );

$I->click( 'Submit' );

$I->waitForText( 'Currency (euro) is less than 2,0', 3 );

update_option( 'rg_gforms_currency', 'USD' );
