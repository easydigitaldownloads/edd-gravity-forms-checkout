<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product quantity field type' );

$I->amOnPage( '/product-quantity-field-type-no-ajax/' );
$I->seeElement( 'select[name="input_3"]' );
$I->dontSeeElement( 'input[name="input_4"]' );
$I->seeElement( 'input[name="input_5"]' );

