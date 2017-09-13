<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-04-24
 * Purpose: Test calculation using the calculation product field works correctly when field used in its calculation is shown/hidden
 */
 // @group CLFD
 

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test calculation using the calculated product field works correctly when field used in its calculation is shown/hidden' );

$I->amOnPage( '/CLFD006-Calculation-Impacted-By-Calculation-Product-Field-Show-Hide/' );

//$I->fillField( 'Quantity', '2' );
$I->fillField( 'input[name="input_2.3"]', '2' );
$I->amGoingTo( 'Click the Include Shipping checkbox so the Shipping field displays' );
$I->checkOption( 'input[name="input_5.1"]' );
$I->waitForText( 'Shipping', 1 );
$I->seeInField( 'input[name="input_2.2"]', '$10.00' );

$I->amGoingTo( 'Click the Include Shipping checkbox so the Shipping field hides');
$I->checkOption( 'input[name="input_5.1"]' );
$I->waitForElementNotVisible( '#input_48_4', 1 );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD006 Calculation Impacted by Calculation Product Field Show Hide' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Test', 1 );

// Edit entry
$I->click( 'Test' );

//since Shipping was hidden and part of the Product Price's calculation, the price should be $0
$I->waitForText( '$0.00', 1 );
