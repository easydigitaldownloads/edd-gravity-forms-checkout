<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test custom mask for single line text' );

$I->amOnPage( '/input-custom-mask-single-line-text-no-ajax/' );

$I->fillField( 'SSN', '12a45e78901' );
$I->fillField( 'Date', '2s151ds2' );
$I->fillField( 'Code', 'Y3N3O1' );
$I->fillField( 'Alphanumeric', '&sw12345ge' );
$I->fillField( 'Mixed', '1c212d11B2o1d198h654329' );

$I->executeJS( 'jQuery(".gform_button").focus()' );

$I->seeInField( 'SSN', '' );
$I->seeInField( 'Date', '' );
$I->seeInField( 'Code', '' );
$I->seeInField( 'Alphanumeric', '' );
$I->seeInField( 'Mixed', '' );

$I->fillField( 'SSN', '' ); // Give focus to field before adding content. Required for PhantomJS.
$I->fillField( 'SSN', '12345678901' );

$I->fillField( 'Date', '' );
$I->fillField( 'Date', '20161212' );
$I->fillField( 'Code', '' );
$I->fillField( 'Code', 'YON321' );
$I->fillField( 'Alphanumeric', '' );
$I->fillField( 'Alphanumeric', 'dsw12345ge' );
$I->fillField( 'Mixed', '' );
$I->fillField( 'Mixed', '10212011Bio101987654329' );

$I->executeJS( 'jQuery(".gform_button").focus()' );

$I->seeInField( 'SSN', '123-4567-8901' );
$I->seeInField( 'Date', '2016-12-12' );
$I->seeInField( 'Code', 'YON 321' );
$I->seeInField( 'Alphanumeric', 'dsw-1234-5ge' );
$I->seeInField( 'Mixed', '10/21/2011 + Bio 101 + 987-65-4329' );
