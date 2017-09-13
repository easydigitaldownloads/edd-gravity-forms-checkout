<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test display product name by field type' );

$I->amOnPage( '/product-name-field-type-no-ajax/' );

$form_id = GFFormsModel::get_form_id( 'Product Name Field type' );

$I->waitForElement( '.ginput_container_singleproduct' );
$I->waitForElement( '.ginput_container_select #input_' . $form_id . '_2' );
$I->waitForElement( '.ginput_container_radio #input_' . $form_id . '_3' );
$I->waitForElement( '.ginput_container_product_price #input_' . $form_id . '_4' );
$I->dontSeeElement( 'li.gfield_price_' . $form_id . '_5 ' );
$I->waitForElement( '.ginput_container_product_calculation #input_' . $form_id . '_6' );
