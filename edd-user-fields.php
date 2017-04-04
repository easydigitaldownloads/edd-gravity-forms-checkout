<?php

/**
 * Add gravity form settings for user fields name and email
 */
GFForms::include_addon_framework();

class eddUserFields extends GFAddOn {

    protected $_version = '1.0';
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'edd-fields';
    protected $_path = 'edd-gravity-forms/edd-gravity-forms.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms EDD User Fields';
    protected $_short_title = 'EDD Fields';
    private static $_instance = null;

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

        $fields = GFAPI::get_fields_by_type( $form, array( 'email', 'name', 'address' ) );

	    /** @var GF_Field $form_field */
	    foreach ( $fields as $form_field ) {
	        $return_fields[ $form_field->type ][] = array(
			    'label' => sprintf( esc_html__( '%s (Field ID #%d)', 'edd-gf' ), $form_field->get_field_label( true, '' ), $form_field->id ),
			    'value' => $form_field->id,
			    'default' => intval( ! isset( $return_fields[ $form_field->type ] ) ), // Is this the first one set? If so, mark as default.
		    );
	    }

	    return $return_fields;
    }

	/**
	 * EDD ICON, BABY!
	 *
	 * @return string
	 */
    public function form_settings_icon() {
	    return '<i class="dashicons dashicons-download" style="font-size: 1.3em; width: 1em; line-height: 1em; height: 1em;"></i>';
    }

	/**
     * Configures the settings which should be rendered on the Form Settings > EDD User Fields Add-On tab.
     *
     * @param array $form Gravity Forms form object
     *
     * @return array
     */
    public function form_settings_fields( $form ) {

	    $edd_fields = array();

	    $form_fields_to_use = GFAPI::get_fields_by_type( $form, array( 'email', 'name', 'address' ) );

	    /** @var GF_Field $form_field */
	    foreach ( $form_fields_to_use as $form_field ) {
		    $edd_fields[ $form_field->type ][] = array(
			    'label' => sprintf( esc_html__( '%s (Field ID #%d)', 'edd-gf' ), $form_field->get_field_label( true, '' ), $form_field->id ),
			    'value' => $form_field->id,
			    'default' => intval( ! isset( $edd_fields[ $form_field->type ] ) ), // Is this the first one set? If so, mark as default.
		    );
	    }

	    $edd_field_array = array(
	    	'name' => esc_html__('Name', 'edd-gf'),
		    'email' => esc_html__('Email', 'edd-gf'),
		    'address' => esc_html__('Address', 'edd-gf'),
	    );

	    $settings_fields = array();

	    $allow_override = isset( $_GET['allow-override'] );

	    foreach ( $edd_field_array as $key => $edd_field ) {

		    if ( ! empty( $edd_fields[ $key ] ) ) {

		    	$_setting_field = array(
				    'label' => $edd_field,
				    'type' => ( $allow_override ? 'text' : 'select' ),
				    'name' => $key,
				    'choices' => $edd_fields[ $key ],
			    );

		    	// Shouldn't be visible, unless people manually add query string. Don't want to prevent altogether.
			    if( sizeof( $edd_fields[ $key ] ) === 1 ) {
				    $_setting_field['type'] = $allow_override ? 'text' : 'hidden';
				    $_setting_field['value'] = $edd_fields[ $key ][0]['value'];
			    }

			    $settings_fields[] = $_setting_field;
		    }

	    }

	    $title = esc_html__('EDD Fields', 'edd-gf');
	    $description = sprintf('<p class="subheading">%s</p>', esc_html__( 'Choose the fields to use as the data sources for Easy Digital Downloads purchases.', 'edd-gf' ) );

	    if ( $allow_override ) {
		    $title .= ' ' . esc_html__( '(Custom Override)', 'edd-gf' );
		    $description .= sprintf( '<p><strong>%s</strong></p>', esc_html__( 'Enter the ID or custom key of the field you want to use as the source of the corresponding EDD data.' ) );
	    }

        // return Name and Email fields settings
        return array(
            array(
	            'title' => $title,
                'description' => $description,
                'fields' => $settings_fields,
            ),
        );
    }

}

eddUserFields::get_instance();
