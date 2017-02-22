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
    protected $_short_title = 'EDD User Fields Add-On';
    private static $_instance = null;

    /**
     * Returns an instance of this class, and stores it in the $_instance property.
     *
     * @return object $_instance An instance of this class.
     */
    public static function get_instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Configures the settings which should be rendered on the Form Settings > EDD User Fields Add-On tab.
     *
     * @return array
     */
    public function form_settings_fields($form) {
        // get form fields 
        $email_fields[] = $name_fields[] = array(
            'label' => esc_html__('SELECT FIELD', 'edd-gf'),
            'value' => '',
        );
        if ($form["fields"]) {
            foreach ($form["fields"] as $form_field) {
                if ($form_field->type == 'email') {
                    $email_fields[] = array(
                        'label' => $form_field->label,
                        'value' => $form_field->id,
                    );
                } elseif ($form_field->type == 'name') {
                    $name_fields[] = array(
                        'label' => $form_field->label,
                        'value' => $form_field->id,
                    );
                }
            }
        }
        // return Name and Email fields settings
        return array(
            array(
                'title' => esc_html__('EDD Settings', 'edd-gf'),
                'fields' => array(
                    array(
                        'label' => esc_html__('Name', 'edd-gf'),
                        'type' => 'select',
                        'name' => 'name_field',
                        // 'tooltip' => esc_html__('This is the tooltip', 'edd-gf'),
                        'choices' => $name_fields,
                    ),
                    array(
                        'label' => esc_html__('Email', 'edd-gf'),
                        'type' => 'select',
                        'name' => 'email_field',
                        // 'tooltip' => esc_html__('This is the tooltip', 'edd-gf'),
                        'choices' => $email_fields,
                    ),
                ),
            ),
        );
    }

}

$obj = new eddUserFields();
