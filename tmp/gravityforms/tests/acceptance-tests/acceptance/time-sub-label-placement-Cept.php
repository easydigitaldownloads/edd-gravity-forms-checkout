<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display sub-label placement for time field' );

$I->amOnPage( '/time-sub-label-placement-no-ajax/' );

$I->seeElement( [ 'css' => '.field_sublabel_above' ] );
$I->seeElement( [ 'css' => '.field_sublabel_below' ] );