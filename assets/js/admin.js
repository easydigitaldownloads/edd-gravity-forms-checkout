jQuery(document).ready(function($) {

	/**
	 * Set field values when an EDD product is selected on a GF Product field
	 *
	 * @return {[type]} [description]
	 */
	$('body').on('change', '.edd-gf-download-select', function() {

		var selected_download = $('option:selected', this).val();

		// Tell Gravity Forms to update the field value.
		SetFieldProperty('eddDownload', selected_download);

		// If the option has variations set, show the variations message
		if( $.isNumeric( selected_download ) && parseInt( selected_download, 10 ) !== 0 && $('option:selected', $(this)).attr('data-variations')) {
			SetFieldProperty('eddHasVariables', true); // And update the field
			$('.product-has-variations-message').show();
		} else {
			SetFieldProperty('eddHasVariables', false);
			$('.product-has-variations-message').hide();
		}

	});

	/**
	 * Update GF Choices for the field
	 * @param  {object} field GF Field
	 */
	function edd_gf_update_field_choices(field) {
		// Tell Gravity Forms to update the choices being displayed
		UpdateFieldChoices(field.type);
		LoadFieldChoices(field);
	}

	/**
	 * Restore GF Choices to either a backup of previous choices or default choices
	 * @param  {object} field GF Field
	 * @uses edd_gf_update_field_choices()
	 */
	function edd_gf_restore_field_choices(field) {

		// console.info('In edd_gf_restore_field_choices');

		if(field.eddChoicesBackup) {

			field.choices = field.eddChoicesBackup;

		} else if ( typeof( field.choices ) === 'undefined' ) {
			// Default field choices (from GF js.php)
			field.choices = new Array(new Choice("First Option", "", "0.00"), new Choice("Second Option", "", "0.00"), new Choice("Third Option", "", "0.00"));
		}

		edd_gf_update_field_choices(field);

		// console.info('field', field);
	}

	$('body').on('show change', '#product_field', function(e) {

		// Get the current field
		var field = GetSelectedField();

		// We only care about product fields.
		if( typeof( field.productField ) === 'undefined' || field.productField === '' ) { return; }

		// What product field was chosen to be used?
		var eddParentField = GetFieldById(field.productField);

		// The ID of the download
		var edd_gf_selected_download = eddParentField ? eddParentField.eddDownload : '';

		// Set the default text for the Choices header
		$('#gfield_settings_choices_container .gfield_choice_header_value').text(EDDGF.text_value);


		/*
		console.info('#product_field change');
		console.log('event', e);
		console.log('field', field);
		console.log('edd_gf_selected_download', edd_gf_selected_download);
		console.log('eddParentField', eddParentField);
		*/

		// There's no connected EDD download
		if( typeof( edd_gf_selected_download ) === 'undefined' || edd_gf_selected_download === '' ) {

			// No connected download
			SetFieldProperty('eddVariationParentID', edd_gf_selected_download);

			$('.edd_gf_connect_variations').slideUp(100);

			// Restore the field choices
			edd_gf_restore_field_choices(field);
		}
		// Quantity field
		else if( field['type'] === 'quantity' ) {

			// No parent
			SetFieldProperty('eddVariationParentID', edd_gf_selected_download);

		}
		// There's a download, but has no variable products
		else if(!eddParentField.eddHasVariables) {

			// No parent
			SetFieldProperty('eddVariationParentID', false);

			$('.edd-gf-get-variations').hide();
			$('.edd-connected,.edd-gf-variation-warning').show();
			$('.edd_gf_connect_variations:hidden').slideDown('fast');

			// Restore the field choices
			edd_gf_restore_field_choices(field);

		}
		// If the download isn't empty and it has variables.
		else {

			// Change the header to "EDD Price ID"
			$('#gfield_settings_choices_container .gfield_choice_header_value').text(EDDGF.text_price_id);

			// No parent
			SetFieldProperty('eddVariationParentID', edd_gf_selected_download);

			$('.edd-gf-variation-warning').hide();
			$('.edd-connected,.edd-gf-get-variations').show();

			$('.edd_gf_connect_variations:hidden').slideDown('fast');
		}
	});

	/**
	 * Triggered when the "Load EDD Options & Prices" button is clicked
	 *
	 * The button is only shown when on a GF Options field that has been connected to an EDD product that has price variables
	 */
	$('body').on('edd-gf-get-variations', edd_gf_get_variations );

	function edd_gf_get_variations() {

		// Get the current field
		var field = GetSelectedField();

		// We only care about product fields.
		if( typeof( field.productField ) === 'undefined' || field.productField === '' ) { return; }

		// What product field was chosen to be used?
		var eddParentField = GetFieldById(field.productField);

		// The ID of the download
		var edd_gf_selected_download = eddParentField ? eddParentField.eddDownload : '';

		$('.edd-gf-loading').show();

		/*
		console.info('in edd-gf-get-variations');
		console.log('field', field);
		console.log('edd_gf_selected_download', edd_gf_selected_download);
		console.log('eddParentField', eddParentField);
		*/

		// Get the variations for the download
		$.post( ajaxurl, {
			action: 'edd_gf_check_for_variations',
			download_id: edd_gf_selected_download,
			nonce: $('#edd_gf_download_nonce').val(),
			format: "json"
		})
		.done(function( data ) {

			// The loading is done
			$('.edd-gf-loading').hide();

			// The ajax call gave us a JSON array of price variables
			items = jQuery.parseJSON(data);

			// If there were no variables, revert back to the original field choices
			if(!items.length) {
				edd_gf_restore_field_choices(field);
			} else {
				// Store the original choices
				field.eddChoicesBackup = field.choices;

				// We add the Object choices in the list to the field choices.
				field.choices = [];

				// Be able to set the value of the price variables
				$('#field_choice_values_enabled').prop("checked", true);
				SetFieldProperty('enableChoiceValue', true); ToggleChoiceValue(); SetFieldChoices();

				// For each price variation
				$.each( items, function( i, item ) {

					// Convert the price to Gravity Forms style
					var currency = GetCurrentCurrency();
					var price = currency.toMoney(item.amount);

					// Create a choice based on the variation
					choice = new Choice();
						choice.text = item.name;
						choice.value = i.toString(); // It needs to be a string so GF can do `choiceValue.replace(/'/g, "&#039;")` on it
						choice.price = price;
						choice.isSelected = false; // previous: (i === 0); // Select the first variation as the default

					// Add the choice to the list of choices available
					field.choices.push(choice);
				});

				// Update the field choices
				edd_gf_update_field_choices(field);

			} // End if variables
		})
		.error(function(data) {
			$('.edd-gf-loading').hide();
		});

	}

});