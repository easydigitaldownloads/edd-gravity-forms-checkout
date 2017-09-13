<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test input mask for US phone single line text' );

$I->amOnPage( '/input-mask-us-phone-single-line-text-no-ajax/' );
$I->click( 'Submit' );
$I->see( 'At least one field must be filled out' );


$I->amOnPage( '/input-mask-us-phone-single-line-text-no-ajax/' );
$I->fillField( 'Single Line Input Mask US Phone', '1234567890' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.' );
