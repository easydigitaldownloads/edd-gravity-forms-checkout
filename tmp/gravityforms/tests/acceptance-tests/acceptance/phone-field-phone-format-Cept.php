<?php
// This test will fail on PhantomJS until the mask script is replaced.

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test phone field display' );

$I->amOnPage( '/phone-field-phone-format-no-ajax/' );

$I->see( 'Phone' );
$I->see( 'Phone International' );

$I->fillField( 'Phone', '2004321234' );
$I->fillField( 'Phone International', '+1234567890123' );

$I->seeInField( 'Phone', '(200) 432-1234' );
$I->seeInField( 'Phone International', '+1234567890123' );
