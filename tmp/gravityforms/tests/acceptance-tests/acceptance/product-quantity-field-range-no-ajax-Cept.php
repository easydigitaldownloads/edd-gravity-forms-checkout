<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product quantity range' );

$I->amOnPage( '/product-quantity-field-range-no-ajax/' );

$I->see( 'Please enter a value between 1 and 5.' );

$I->fillField( 'Quantity range & field type number', '6' );
$I->click( 'Submit' );

$I->waitForText( 'Please enter a value between 1 and 5.', 3 );
$I->fillField( 'Quantity range & field type number', '5' );
$I->click( 'Submit' );

$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );