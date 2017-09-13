<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test field Maximum Characters' );

$I->amOnPage( '/field-maximum-characters-no-ajax/' );

$I->fillField( 'Title', 'Lorem & ipsum' );
$I->executeJS( "tinymce.get( jQuery( 'textarea.wp-editor-area' ).attr( 'id' ) ).setContent( 'Lorem & ipsum' );" );
$I->fillField( 'Post Body', 'Lorem & ipsum' );
$I->fillField( 'Post Excerpt', 'Lorem & ipsum' );
$I->fillField( 'Post Custom Field', 'Lorem & ipsum' );

$I->seeInField( 'Title', 'Lorem & ip' );
$I->seeInField( 'Post Body', 'Lorem & ip' );
$I->seeInField( 'Post Excerpt', 'Lorem & ip' );
$I->seeInField( 'Post Custom Field', 'Lorem & ip' );

$I->click( 'Submit' );
$I->waitForText( 'The text entered exceeds the maximum number of characters.' );
$I->executeJS( "tinymce.get( jQuery( 'textarea.wp-editor-area' ).attr( 'id' ) ).setContent( 'Lorem & ip' );" );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.' );