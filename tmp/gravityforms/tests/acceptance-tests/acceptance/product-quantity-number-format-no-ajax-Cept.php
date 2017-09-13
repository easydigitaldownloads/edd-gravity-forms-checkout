<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product quantity number format' );

$I->amOnPage( '/product-quantity-number-format-no-ajax/' );

$I->seeElement( 'input[name="input_2"]' );
$I->seeElement( 'input[name="input_3"]' );
$I->seeElement( 'input[name="input_4"]' );

$I->fillField( 'input[name="input_2"]', '1234' );
$I->fillField( 'input[name="input_3"]', '1234' );
$I->fillField( 'input[name="input_4"]', '$1,234.00' );

$I->executeJS( 'return jQuery(".gform_button").focus()' );
$I->seeInField( 'input[name="input_4"]', '$1,234.00' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );
$I->click( 'Forms' );
$I->click( 'Entries' );

$I->click( '#form_switcher_arrow' );
$I->click( '//*[@id="form_switcher_chosen"]/div/ul/li[text() = \'Product Quantity Number Format\']' );

$I->see( '1,234' );
$I->see( '1.234' );
$I->see( '$1,234.00 ' );