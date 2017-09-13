<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test number formating euro display' );
/**
 * Set Euro currency
 */
update_option( 'rg_gforms_currency', 'EUR' );

$I->amOnPage( '/test-number-formatting-with-ajax/' );
$I->waitForText( 'Currency', 3 );
$I->fillField( 'Currency', '3,33' );
// Used for focusing out currency field
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'Currency', '3,33 €' );
$I->seeInField( 'Calculated', '503,33 €' );

$I->click( 'Submit' );
$I->dontSee( 'Please enter a valid number' );

$I->see( '3,33 €' );
$I->see( '503,33 €' );

$I->amOnPage( '/test-number-formatting-with-ajax/' );
$I->waitForText( 'Currency', 3 );
$I->fillField( 'Currency', '3.100' );
// Used for focusing out currency field
$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'Currency', '3.100,00 €' );
$I->seeInField( 'Calculated', '3.600,00 €' );

$I->click( 'Submit' );
$I->dontSee( 'Please enter a valid number' );

$I->see( '3.100,00 €' );
$I->see( '3.600,00 €' );

update_option( 'rg_gforms_currency', 'USD' );
