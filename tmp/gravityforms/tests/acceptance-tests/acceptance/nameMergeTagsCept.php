<?php
//$scenario->skip();
$I = new AcceptanceTester( $scenario );

$I->amOnPage( '/name-merge-tags' );

$I->waitForText( 'Name merge tags', 3 );

$I->fillField( 'First', 'Steve' );
$I->fillField( 'Last', 'Henty' );
$I->click( 'Submit' );
$I->waitForText( 'Steve', 3 );
$I->waitForText( 'Henty', 3 );
