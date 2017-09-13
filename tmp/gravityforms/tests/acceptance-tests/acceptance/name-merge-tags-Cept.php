<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test name merge tags' );

$I->amOnPage( '/name-merge-tags-no-ajax' );

$I->waitForText( 'Name merge tags', 3 );

$I->fillField( 'First', 'Steve' );
$I->fillField( 'Last', 'Henty' );
$I->click( 'Submit' );
$I->waitForText( 'Steve', 3 );
$I->waitForText( 'Henty', 3 );
