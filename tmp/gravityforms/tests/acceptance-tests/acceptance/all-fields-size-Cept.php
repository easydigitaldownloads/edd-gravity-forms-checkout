<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test all fields Field Size' );
$form_id = GFFormsModel::get_form_id( 'All fields Field Size' );

$I->amOnPage( '/all-fields-field-size-no-ajax' );

$inputs = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31 );

$i = 1;
foreach ( $inputs as $input ) {
	switch ( $i % 3 ) {
		case 1:
			if ( $input == 10 ) {
				$I->seeElement( [ 'css' => '.ginput_container .small' ], [ 'name' => 'input_' . $input . '[]' ] );
			} else {
				$I->seeElement( [ 'css' => '.ginput_container .small' ], [ 'name' => 'input_' . $input ] );
			}
			break;
		case 2:
			if ( $input == 11 ) {
				$I->seeElement( [ 'css' => '.ginput_container .medium' ], [ 'name' => 'input_' . $input . '[]' ] );
			} else {
				$I->seeElement( [ 'css' => '.ginput_container .medium' ], [ 'name' => 'input_' . $input ] );
			}
			break;
		case 0:
			if ( $input == 12 ) {
				$I->seeElement( [ 'css' => '.ginput_container .large' ], [ 'name' => 'input_' . $input . '[]' ] );
			} else {
				$I->seeElement( [ 'css' => '.ginput_container .large' ], [ 'name' => 'input_' . $input ] );
			}
			break;
	}
	$i ++;
}