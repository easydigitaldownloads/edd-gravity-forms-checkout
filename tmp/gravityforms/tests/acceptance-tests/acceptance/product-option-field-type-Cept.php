<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product option by field type' );

$I->amOnPage( '/product-option-field-type-no-ajax/' );

$I->seeElement( '.ginput_container_select select[name="input_2"]' );
$I->seeElement( '.ginput_container_checkbox input[name="input_3.1"]' );
$I->seeElement( '.ginput_container_radio input[name="input_4"]' );