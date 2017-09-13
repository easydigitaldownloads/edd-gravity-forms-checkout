<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Test field Default Label' );

$I->amOnPage( '/default-value-no-ajax' );
$form_id = GFFormsModel::get_form_id( 'Default Value' );

$I->seeInField( 'Default value', 'The default value' );
$I->seeInField( 'Textarea', 'Lorem ipsum dolor' );
$I->seeInField( 'Select', 'Second Choice' );
$I->seeInField( 'Number', '43' );
$I->seeInField( 'Prefix', 'Mr.' );
$I->seeInField( 'First', 'Firstname' );
$I->seeInField( 'Middle', 'Middlename' );
$I->seeInField( 'Last', 'Lastname' );
$I->seeInField( 'Suffix', 'Sufixname' );
$I->seeInField( 'Date', date( 'm/d/Y' ) );
$I->seeInField( 'HH', '10' );
$I->seeInField( 'MM', '58' );
$I->seeInField( 'select[id="input_' . $form_id . '_8_3"]', 'pm' );
$I->seeInField( 'Phone', '0210700000000' );
$I->seeInField( 'Website', 'https://www.google.com' );
$I->seeInField( 'Email', 'email@email.com' );
$I->seeInField( 'Post Title', 'Post title' );
$I->seeInField( 'Post Body', 'Lorem ipsum post text' );
$I->seeInField( 'Post Excerpt', 'Lorem ipsum post excerpt' );
$I->seeInField( 'Post Tags', 'tag 1, tag 2, tag 3' );
$I->seeInField( 'Post Custom Field', 'Custom field' );