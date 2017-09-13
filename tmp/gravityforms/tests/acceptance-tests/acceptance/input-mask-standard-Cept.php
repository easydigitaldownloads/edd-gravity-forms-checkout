<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test standard mask for input fields' );

$I->amOnPage( '/input-mask-standard-single-line-text-no-ajax/' );

$I->fillField( 'US Phone', '' );
$I->fillField( 'US Phone', 'abc123' );

$I->fillField( 'US Phone + Ext', '' );
$I->fillField( 'US Phone + Ext', 'abc123' );

$I->fillField( 'Date', '' );
$I->fillField( 'Date', 'abc123' );

$I->fillField( 'Tax ID', '' );
$I->fillField( 'Tax ID', 'abc123' );

$I->fillField( 'SSN', '' );
$I->fillField( 'SSN', 'abc123' );

$I->fillField( 'Zip Code', '' );
$I->fillField( 'Zip Code', 'abc123' );

$I->fillField( 'Full Zip Code', '' );
$I->fillField( 'Full Zip Code', 'abc123' );

$I->executeJS( 'return jQuery(".gform_button").focus()' );

$I->seeInField( 'US Phone', '' );
$I->seeInField( 'US Phone + Ext', '' );
$I->seeInField( 'Date', '' );
$I->seeInField( 'Tax ID', '' );
$I->seeInField( 'SSN', '' );
$I->seeInField( 'Zip Code', '' );
$I->seeInField( 'Full Zip Code', '' );

$I->fillField( 'US Phone', '' );
$I->fillField( 'US Phone', '1234567890' );

$I->fillField( 'US Phone + Ext', '' );
$I->fillField( 'US Phone + Ext', '123456789012345' );

$I->fillField( 'Date', '' );
$I->fillField( 'Date', '22222222' );

$I->fillField( 'Tax ID', '' );
$I->fillField( 'Tax ID', '123456789' );

$I->fillField( 'SSN', '' );
$I->fillField( 'SSN', '123456789' );

$I->fillField( 'Zip Code', '' );
$I->fillField( 'Zip Code', '12345' );

$I->fillField( 'Full Zip Code', '' );
$I->fillField( 'Full Zip Code', '123456789' );

$I->executeJS( 'return jQuery(".gform_button").focus()' );

$I->seeInField( 'US Phone', '(123) 456-7890' );
$I->seeInField( 'US Phone + Ext', '(123) 456-7890 x12345' );
$I->seeInField( 'Date', '22/22/2222' );
$I->seeInField( 'Tax ID', '12-3456789' );
$I->seeInField( 'SSN', '123-45-6789' );
$I->seeInField( 'Zip Code', '12345' );
$I->seeInField( 'Full Zip Code', '12345-6789' );
