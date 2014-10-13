jQuery(document).ready(function($) {

	var EDD_GF_Admin = EDD_GF_Admin || {

		init: function() {

			var self = EDD_GF_Admin;

			$(document).on('gform_load_field_settings', self.hide_connect_for_options );

			/**
			 * Set field values when an EDD product is selected on a GF Product field
			 *
			 * @return {void}
			 */
			$('body').on('change', '#field_edd_download', self.change_download );

			/**
			 * When changing the Product picker in the Option field
			 * @param  {[type]} e [description]
			 * @return {[type]}   [description]
			 */
			$('body').on('show change', '#product_field,#product_field_type', self.maybe_show_variations );

			/**
			 * Triggered when the "Load EDD Options & Prices" button is clicked
			 *
			 * The button is only shown when on a GF Options field that has been connected to an EDD product that has price variables
			 */
			$('body').on('mouseup keyup', '.edd-gf-get-variations button', self.get_variations );

		},

		/**
		 * Use this function to log to console to make it easier to enable/disable logging.
		 * @param  {[type]} content [description]
		 * @param  {[type]} info    [description]
		 * @return {[type]}         [description]
		 */
		log: function( content, info ) {

			info = info || null;

			console.log( content, info );
		},

		/**
		 * Hide EDD connection info for other Product fields like Coupon, Quantity and Total
		 * @return {void}
		 */
		hide_connect_for_options: function() {

			// Get the current field
			var field = GetSelectedField();

			if( field.type !== 'product' && field.type !== 'option' ) {
				EDD_GF_Admin.product_hide_all( field );
			}

		},

		/**
		 * Update GF Choices for the field
		 * @param  {object} field GF Field
		 */
		update_field_choices: function (field) {
			// Tell Gravity Forms to update the choices being displayed
			UpdateFieldChoices(field.type);
			LoadFieldChoices(field);
		},

		get_product_field: function ( field ) {

			// We only care about product fields.
			if( typeof( field.type ) === 'undefined' || ( field.type !== 'product' && field.type !== 'option' )  ) {

				EDD_GF_Admin.log( 'not a product field', field);

				return false;
			}

			var eddProductField = false;

			// The current field is a Product field, not an Options field
			if( typeof( field.productField ) === 'undefined' || field.productField === '' ) {

				// The current field _is_ the parent field
				eddProductField = field;

			}

			// This is a child Options field, not a Product with a Field Type setting
			else {

				// What product field was chosen to be used?
				eddProductField = GetFieldById( field.productField );
			}

			return eddProductField;
		},

		get_variations: function( e ) {

			var self = EDD_GF_Admin;

			// Support keyboard triggers as well as mouse input.
			if( e.type === 'keyup' ) {

				// Not space bar and not return
				if ( e.keyCode !== 32 && e.keyCode !== 13 ) {

					self.log( 'keyCode not space or return.', e.keyCode );

					return;
				}
			}

			// Get the current field
			var field = GetSelectedField();

			// We only care about product fields.
			var eddProductField = self.get_product_field( field );

			// The ID of the download
			var edd_gf_selected_download = eddProductField ? eddProductField.eddDownload : '';

			$('.edd-gf-loading').show();

			self.log('in edd-gf-get-variations', {
				'field': field,
				'edd_gf_selected_download': edd_gf_selected_download,
				'eddProductField': eddProductField
			});

			// Get the variations for the download
			$.post( ajaxurl, {
				action: 'edd_gf_check_for_variations',
				download_id: edd_gf_selected_download,
				nonce: $('#edd_gf_download_nonce').val(),
				format: "json"
			})
			.done(function( data ) {

				self.log( 'Check For Variations Data', data );

				// The loading is done
				$('.edd-gf-loading').hide();

				// The ajax call gave us a JSON array of price variables
				items = jQuery.parseJSON(data);

				self.log( 'parsed JSON price Variable Items', items );

				// If there were no variables, revert back to the original field choices
				if( items.length === 0 ) {

					self.log('There were no variations. Reverting to original field choices.');

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
					self.update_field_choices(field);

				} // End if variables
			})
			.error(function(data) {
				$('.edd-gf-loading').hide();
			});

		},

		maybe_show_variations: function (e) {
			var self = EDD_GF_Admin;

			// Get the current field
			var field = GetSelectedField();

			var eddProductField = self.get_product_field( field );

			// The ID of the download
			var edd_gf_selected_download = eddProductField ? eddProductField.eddDownload : '';

			// Set the default text for the Choices header
			$('#gfield_settings_choices_container .gfield_choice_header_value').text(EDDGF.text_value);

			self.log( '#' + $(e.target).attr('id') + ' ' + e.type , {
				'event': e,
				'field': field,
				'edd_gf_selected_download': edd_gf_selected_download,
				'eddProductField': eddProductField
			});

			// There's no connected EDD download
			if( edd_gf_selected_download === '' || edd_gf_selected_download === '0' ) {

				$('.product-has-variations-message').hide();
				$('.edd_gf_connect_variations').slideUp(100);

				// Restore the field choices
				self.restore_field_choices( field );
			}

			// If the download isn't empty and it has variables.
			else {

				// Options field
				if( field.productField !== '' ) {

					// No parent
					SetFieldProperty('eddVariationParentID', false);

				}
				// Product field, not Options field
				else {

					// Parent is self (Product Field)
					SetFieldProperty('eddVariationParentID', edd_gf_selected_download);
				}

				if( eddProductField.eddHasVariables ) {
					self.product_has_variables( field );
				}
				// There's a download, but has no variable products
				else {
					self.product_has_no_variables( field );
				}

			}
		},

		/**
		 * Show/hide items when the selected Download has variations
		 * @param  {object} field GF Field Object
		 * @return {void}
		 */
		product_has_variables: function( field ) {

			EDD_GF_Admin.log( 'product_has_variables' );

			$('.edd-gf-get-variations').show();
			$('.product-has-variations-message').show();
			$('.edd-gf-no-variations-warning').hide();
			$('.product-add-option-field').hide();

			field.productField = field.productField || '';

			// Product Field
			if( field.productField === '' ) {

				// If the Field Type is not Drop Down or Radio, hide "Load EDD Options"
				if( field.inputType !== 'radio' && field.inputType !== 'select' ) {

					$('.edd_gf_connect_variations').hide();

					// Show the message that the product has variations and an options field is required.
					$('.product-add-option-field').show();

					return;
				}
			}

			// Change the header to "EDD Price ID"
			$('#gfield_settings_choices_container .gfield_choice_header_value').text(EDDGF.text_price_id);

			$('.edd_gf_connect_variations:hidden').slideDown('fast');

		},

		/**
		 * Show/hide items when the selected Download has no variations
		 * @param  {object} field GF Field Object
		 * @return {void}
		 */
		product_has_no_variables: function( field ) {

			EDD_GF_Admin.log( 'product_has_no_variables' );

			$('.edd-gf-no-variations-warning').hide();
			$('.product-has-variations-message').hide();
			$('.edd-gf-get-variations').hide();

			// If this not the Product field, show the connected text
			if( field.productField !== '' ) {

				$('.edd-gf-no-variations-warning').show();
				$('.edd_gf_connect_variations:hidden').slideDown('fast');

			} else {

				// If we're on the Product field, show the No Variations
				// warning if the Field Type is a variations type
				if( field.inputType === 'radio' || field.inputType === 'select' ) {
					$('.edd-gf-no-variations-warning').show();
					$('.edd_gf_connect_variations:hidden').slideDown('fast');
				} else {

					$('.edd_gf_connect_variations').hide();
				}

			}

			// Restore the field choices
			EDD_GF_Admin.restore_field_choices(field);

		},

		/**
		 * Restore GF Choices to either a backup of previous choices or default choices
		 * @param  {object} field GF Field
		 * @uses EDD_GF_Admin.update_field_choices()
		 */
		restore_field_choices: function( field ) {

			EDD_GF_Admin.log('In edd_gf_restore_field_choices');

			if(field.eddChoicesBackup) {

				field.choices = field.eddChoicesBackup;

			} else if ( typeof( field.choices ) === 'undefined' ) {

				// Default field choices (from GF js.php)
				field.choices = [
					new Choice("First Option", "", "0.00"),
					new Choice("Second Option", "", "0.00"),
					new Choice("Third Option", "", "0.00")
				];

			}

			EDD_GF_Admin.update_field_choices(field);

			EDD_GF_Admin.log('field', field);
		},

		/**
		 * Set field values when an EDD product is selected on a GF Product field
		 *
		 * Sets the `eddDownload` to the selected download and `eddHasVariables` to boolean
		 *
		 * @return {void}
		 */
		change_download: function( e ) {

			var self = EDD_GF_Admin;

			// Get the current field
			var field = GetSelectedField();

			var selected_download = $('option:selected', this).val();

			// Tell Gravity Forms to update the field value.
			SetFieldProperty('eddDownload', selected_download );

			// Default to no variables
			SetFieldProperty('eddHasVariables', false);

			// If the option has variations set, show the variations message
			if( $.isNumeric( selected_download ) && parseInt( selected_download, 10 ) !== 0 ) {

				if( $('option:selected', $(this)).attr('data-variations') ) {

					SetFieldProperty('eddHasVariables', true);

				}
			}

			self.maybe_show_variations( e );

		}

	}

	EDD_GF_Admin.init();

});