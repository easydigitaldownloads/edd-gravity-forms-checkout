<?php


$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test maximum upload file size' );

$I->amOnPage( '/maximum-file-size-no-ajax/' );

$form_id = GFFormsModel::get_form_id( 'Maximum File Size' );

$I->attachFile( 'input[name="input_1"]', '50 Ecommerce Growth Ideas Ebook for 2017.pdf' );
$I->attachFile( '#field_' . $form_id . '_2 input[type="file"]', '50 Ecommerce Growth Ideas Ebook for 2017.pdf' );

$I->waitForText( '50 Ecommerce Growth Ideas Ebook for 2017.pdf - File exceeds size limit', 3, '#field_' . $form_id . '_1 .validation_message' );
$I->waitForText( '50 Ecommerce Growth Ideas Ebook for 2017.pdf - File exceeds size limit', 3, '#field_' . $form_id . '_2 .validation_message' );
