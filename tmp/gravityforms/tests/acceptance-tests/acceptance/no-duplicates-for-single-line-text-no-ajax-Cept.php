<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test no duplicates for single line text' );

$I->amOnPage( '/no-duplicates-for-single-line-text-no-ajax/' );
$I->fillField( 'Untitled', 'abc' );
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us!' );


$I->amOnPage( '/no-duplicates-for-single-line-text-no-ajax/' );
$I->fillField( 'Untitled', 'abcd' );
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us!' );


$I->amOnPage( '/no-duplicates-for-single-line-text-no-ajax/' );
$I->fillField( 'Untitled', 'abc' );
$I->click( 'Submit' );
$I->see( 'This field requires a unique entry and \'abc\' has already been used' );
