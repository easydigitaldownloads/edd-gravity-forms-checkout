<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test enable chosen for select box' );

$I->amOnPage( '/enable-enhanced-user-interface-no-ajax/' );

$I->seeElementInDOM( '.chosen-search input' );
$I->seeElementInDOM( '.search-field input' );