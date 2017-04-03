<?php

/**
 * Add gravity form settings for user fields name and email
 */
GFForms::include_feed_addon_framework();

class eddUserFields extends GFFeedAddOn {

    protected $_version = '1.0';
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'edd-fields';
    protected $_path = 'edd-gravity-forms/edd-gravity-forms.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms EDD User Fields';
    protected $_short_title = 'EDD Payment Fields';
    private static $_instance = null;
    protected $_multiple_feeds = false;

    /**
     * Returns an instance of this class, and stores it in the $_instance property.
     *
     * @return eddUserFields $_instance An instance of this class.
     */
    public static function get_instance() {


        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Only show form settings menu if there are multiple fields to pick
	 *
	 * @param array $tabs Array of form settings tabs
	 * @param int $form_id GF Form ID
	 *
	 * @return array
	 */
	public function add_form_settings_menu( $tabs, $form_id ) {

		if ( $this->has_multiple_fields( $form_id ) ) {
			return parent::add_form_settings_menu( $tabs, $form_id );
		}

		return $tabs;
	}

	/**
	 *
	 * @param $form_id
	 *
	 * @return bool True: Form has multiple of at least one field type. False: Form does not have multiple (if any)
	 */
	private function has_multiple_fields( $form_id ) {

		$form = GFAPI::get_form( $form_id );

		foreach( array( 'name', 'address', 'email' ) as $field_type ) {

			$fields = GFAPI::get_fields_by_type( $form, array( $field_type ) );

			if ( sizeof( $fields ) > 1 ) {
				return true;
			}
		}

		return false;
	}

	public function get_edd_field_settings( $form = array() ) {

	    $return_fields = array();

	    if ( $form["fields"] ) {

		    /** @var GF_Field $form_field */
		    foreach ( $form["fields"] as $form_field ) {
			    switch ( $form_field->type ) {
				    case 'email':
				    case 'name':
				    case 'address':
				    	$return_fields[ $form_field->type ][] = array(
						    'label' => sprintf( esc_html__( '%s (Field ID #%d)', 'edd-gf' ), $form_field->get_field_label( true, '' ), $form_field->id ),
						    'value' => $form_field->id,
						    'default' => intval( ! isset( $return_fields[ $form_field->type ] ) ),
					    );
					    break;
			    }
		    }
	    }

	    return $return_fields;
    }

	public function feed_settings_title() {
		return esc_html__( 'Easy Digital Downloads', 'edd-gf' ) . '<i style="font-size: 1em; height: 1em; width: 1em;" class="dashicons dashicons-download"></i>';
	}

	/**
     * Configures the settings which should be rendered on the Form Settings > EDD User Fields Add-On tab.
     *
     * @param array $form Gravity Forms form object
     *
     * @return array
     */
    public function get_feed_settings_fields() {

    	$form = $this->get_current_form();

	    $edd_fields = $this->get_edd_field_settings( $form );

	    $edd_field_array = array(
	    	'name' => esc_html__('Name', 'edd-gf'),
		    'email' => esc_html__('Email', 'edd-gf'),
		    'address' => esc_html__('Address', 'edd-gf'),
	    );

	    $settings_fields = array();

	    foreach ( $edd_field_array as $key => $edd_field ) {

		    if ( ! empty( $edd_fields[ $key ] ) ) {

		    	$_setting_field = array(
				    'label' => $edd_field,
				    'type' => 'select',
				    'name' => $key,
				    'choices' => $edd_fields[ $key ],
			    );

			    if( sizeof( $edd_fields[ $key ] ) === 1 ) {
				    $_setting_field['type'] = 'text'; // TODO: Switch back to hidden
				    $_setting_field['value'] = $edd_fields[ $key ][0]['value'];
			    }

			    $settings_fields[] = $_setting_field;
		    }

	    }

        // return Name and Email fields settings
        return array(
            array(
                'description' => sprintf('<p class="subheading">%s</p>', 'What field do you want to use as the data source for Easy Digital Downloads purchase?' ),
                'fields' => $settings_fields,
            ),
        );
    }

}

eddUserFields::get_instance();
