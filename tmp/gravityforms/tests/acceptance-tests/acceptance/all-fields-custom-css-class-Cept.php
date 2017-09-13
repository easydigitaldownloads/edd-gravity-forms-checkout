<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test add custom CSS class for all fields' );
$form_id = GFFormsModel::get_form_id( 'All fields Custom CSS class' );

$I->amOnPage( '/all-fields-custom-css-class-no-ajax/' );

for ( $i = 1; $i <= 23; $i ++ ) {
	if ( $i == 8 ) {
		continue;
	}
	$I->seeElementInDOM( 'ul.gform_fields li#field_' . $form_id . '_' . $i . '.custom-class' );
}
$I->seeElementInDOM( '#gform_page_' . $form_id . '_2.custom-class' );