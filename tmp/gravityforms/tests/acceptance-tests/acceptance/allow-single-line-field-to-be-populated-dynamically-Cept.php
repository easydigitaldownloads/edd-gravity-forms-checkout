<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test allow single line text field dynamically populated' );

$I->amOnPage( '/allow-single-line-field-to-be-populated-dynamically-no-ajax/?field=abcd123' );

$I->seeInField( 'Untitled', 'abcd123' );
