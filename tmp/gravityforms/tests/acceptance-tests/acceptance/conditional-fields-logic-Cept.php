<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test conditional fields logic' );

$I->amOnPage( '/conditional-fields-logic-no-ajax/' );

$I->checkOption( 'Enable paragraph field' );
$I->seeElement( 'textarea[name="input_15"]' );
$I->selectOption( 'input[name=input_7]', 'First Choice' );
$I->seeElement( 'input[name="input_1"]' );
$I->selectOption( 'input[name=input_7]', 'Second Choice' );
$I->seeElement( 'textarea[name="input_2"]' );
$I->scrollTo( [ 'css' => '.gform_footer' ], 20, 50 );
$I->checkOption( 'First Choice Check' );
$I->checkOption( 'Second Choice Check' );
$I->seeElement( 'textarea[name="input_26"]' );
$I->see( 'bye' );
$I->selectOption( 'select[name=input_20]', 'Second Choice' );
$I->seeElement( 'textarea[name="input_19"]' );