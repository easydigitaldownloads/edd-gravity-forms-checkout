<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test placeholder single line text' );

$I->amOnPage( '/placeholder-single-line-text/' );
$I->seeElement( 'input', [ 'placeholder' => 'Placeholder Single Line Text' ] );
$I->click( 'Submit' );
$I->see( 'At least one field must be filled out' );


$I->amOnPage( '/placeholder-single-line-text/' );
$I->fillField( 'Placeholder Single Line Text', 'Placeholder Single Line Text' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.' );
