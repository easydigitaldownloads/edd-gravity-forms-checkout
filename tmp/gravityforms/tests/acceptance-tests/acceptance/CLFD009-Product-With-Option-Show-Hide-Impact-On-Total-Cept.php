<?php
/**
 * Author: Dana Cobb
 * Date Created: 2017-04-25
 * Purpose: Test that a total field updates correctly when an option field tied to a product is shown/hidden
 */
 // @group CLFD
 

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test that a total field updates correctly when an option field tied to a product is shown/hidden' );

$I->amOnPage( '/CLFD009-Product-With-Option-Show-Hide-Impact-On-Total/' );

$I->amGoingTo( 'Check that the total is $2.00 first');
$I->seeInField( 'input[name="input_3"]', '2' );

//Show the Option field and see that total has increased
$I->fillField( 'Untitled', 'showme' );
$I->wait( 1 );
$I->seeInField( 'input[name="input_3"]', '6' );

//Hide Option field and see total go back to $2
$I->fillField( 'Untitled', '' );
$I->seeInField( 'input[name="input_3"]', '2' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Get Form ID by title
$form_id = GFFormsModel::get_form_id( 'CLFD009 Product With Option Show Hide Impact On Total' );

// Go to Entry list
$I->amOnPage( '/wp-admin/admin.php?page=gf_entries&id=' . $form_id );

$I->waitForText( 'Product Name', 1 );

// Edit entry
$I->click( 'Product Name' );

//since Option was hidden, the total should be $2, not $6
$I->dontSee( '$6.00' );
