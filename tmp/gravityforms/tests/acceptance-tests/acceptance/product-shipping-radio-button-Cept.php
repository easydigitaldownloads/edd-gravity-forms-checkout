<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product shipping radio button' );

// Set the currency format to USD
update_option( 'rg_gforms_currency', 'USD' );

$I->amOnPage( '/product-shipping-radio-button-no-ajax/' );

$I->waitForElement( '.ginput_container_radio input[type="radio"]', 3 );
$I->selectOption( 'form input[name=input_2]', 'Second Choice' );
$I->seeInField( 'input[name="input_3"]', '14' );
