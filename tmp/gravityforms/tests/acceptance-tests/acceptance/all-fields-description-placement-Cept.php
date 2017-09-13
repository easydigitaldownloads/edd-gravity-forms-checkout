<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test description placement for all fields' );
$form_id = GFFormsModel::get_form_id( 'All fields Description placement' );

$I->amOnPage( '/all-fields-description-placement-no-ajax/' );

for ( $i = 1; $i <= 38; $i ++ ) {
	switch ( $i % 2 ) {
		case 1:
			$I->seeElement( [ 'css' => '#field_' . $form_id . '_' . $i . '.field_description_below' ] );
			break;
		case 2:
			$I->seeElement( [ 'css' => '#field_' . $form_id . '_' . $i . '.field_description_above' ] );
			break;
	}
}
$I->seeElement( [ 'css' => '#field_' . $form_id . '_39.field_description_above' ] );
$I->seeElement( [ 'css' => '#field_' . $form_id . '_40.field_description_below' ] );
$I->seeElement( [ 'css' => '#field_' . $form_id . '_41.field_description_above' ] );