<?php

class KWS_GF_EDD_Admin {

	/**
	 * Add hooks and setup licensing
	 */
	function __construct() {

		// We want to be in the admin.
		if (!(is_admin() || (defined('DOING_CRON') && DOING_CRON))) { return; }

		add_action( 'plugins_loaded', array(&$this, 'admin_init') );
		add_action( 'admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts') );

		add_filter( 'gform_noconflict_styles', array( $this, 'register_no_conflict') );
		add_filter( 'gform_noconflict_scripts', array( $this, 'register_no_conflict') );

		add_action( 'admin_notices', array(&$this, 'gravity_forms_required') );

		// Enable debug with Gravity Forms Logging Add-on
	    add_filter( 'gform_logging_supported', array( $this, 'enable_gform_logging' ) );
	}

	/**
	 * Allow EDD GF scripts to be shown with Gravity Forms No-Conflict Mode
	 * @since 1.0.4
	 * @param  array      $registered Allowed scripts or styles for GF
	 * @return array                  Allowed scripts or styles for GF, with EDDGF added
	 */
	function register_no_conflict( $registered ) {

		$registered[] = 'edd-gf-admin';

		return $registered;
	}

	/**
	 * Add this plugin to GF logging
	 * @param  array       $logging_supported Existing integrations using GF logging
	 * @return array                         Modified list, including this plugin
	 */
	function enable_gform_logging( $supported_plugins = array() ) {
		$supported_plugins['edd-gf'] = __( 'Gravity Forms Checkout for EDD', 'edd-gf');
	    return $supported_plugins;
	}

	/**
	 * Print an admin notice that Gravity Forms is necessary for this plugin.
	 */
	function gravity_forms_required() {
		global $pagenow;

		// Only show the notice on the Plugins page so it's not annoying.
		if($pagenow !== 'plugins.php') { return; }

		// Gravity Forms is active
		if(defined('RG_CURRENT_PAGE')) { return; }

		if(is_plugin_inactive('gravityforms/gravityforms.php')) {
			$heading = __('Gravity Forms is installed but not active.', 'edd-gf');
			$message = sprintf(__('%sActivate Gravity Forms%s to use the %s plugin.', 'edd-gf'), '<strong><a href="'.wp_nonce_url(admin_url('plugins.php?action=activate&plugin=gravityforms/gravityforms.php'), 'activate-plugin_gravityforms/gravityforms.php').'">', '</a></strong>', KWS_GF_EDD::name );
		} else {
			$heading = sprintf(__('%s requires Gravity Forms.', 'edd-gf'), KWS_GF_EDD::name);
			$message = sprintf(__('%sPurchase Gravity Forms%s', 'edd-gf'), "\n\n".'<a href="http://www.gravityforms.com" class="button button-large button-default">', '</a>');
		}

		printf('<div class="error">%s%s</div>', '<h3>'.$heading.'</h3>', wpautop( $message ));
	}

	/**
	 * Add scripts to the admin.
	 */
	function admin_enqueue_scripts() {

		$min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';

		wp_enqueue_script( 'edd-gf-admin', plugins_url( 'assets/js/admin'.$min.'.js', EDD_GF_PLUGIN_FILE ), array('jquery'), KWS_GF_EDD::version, true);

		wp_localize_script( 'edd-gf-admin', 'EDDGF', array(
			'text_value' => __('Value', 'edd-gf'),
			'text_price_id' => __('EDD Price ID', 'edd-gf')
		));
	}

	/**
	 * Add hooks and license stuff
	 * @todo  Hook up the license functionality
	 */
	function admin_init() {

		add_filter( 'gform_tooltips', array( &$this, 'gf_tooltips' ) );

		add_action( 'gform_field_standard_settings', array( &$this, 'options_field' ), 50, 2);

		add_action( 'gform_field_standard_settings', array( &$this, 'product_field' ), 10, 2);

		add_action("gform_editor_js", array( &$this, 'editor_script' ));

		// check for download price variations via ajax for Options field
		add_action( 'wp_ajax_edd_gf_check_for_variations', array( &$this, 'check_for_variations' ) );
	}

	/**
	 * Get the price variations for an EDD product
	 * @return string JSON-encoded array of price variations
	 */
	public function check_for_variations() {
		if( isset($_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'edd_gf_download_nonce') ) {

			$download_id = absint( $_POST['download_id'] );

			$response = array();

			if($prices = edd_get_variable_prices($download_id)) {
				$response = $prices;
			}

			exit(json_encode($response));
		}
	}

	/**
	 * Modify the number of results fetched by EDD
	 *
	 * This allows all downloads to be shown in the dropdown. Hopefully, not a ton, otherwise it'll get heavy!
	 *
	 * @param  int $per_page Previous results per page setting
	 * @return int           New results per page setting. If you have this many, you're in trouble.
	 */
	function results_per_page($per_page) {
		return PHP_INT_MAX;
	}

	/**
	 * Output binding JS for when GF fields are shown in admin
	 */
	function editor_script(){
	    ?>
	    <script type='text/javascript'>

	        //adding setting to fields of type "product"
	        fieldSettings["product"] += ", .edd_gf_connect_download";

	        //adding setting to fields of type "options"
	        fieldSettings["option"] += ", .edd_gf_connect_variations";

	        //binding to the load field settings event to initialize the checkbox
	        jQuery(document).bind("gform_load_field_settings", function(event, field, form){

	        	/*
	        	console.info('in gform_load_field_settings (event, field, form)');
	        	console.log(event, field, form);
	        	*/

	        	// Set the value of the download ID
	        	// We trigger change to force display of variations message.
	        	jQuery(".edd-gf-download-select").val(field["eddDownload"]).trigger('change');

	        	// Load the EDD messages for price variants
	        	jQuery("#product_field").trigger('show');
	        });
	    </script>
	    <?php
	}

	/**
	 * Add a "Connect to EDD Download" select box to GF product fields
	 * @param  int $position Current position on GF field
	 * @param  int $form_id  The current GF form ID
	 */
	function product_field($position, $form_id) {

		if($position !== 25){ return; }

		// EDD doesn't exist.
		if( !class_exists( 'EDD_API' ) ) {
			return;
		}

		$EDD_API = new EDD_API;

		// Force EDD to show all the downloads at once.
		add_filter('edd_api_results_per_page', array( &$this, 'results_per_page') );

		// Get all the EDD products
		$products = $EDD_API->get_products();

		// Restore sanity to EDD results per page.
		remove_filter('edd_api_results_per_page', array( &$this, 'results_per_page') );

?>
		<li class="edd_gf_connect_download field_setting">

		    <label for="field_admin_label">
		    	<span class="fa fa-lg fa-arrow-circle-o-down"></span>
		        <?php printf(__("Connect to EDD %s", 'edd-gf'), edd_get_label_singular()); ?>
		        <?php gform_tooltip("form_field_edd_download_value") ?>
		    </label>

			<select id="field_edd_download" name="downloads[0][id]" class="edd-gf-download-select">
		<?php

			if( $products ) {
		    	echo '<option value="0">' . sprintf( __('Choose a %s', 'edd-gf'), esc_html( edd_get_label_singular() ) ) . '</option>';

		    	foreach( $products['products'] as $download ) {

		    		if( $download['info']['status'] != 'publish' ) {
		    			$prefix = strtoupper( $download['info']['status'] ) . ' - ';
		    		} else {
		    			$prefix = '';
		    		}

		    		// Does the product have variations?
		    		// This was too complex to verify otherwise, so just using another query.
		    		$has_variations = edd_has_variable_prices( $download['info']['id'] ) ? ' data-variations="1"' : '';

		    		echo '<option value="' . $download['info']['id'] . '"'.$has_variations.'>' . $prefix . esc_html( $download['info']['title'] ) . '</option>';
		    	}

		    } else {
		    	echo '<option value="0">' . sprintf( __('No %s created yet', 'edd-gf'), edd_get_label_plural() ) . '</option>';
		    }
		?>
			</select>

			<span class="howto product-has-variations-message" style="display:none;"><?php esc_html_e('This download has variations.', 'edd-gf'); ?><span class="product-add-option-field" style="display:none;"><?php esc_html_e('You will need to add an "Option" Pricing Field or change the Field Type to "Radio" or "Drop Down" below in order to configure the variation pricing.', 'edd-gf'); ?></span>
			</span>
		</li>
		<?php
	}

	/**
	 * Output field HTML for GF "Product Options" variations
	 * @param  int $position Current position on GF field
	 * @param  int $form_id  The current GF form ID
	 */
	function options_field($position, $form_id) {

		if($position !== 25) { return; }

		$button_text = sprintf(esc_attr__('Load EDD Options &amp; Prices for this Product %s', 'edd-gf'), gform_tooltip("edd_gf_load_variations", '', true));
		$connected_text = esc_attr__('This is an Easy Digital Downloads product', 'edd-gf');
		$connect_variation_help = sprintf(esc_attr__( 'The "Label" below must match the EDD Option Name or the EDD Price ID value. %sLearn more about connecting variable price products%s.', 'edd-gf' ), '<a href="https://katzwebservices.zendesk.com/hc/en-us/articles/201760193-How-to-connect-Gravity-Forms-with-Easy-Digital-Downloads-price-variations" target="_blank">', '</a>');
	?>
		<li class="edd_gf_connect_variations field_setting" style="position:relative;">
			<?php wp_nonce_field( 'edd_gf_download_nonce', 'edd_gf_download_nonce' ); ?>

			<div class="edd-connected"><span class="fa fa-lg fa-arrow-circle-o-down"></span> <?php echo $connected_text; ?></div>

			<div class="edd-gf-no-variations-warning" style="display:none;">
				<p><span class="description"><?php esc_html_e('This EDD product has no price variants; the settings below will not be applied to the EDD download purchase.', 'edd-gf'); ?></span></p>
			</div>
			<div class="edd-gf-get-variations" style="display:none; position:relative;">
				<p><span class="howto"><?php echo $connect_variation_help; ?></span></p>
				<p class="ui-helper-clearfix">
					<button class="button button-small button-default alignleft" type="button"><?php echo $button_text; ?></button>
					<img class="waiting edd-gf-loading alignleft" style="display:none; margin-top: 2px; margin-left:5px;" width="20" height="20" src="<?php echo admin_url('images/spinner.gif'); ?>" />
				</p>
			</div>
		</li>
	<?php
	}

	/**
	 * Add a variations tooltip
	 * @param  array $tooltips Original tooltips array
	 * @return array           Modified array
	 */
	function gf_tooltips($tooltips) {
		$tooltips['edd_gf_load_variations'] = sprintf('<h6>%s</h6> %s', __('Use EDD Settings', 'edd-gf'), __( 'Load the variation names and prices from the Easy Digital Downloads product. You can change the options after loading.', 'edd-gf'));
		$tooltips["form_field_edd_download_value"] = sprintf("<h6>%s</h6>%s", __('EDD Download', 'edd-gf'), __('Connect this product to an Easy Digital Downloads product. If connected, when this product is purchased it will generate a sale in Easy Digital Downloads.', 'edd-gf'));
		return $tooltips;
	}
}

new KWS_GF_EDD_Admin;