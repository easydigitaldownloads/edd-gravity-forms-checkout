<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product shipping drop down' );

$I->amOnPage( '/product-shipping-dropdown-no-ajax/' );

$I->seeElement( '.ginput_container_select select[name="input_2"]' );
$I->selectOption( 'Shipping', 'Second Choice' );
$I->seeInField( 'input[name="input_3"]', '14' );