<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test sub-label placement' );

$I->amOnPage( '/name-sub-label-placement-no-ajax/' );

$I->seeElement( [ 'css' => '.field_sublabel_below' ] );
$I->seeElement( [ 'css' => '.field_sublabel_above' ] );