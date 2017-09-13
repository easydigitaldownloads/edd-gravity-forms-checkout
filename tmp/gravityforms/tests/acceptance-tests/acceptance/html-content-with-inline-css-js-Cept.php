<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test inline CSS & JS display inside HTML Content field' );

$I->amOnPage( '/html-content-with-inline-css-js-no-ajax/' );

$I->dontSee( 'jQuery(\'#gravity_html\').css(\'text-align\',\'center\');' );
$I->seeInField( 'Text', 'Single line text' );
$I->waitForJS( "return jQuery('#gravity_html[style*=\"text-align\"]');", 3 );
