<?php

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test maximum number of files for multiple files upload' );

$I->amOnPage( '/maximum-number-of-files-for-multiple-files-no-ajax/' );

$I->seeElement( '.ginput_container_fileupload' );

$I->attachFile( '.ginput_container_fileupload input[type="file"]', 'gravityforms.png' );
$I->attachFile( '.ginput_container_fileupload input[type="file"]', 'gravityforms.png' );
$I->attachFile( '.ginput_container_fileupload input[type="file"]', 'gravityforms.png' );

$I->waitForText( 'Maximum number of files reached', 3 );
