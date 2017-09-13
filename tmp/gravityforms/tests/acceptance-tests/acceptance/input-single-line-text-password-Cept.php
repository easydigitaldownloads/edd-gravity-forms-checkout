<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test enable password input for single line text' );

$I->amOnPage( '/password-input-single-line-text-no-ajax' );
$I->seeElementInDOM( 'input[type=password]' );
