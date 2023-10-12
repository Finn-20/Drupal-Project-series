jQuery(document).ready(function(){
	// checking submit of usecase_accelators.
	jQuery(document).off('click', '.contributesave').on('click', '.contributesave', function (){
		var option_val = jQuery(this).val();
		if(option_val == 'Save and Un-archive'){
			return confirm('ALERT! You are about to Re-Publish the archived asset. Please confirm, If you still wish to proceed?');
		}
		if(option_val == 'Save and Archive'){
			return confirm('ALERT! You are about to archive your asset, please note this action will disable your asset for any editing and will not be directly visible to Data and AI Gallery users. Please confirm, If you still wish to proceed?');
		}
	});
	jQuery('.linkedin_url_field').each(function(){
	jQuery("#edit-field-linked-url-0-uri").tooltip('hide').attr('data-original-title', 'Enter Linkedin Profile URL').tooltip('show');
		jQuery("#edit-field-linkedin-sec-url-0-uri").tooltip('hide').attr('data-original-title', 'Enter Linkedin Profile URL').tooltip('show');
	});


	jQuery('.contact_phone_field').each(function(){
		var contact_primary_sec_no = document.querySelector('#'+jQuery(this).attr('id'));
		var iti = window.intlTelInput(contact_primary_sec_no, {
		hiddenInput: "full_number",
			preferredCountries: ['fr','gb','us','in'],
			separateDialCode: true,
			//utilsScript: "../build/js/utils.js",
		});
		window.intlTelInputGlobals.loadUtils("/utils.js");
		iti.setNumber(jQuery(this).val());
	});

	/****** Helper Functions ******/
	function show_hide_action_buttons(index_counter) {
	  jQuery('div.messages__wrapper').html('');
	   //Delete Button should display on last
	   var total_tab_count = jQuery('div.field-group-tabs-wrapper ul.vertical-tabs-list li.vertical-tab-button').length;

	   var tab_title = jQuery('div.field-group-tabs-wrapper ul.vertical-tabs-list li.vertical-tab-button.selected a span:first-child').text();
	   if(tab_title == "Overview"){
		   var new_title = "Overview";
		   jQuery('.titles span').html('<span class="overview">'+new_title+'</span>');
	   }
	    if(tab_title == "General Details"){
		   var new_title = "Description";
		   jQuery('.titles span').html('<span class="description">'+new_title+'</span>');
	    }
	   if(tab_title == "Main Selections"){
		   var new_title = "Industry & Playing Fields";
		   jQuery('.titles span').html('<span class="sector">'+new_title+'</span>');
	   }
	   if(tab_title == "Required Categories") {
		   var new_title = "Categorization";
		   jQuery('.titles span').html('<span class="category">'+new_title+'</span>');
	   }
	   if(tab_title == "Demonstration") {
		   var new_title = "Demonstration";
		   jQuery('.titles span').html('<span class="demo">'+new_title+'</span>');
	   }
	   if(tab_title == "Owners & Contributors") {
		   var new_title = "Owner Details";
		   jQuery('.titles span').html('<span class="owner">'+new_title+'</span>');
	   }
	   if(tab_title == "Case Study") {
		   var new_title = "Case Study";
		   jQuery('.titles span').html('<span class="case_study">'+new_title+'</span>');
	   }
	   if(tab_title == "Optional Selections") {
		   var new_title = "Industry, Playing Fields & Offers";
		   jQuery('.titles span').html('<span class="optional">'+new_title+'</span>');
	   }
	   if(tab_title == "Use case Attachments") {
		   var new_title = "Collaterals";
		   jQuery('.titles span').html('<span class="collatral">'+new_title+'</span>');
	   }
	   if(tab_title == "Admin Section") {
		   var new_title = "Admin Section";
		   jQuery('.titles span').html('<span class="admin">'+new_title+'</span>');
	   }


	   jQuery('div.vtab-title-custom').html('<div class="tab-title-wrapper"><span class="tab-title">' + tab_title + '</span><span class="user-guide-link"><a data-target-help-section = "' + tab_title.toLowerCase().replace(' ', '_') + '" title="'+tab_title+'" class="link_usecase_guide" target="_blank"><i class="fas fa-info"></i></a></span><span class="skip-button"><a class="skip-button-text">Skip to last step</a></span>');

	   jQuery('a.link_usecase_guide').click(function(){
		var datahelp = jQuery(this).attr('data-target-help-section');
		var url = "/usecase-accelerator-upload-guide#"+datahelp;
		 window.open(url, '_blank');
		});


	   if (jQuery('li.tab-id-' + index_counter + ' a').hasClass('form-fields-required') || (index_counter == total_tab_count) || (index_counter ==1)) {
		   jQuery('span.skip-button').hide();
	   }
	   else {
		   jQuery('span.skip-button').show();
	   }
	   /*  */
	   jQuery('a.skip-button-text').click(function(){

			var next_prev_counter = 1;
			var data_selected_tab = jQuery('a.change-tabs').attr('data-selected-tab');

			var current_tab_class = 'tab-id-' + data_selected_tab;

			var next_tab_val = 'tab-id-' +total_tab_count;

			var next_tab_class = 'tab-id-' + next_tab_val;
			var next_last_child_class = 'tab-id-'+ (total_tab_count-1);
			/*** Change Upper Tab ***/
			jQuery('li.' + current_tab_class).removeClass('active');
			jQuery('li.' + current_tab_class).removeClass('selected');

			if (!jQuery('li.' + current_tab_class).hasClass('visited')) {
			jQuery('li.' + current_tab_class).addClass('visited')
			}

			jQuery('li.'+ next_last_child_class).addClass('visited');


			if (!jQuery('li.tab-id-' + total_tab_count).hasClass('visited')) {
			jQuery('li.tab-id-' + total_tab_count).addClass('active')
			}
			jQuery('a.change-tabs').attr('data-selected-tab', total_tab_count);
			jQuery('li.tab-id-' + total_tab_count).children('a').click();
        });

		if(index_counter == 1){
			jQuery('.form-item.js-form-item.form-type-select.js-form-type-select.form-item-select-preview.js-form-item-select-preview.form-no-label.form-group').removeClass('showPreviewButton');
		}
	   if(index_counter == 2){
		 jQuery('#edit-actions').show();
		 jQuery('#edit-actions .dropdown').hide();
		 jQuery('#edit-actions #edit-delete').hide();
		 jQuery('#edit-actions #edit-draft').show();

	   }
	   if (index_counter > 1) {

		 jQuery('div.prev-button').show();
		 jQuery('.form-item.js-form-item.form-type-select.js-form-type-select.form-item-select-preview.js-form-item-select-preview.form-no-label.form-group').removeClass('showPreviewButton');
		}
	   else {
	     jQuery('#edit-actions').hide();
		 jQuery('#edit-actions .dropdown').hide();
		 jQuery('#edit-actions #edit-delete').hide();
		 jQuery('#edit-actions #edit-draft').hide();
		 jQuery('div.prev-button').hide();
		 jQuery('.form-item.js-form-item.form-type-select.js-form-type-select.form-item-select-preview.js-form-item-select-preview.form-no-label.form-group').removeClass('showPreviewButton');
		}

	   if (index_counter == total_tab_count) {
		 jQuery('#edit-actions').show();
		  jQuery('#edit-actions .dropdown').show();
		 jQuery('#edit-actions #edit-delete').show();
		 jQuery('#edit-actions #edit-draft').show();
		 jQuery('.form-type-vertical-tabs').show();
		 jQuery('div.next-button').hide();
		 jQuery('.form-item.js-form-item.form-type-select.js-form-type-select.form-item-select-preview.js-form-item-select-preview.form-no-label.form-group').addClass('showPreviewButton');
		}

	   else {
			
			 jQuery('#edit-actions').show();
			 jQuery('.form-type-vertical-tabs').hide();
			 jQuery('div.next-button').show();
			 jQuery('#edit-actions .dropdown').hide();
			 jQuery('#edit-actions #edit-delete').hide();
			 jQuery('.form-item.js-form-item.form-type-select.js-form-type-select.form-item-select-preview.js-form-item-select-preview.form-no-label.form-group').removeClass('showPreviewButton');

		}
	}

	function checkIfFormValidated(isValidationRequired) {
		if (!isValidationRequired)
			return 'pass';

		var isValidated = true;
		var error_msgs = '';
		var pane_id = jQuery('li.last-selected-tab a').attr('data-target-id');
	
		jQuery(pane_id + ' input.required').each(function(){
			if (jQuery(this).val() === '') {
				if(!jQuery(this).hasClass('error')) {
				  jQuery(this).addClass('error');
				}

				if (!jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').hasClass('error')) {
					jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').addClass('error');
			    }
			    if (!jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').hasClass('has-error')) {
			    	jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').addClass('has-error');
			    }

				var label_text = jQuery(this).siblings('label').text();
				if (label_text === undefined || label_text === '') {
					label_text = 'Other';
					if (!jQuery(this).parent('.form-type-textfield').hasClass('has-error')) {
						jQuery(this).parent('.form-type-textfield').addClass('has-error');
					}
					if (!jQuery(this).parent('.form-type-textfield').hasClass('error')) {
						jQuery(this).parent('.form-type-textfield').addClass('error');
					}
				}
				error_msgs = error_msgs + '<li class="item item--message">' + label_text +' field is required.</li>';
				isValidated = false;
			}
			else {
				if(jQuery(this).hasClass('error')) {
				  jQuery(this).removeClass('error');
				}

				if (jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').hasClass('error')) {
					jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').removeClass('error');
			    }
			    if (jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').hasClass('has-error')) {
			    	jQuery(this).parent('div.form-type-textfield').parent('.form-wrapper').removeClass('has-error');
			    }

			    var label_text = jQuery(this).siblings('label').text();
				if (label_text === undefined || label_text === '') {
					if (jQuery(this).parent('.form-type-textfield').hasClass('has-error')) {
						jQuery(this).parent('.form-type-textfield').removeClass('has-error');
					}
					if (jQuery(this).parent('.form-type-textfield').hasClass('error')) {
						jQuery(this).parent('.form-type-textfield').removeClass('error');
					}
				}
			}
		});

		jQuery(pane_id + ' textarea.required').each(function(){
			if (jQuery(this).val() === '') {
				if(!jQuery(this).hasClass('error')) {
				  jQuery(this).addClass('error');
				}
				error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).parent('div.form-textarea-wrapper').siblings('label.form-required').text() +' field is required.</li>';
				isValidated = false;

		}
		});

		//File validation
		jQuery(pane_id + ' input[type="file"]').each(function(){
            if(jQuery('input[name="files[field_associated_image_0]"]').length){
			if(jQuery('input[name="files[field_associated_image_0]"]').get(0).files.length === 0) {
				if(!jQuery(this).hasClass('error')) {
					jQuery(this).addClass('error');
				}
				if (!jQuery('div.form-item-field-associated-image-0').hasClass('error')) {
					jQuery('div.form-item-field-associated-image-0').addClass('error');
				}
				if (!jQuery('div.form-item-field-associated-image-0').hasClass('has-error')) {
					jQuery('div.form-item-field-associated-image-0').addClass('has-error');
				}

				error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).parent('div.form-managed-file').siblings('label.form-required').text() +' field is required.</li>';
				isValidated = false;
			}
			else {
				if(jQuery(this).hasClass('error')) {
				  jQuery(this).removeClass('error');
				}
			    if (jQuery('div.form-item-field-associated-image-0').hasClass('error')) {
			    	jQuery('div.form-item-field-associated-image-0').removeClass('error');
			    }
			    if (jQuery('div.form-item-field-associated-image-0').hasClass('has-error')) {
			    	jQuery('div.form-item-field-associated-image-0').removeClass('has-error');
			    }
			}
                     }
		});


		//Select box validation
		var isAdded = [];
		jQuery(pane_id + ' select.required').each(function(){
			if (jQuery(this).val() === '_none') {
				if(!jQuery(this).hasClass('error')) {
				  jQuery(this).addClass('error');
				}
				var select_label = jQuery(this).closest('div.select-wrapper').siblings('label.form-required').text();
				if(isAdded.indexOf(select_label) === -1){
					error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).closest('div.select-wrapper').siblings('label.form-required').text() +' field is required.</li>';
				   isAdded.push(select_label);
				}

				isValidated = false;
			}
		});

        	//Email field validation
                 var email_message = jQuery('input[name=owner_contributor_email_message]').val();
		 jQuery(pane_id + ' input.email_field').each(function(){
		  var emailReg = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@(capgemini.com|sogeti.com)$/;
		   var email_val = jQuery(this).val();
		   if(!emailReg.test(email_val)){
			   if(!jQuery(this).hasClass('error')) {
				  jQuery(this).addClass('error');
				}
				error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).siblings('label').text() +' field  - ' + email_message + '</li>';
				isValidated = false;
			}
		});

		jQuery(pane_id + ' input.email_contact_field').each(function(){
			let emailReg = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			let email_val = jQuery(this).val();

			if(jQuery('#edit-field-do-you-want-to-add-case-st-yes').prop('checked')) {
				if(!emailReg.test(email_val)){
					if(!jQuery(this).hasClass('error')) {
					jQuery(this).addClass('error');
					jQuery(this).addClass('ttt');
					}
					error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).siblings('label').text() +' field  - enter valid email id.</li>';
					isValidated = false;
				}
			}
		 });

		//Phone number validation
		var contact_validation = [];
		jQuery(pane_id + ' input.contact_phone_field').each(function(){

			var input = document.querySelector('#'+jQuery(this).attr('id'));
            var iti = window.intlTelInputGlobals.getInstance(input);
			var number = iti.getNumber(intlTelInputUtils.numberFormat.E164);
			if(jQuery(this).attr('id') == 'edit-field-usecase-primary-ownet-cnt-0-value'){
				var label = 'Primary Contact';
			}else{
				var label = 'Secondary Contact';
			}

			if (number.trim()!= '') {
				if (iti.isValidNumber()) {
				  contact_validation.push('trueval');
				  // isValidated = true;
				} else {

				  var errorCode = iti.getValidationError();
				  contact_validation.push('falseval');
				  error_msgs = error_msgs + '<li class="item item--message">' + label +' field - enter valid contact number </li>';
				  // isValidated = false;
				}

			}
			if(number.trim() == ''){
				  var errorCode = iti.getValidationError();
				  contact_validation.push('falseval');
				  error_msgs = error_msgs + '<li class="item item--message">' + label +' field - enter valid contact number </li>';
				  // isValidated = false;
		   }
			// console.log(jQuery.inArray('falseval', contact_validation));
			if(jQuery.inArray('falseval', contact_validation) > -1) {
				isValidated = false;
			} /*
			else {
				isValidated = true;
			}*/


		});

		linkedin_validation= [];
		jQuery(pane_id + ' input.linkedin_url_field').each(function(){
			var linkedinurl = jQuery(this).val();
			if(linkedinurl != ''){
var linkedin_val = /(ftp|http|https):\/\/?(?:www\.)?linkedin.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
				if(!linkedin_val.test(linkedinurl) ) {

					linkedin_validation.push('falseval');
					error_msgs = error_msgs + '<li class="item item--message">' + 'Linkedin url' +' field - enter valid Linkedin Profile URL</li>';
					if(jQuery.inArray('falseval', linkedin_validation) > -1) {
						if(!jQuery(this).hasClass('error')) {
						jQuery(this).addClass('error');
						}
						if (!jQuery('div.form-type-url').hasClass('error')) {
						jQuery('div.form-type-url').addClass('error');
						}
						if (!jQuery('div.form-type-url').hasClass('has-error')) {
						jQuery('div.form-type-url').addClass('has-error');
						}
						isValidated = false;
					} else {
						isValidated = true;
					}
				}
			}
		});



		//contact end
		 // Required Checkboxes Validation
		jQuery(pane_id + ' div.field--widget-options-buttons fieldset.form-composite.required').each(function(){
		  if (jQuery(this).find('input.form-checkbox').length > 0) {
			  isEmpty = true;
			  jQuery(this).find('input.form-checkbox:checked').each(function(){
				  isEmpty = false;
			  });
			  if (isEmpty) {
				  error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).find('span.form-required').text() +' field is required.</li>';
				  isValidated = false;
			  }
		  }

		});

		jQuery(pane_id + ' div.field--widget-options-buttons fieldset.form-composite.required').each(function(){
			if (jQuery(this).find('input.form-radio').length > 0) {
				isEmpty = true;
				jQuery(this).find('input.form-radio:checked').each(function(){
					isEmpty = false;
				});
				if (isEmpty) {
					error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).find('span.form-required').text() +' field is required.</li>';
					isValidated = false;
				}
			}
		  });
		// Required Checkboxes Validation
		jQuery(pane_id + ' div.field--widget-select-or-other-reference').each(function(){
		  if (jQuery(this).find('label.form-required').length > 0) {
			  if(jQuery(this).find('input.form-checkbox').length > 0) {
				isEmpty = true;
			    jQuery(this).find('input.form-checkbox:checked').each(function(){
			      isEmpty = false;
			    });
			  }
			  else if (jQuery(this).find('input.form-radio').length > 0) {
			    isEmpty = false;
			    if (jQuery(this).find("input[name='field_usecase_framework[select]']:checked").val() == undefined || jQuery(this).find("input[name='field_usecase_framework[select]']:checked").val() == '') {
				  isEmpty = true;
			    }
			  }

			  if (isEmpty) {
				  error_msgs = error_msgs + '<li class="item item--message">' + jQuery(this).find('label.form-required').text() +' field is required.</li>';
				  isValidated = false;
			  }
			}
		});
		if (isValidated) {
			if (jQuery('li.last-selected-tab').addClass('validation-failed')) {
				jQuery('li.last-selected-tab').removeClass('validation-failed');
			}
			if (!jQuery('li.last-selected-tab').hasClass('visited')) {
				jQuery('li.last-selected-tab').addClass('visited')
			}
			return 'pass';
		}
		else {
			if (!jQuery('li.last-selected-tab').addClass('validation-failed')) {
				jQuery('li.last-selected-tab').addClass('validation-failed');
			}
			return error_msgs;
		}
	}

	/** parent,child selection change the parent class **/

	function processallchildboxescheckes(checkboxid) {
		var kids_checked = 0;
		var count = 0;
		jQuery("[id^="+checkboxid+"-children]").each(function(){
			if (jQuery(this).prop('checked') == 0){
				// all_kids_checked = 1;
				// return all_kids_checked;
			} else {
				kids_checked++;
			}
			count++;
		});

		var all_checked = 0;
		if(count == kids_checked) {
			var all_checked = 1;
		} else if (count > kids_checked && kids_checked != 0){
			var all_checked = 2;
		}

	  return all_checked;
	}
	jQuery('.select-parents input[type=checkbox]').click(function(){
		var selected_id = jQuery(this).prop('id');
		var broken_data = selected_id.split("-children");
		if(broken_data.length > 1 ){
			if (processallchildboxescheckes(broken_data[0]) == 2) {
				jQuery('#'+broken_data[0]).addClass('checkbox-grayed');
			}
			else if (processallchildboxescheckes(broken_data[0]) == 1) {
				jQuery('#'+broken_data[0]).removeClass('checkbox-grayed');
			}
			else if (processallchildboxescheckes(broken_data[0]) == 0) {
				jQuery('#'+broken_data[0]).removeClass('checkbox-grayed');
				jQuery('#'+broken_data[0]).prop("checked", false);
			}
		}
	});

	/*** Toggle Demo Link field mandatory value based on value provided ***/
	function demo_link_toggle_mandatory_requirement(isDemo) {

	  if (isDemo === 'yes') {
	    if (!jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').hasClass('required')) {
	      jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').addClass('required');
	    }
		if (!jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').hasClass('required')) {
	      jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').addClass('required');
	    }
	    if (!jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').hasClass('js-form-required')) {
	      jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').addClass('js-form-required');
	    }
		if (!jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').hasClass('js-form-required')) {
	      jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').addClass('js-form-required');
	    }
	    if (!jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').hasClass('form-required')) {
	      jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').addClass('form-required');
	    }
		if (!jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').hasClass('form-required')) {
	      jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').addClass('form-required');
	    }
	  }
	  else {
	    if (jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').hasClass('required')) {
	      jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').removeClass('required');
	    }
	    if (jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').hasClass('js-form-required')) {
		  jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').removeClass('js-form-required');
	    }
	    if (jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').hasClass('form-required')) {
	      jQuery('#edit-field-demonstration-details-0-subform-field-demo-link-0-value').siblings('label').removeClass('form-required');
	    }

		 if (jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').hasClass('required')) {
	      jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').removeClass('required');
	    }
		if (jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').hasClass('js-form-required')) {
		  jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').removeClass('js-form-required');
	    }
		 if (jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').hasClass('form-required')) {
	      jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').siblings('label').removeClass('form-required');
		}
		jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').parent('div.form-type-textfield').parent('.form-wrapper').removeClass('has-error');
		jQuery('#edit-field-demonstration-details-0-inline-entity-form-field-demo-link-0-value').parent('div.form-type-textfield').parent('.form-wrapper').removeClass('error');
		
		jQuery('.field--name-field-demo-link input').removeClass('required error');
		jQuery('#edit-field-live-demo-env-check-no').prop('checked', true);
		let external = 'No';
		externalDemoLinkMandatory(external);
	}
}

	function externalDemoLinkMandatory(isExternalDemoLink) {

		if (isExternalDemoLink === 'Yes') {
			jQuery('.field--name-field-demo-link-modified label').addClass('js-form-required form-required');
			jQuery('.field--name-field-demo-link-modified input').addClass('required');
		}
		else {
			jQuery('.field--name-field-demo-link-modified label').removeClass('js-form-required form-required');
			jQuery('.field--name-field-demo-link-modified input').removeClass('required error');
			jQuery('#edit-field-demo-details-modified-0-inline-entity-form-field-demo-link-modified-0-value').parent('div.form-type-textfield').parent('.form-wrapper').removeClass('has-error error');

		}
	}


	function internalDemoVideoMandatory(isDemoVideo) {

		

		if (isDemoVideo === 'yes') {

			if(jQuery('#field_demo_video_link-media-library-wrapper input').val() !== '') {
				jQuery('#field_demo_video_link-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
				jQuery('#field_demo_video_link-media-library-wrapper input').removeClass('required error');
			}
			else {
				jQuery('#field_demo_video_link-media-library-wrapper .panel-title').addClass('js-form-required form-required');
				jQuery('#field_demo_video_link-media-library-wrapper input').addClass('required');
			}
		}
		else {
			jQuery('#field_demo_video_link-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
			jQuery('#field_demo_video_link-media-library-wrapper input').removeClass('required error');
			jQuery('#edit-field-demo-video-check-no').prop('checked', true);
			let external = 'No';
			externalDemoVideoMandatory(external);
		}
	}

	function externalDemoVideoMandatory(isDemoVideo) {

		if (isDemoVideo === 'Yes') {

			if(jQuery('#field_demo_video_modified_-media-library-wrapper input').val() !== '') {
				jQuery('#field_demo_video_modified_-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
				jQuery('#field_demo_video_modified_-media-library-wrapper input').removeClass('required error');
			}
			else {
				jQuery('#field_demo_video_modified_-media-library-wrapper .panel-title').addClass('js-form-required form-required');
				jQuery('#field_demo_video_modified_-media-library-wrapper input').addClass('required');
			}
		}
		else {
			jQuery('#field_demo_video_modified_-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
			jQuery('#field_demo_video_modified_-media-library-wrapper input').removeClass('required error');
		}
	}

	function internalUsecaseVideoMandatory(isUsecaseVideo) {




		if (isUsecaseVideo === 'yes') {

			if(jQuery('#field_usecase_video_link-media-library-wrapper input').val() !== '') {
				jQuery('#field_usecase_video_link-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
				jQuery('#field_usecase_video_link-media-library-wrapper input').removeClass('required error');
			}
			else {
				jQuery('#field_usecase_video_link-media-library-wrapper .panel-title').addClass('js-form-required form-required');
				jQuery('#field_usecase_video_link-media-library-wrapper input').addClass('required');
			}
		}
		else {
			jQuery('#field_usecase_video_link-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
			jQuery('#field_usecase_video_link-media-library-wrapper input').removeClass('required error');
			jQuery('#edit-field-usecase-video-check-no').prop('checked', true);
			let external = 'No';
			externalUsecaseVideoMandatory(external);
		}
	}

	
	function externalUsecaseVideoMandatory(isUsecaseVideo) {

		if (isUsecaseVideo === 'yes') {
			if(jQuery('#field_usecase_video_modified_-media-library-wrapper input').val() !== '') {
				jQuery('#field_usecase_video_modified_-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
				jQuery('#field_usecase_video_modified_-media-library-wrapper input').removeClass('required error');
			}
			else {
				jQuery('#field_usecase_video_modified_-media-library-wrapper .panel-title').addClass('js-form-required form-required');
				jQuery('#field_usecase_video_modified_-media-library-wrapper input').addClass('required');
			}
		}
		else {
			jQuery('#field_usecase_video_modified_-media-library-wrapper .panel-title').removeClass('js-form-required form-required');
			jQuery('#field_usecase_video_modified_-media-library-wrapper input').removeClass('required error');
		}
	}


function external_link_toggle_mandatory_requirement(isExternal) {

  if (isExternal == 'Yes') {
    jQuery('#edit-field-business-driver-duplicate-0-value').addClass('required');
	  jQuery('.form-item-field-business-driver-duplicate-0-value label').addClass('form-required');

	}
	else {
	  jQuery('#edit-field-business-driver-duplicate-0-value').removeClass('required');
		jQuery('.form-item-field-business-driver-duplicate-0-value label').removeClass('form-required');
  }
  if (isExternal == null) {

    jQuery('#edit-field-business-driver-check-no').prop('checked', true);
    jQuery('#edit-field-s-no').prop('checked', true);
  }

}

function usecasevideo_link_toggle_mandatory_requirement(usecaseVideo) {

  if (usecaseVideo == null) {
    jQuery('#edit-field-usecase-video-check-no').prop('checked', true);

  }
}

function livedemo_link_toggle_mandatory_requirement(liveDemo) {

  if (liveDemo == null) {
    jQuery('#edit-field-live-demo-env-check-no').prop('checked', true);

  }
}

function demovideo_link_toggle_mandatory_requirement(demoVideo) {

  if (demoVideo == null) {

    jQuery('#edit-field-demo-video-check-no').prop('checked', true);
  }
}

function solution_link_toggle_mandatory_requirement(isSolution) {
  if (isSolution == 'Yes') {
    jQuery('#edit-field-solution-duplicate-0-value').addClass('required');
	  jQuery('.form-item-field-solution-duplicate-0-value label').addClass('form-required');
	}
	else {
	  jQuery('#edit-field-solution-duplicate-0-value').removeClass('required');
		jQuery('.form-item-field-solution-duplicate-0-value label').removeClass('form-required');
  }
}

	/****** Helper functions ends here ******/


	/****** Usecase Add form page tabs on load ******/
	var count = 1;

	jQuery('div.field-group-tabs-wrapper ul.vertical-tabs-list').after('<div class="vtab-title-custom"></div>');
	jQuery('div[data-drupal-messages-fallback]').after('<div class="messages__wrapper"></div>');
	jQuery('div.field-group-tabs-wrapper ul.vertical-tabs-list li.vertical-tab-button').each(function() {
		jQuery(this).addClass('tab-id-' + count);
		jQuery(this).attr('data-list-count', count);
		
		if (count == 1) {
			jQuery(this).addClass('last-selected-tab');
			
		}
		jQuery(this).children('a').children('div.summary').html(count);

		var data_target_id = jQuery(this).children('a').attr('href');
		jQuery(this).children('a').removeAttr('href');
		jQuery(this).children('a').attr('data-target-id', data_target_id);

		/*** Add required fields info in tabs if it has any required fields ***/
		if (jQuery(data_target_id).find('.required').length > 0) {
			if (!jQuery(this).children('a').hasClass('form-fields-required')) {
				jQuery(this).children('a').addClass('form-fields-required');
				jQuery(this).children('a').attr('title', 'This will have required fields.');
			}
		}

		count = count+1;
	});
	var tab_title = jQuery('div.field-group-tabs-wrapper ul.vertical-tabs-list li.vertical-tab-button.selected a span:first-child').text();
	
	jQuery('div.vtab-title-custom').html('<div class="tab-title-wrapper"><span class="tab-title">' + tab_title + '</span><span class="user-guide-link"><a data-target-help-section = "' + tab_title.toLowerCase().replace(' ', '_') + '" title="'+tab_title+'" class="link_usecase_guide" target="_blank"><i class="fas fa-info"></i></a></span><span class="skip-button"><a class="skip-button-text">Skip to last step</a></span>');

	jQuery('span.skip-button').hide();
    //jQuery('#edit-actions').hide();

		jQuery('#edit-actions').show();
		jQuery('#edit-actions .dropdown').hide();
		jQuery('#edit-actions #edit-delete').hide();
		jQuery('#edit-actions #edit-draft').hide();

	///
	jQuery('.form-type-vertical-tabs').hide();
	jQuery('div.prev-button').hide();

	/**** Parent child class changes ***/
	jQuery('.select-parents input[type=checkbox]').each(function(){
		var selected_id = jQuery(this).prop('id');
		var broken_data = selected_id.split("-children");
		if(broken_data.length > 1 ){
			if (processallchildboxescheckes(broken_data[0]) == 2) {
				jQuery('#'+broken_data[0]).addClass('checkbox-grayed');
			}
			else if (processallchildboxescheckes(broken_data[0]) == 1) {
				jQuery('#'+broken_data[0]).removeClass('checkbox-grayed');
			}
			else if (processallchildboxescheckes(broken_data[0]) == 0) {
				jQuery('#'+broken_data[0]).removeClass('checkbox-grayed');
				jQuery('#'+broken_data[0]).prop("checked", false);
			}
		}
	});
	//processallchildboxescheckes(checkboxid);

	/**** Demo link field toggle mandatory requirement based on selected option ****/
		var isDemo = jQuery("input[name='field_have_demonstration']:checked").val();
		demo_link_toggle_mandatory_requirement(isDemo);
		var isExternal = jQuery("input[name='field_business_driver_check']:checked").val();
		external_link_toggle_mandatory_requirement(isExternal);
		var isSolution = jQuery("input[name='field_s']:checked").val();
		solution_link_toggle_mandatory_requirement(isSolution);
		var liveDemo = jQuery("input[name='field_live_demo_env_check']:checked").val();
		livedemo_link_toggle_mandatory_requirement(liveDemo);
		var demoVideo = jQuery("input[name='field_demo_video_check']:checked").val();
		demovideo_link_toggle_mandatory_requirement(demoVideo);
		var usecaseVideo = jQuery("input[name='field_usecase_video_check']:checked").val();
		usecasevideo_link_toggle_mandatory_requirement(usecaseVideo);
		var internalDemoVideoOption = jQuery("input[name='field_demo_video']:checked").val();
		internalDemoVideoMandatory(internalDemoVideoOption);
		var externalDemoVideoOption = jQuery("input[name='field_demo_video_check']:checked").val();
		externalDemoVideoMandatory(externalDemoVideoOption);
		var internalUsecaseVideoOption = jQuery("input[name='field_have_video_usecase']:checked").val();
		internalUsecaseVideoMandatory(internalUsecaseVideoOption);
		var externalUsecaseVideoOption = jQuery("input[name='field_usecase_video_check']:checked").val();
		externalUsecaseVideoMandatory(externalUsecaseVideoOption);
		var externalDemoLinkOption = jQuery("input[name='field_live_demo_env_check']:checked").val();
		externalDemoLinkMandatory(externalDemoLinkOption);
		

	/****** Usecase Add form page tabs on load ends here******/


	/**** Top Tabs click function ****/
	jQuery('div.field-group-tabs-wrapper ul.vertical-tabs-list li.vertical-tab-button a').unbind("click").bind("click", function(e){
	    e.preventDefault();

		jQuery('.contact_phone_field').each(function(){
			var contact_primary_sec_no = document.querySelector('#'+jQuery(this).attr('id'));
			//window.intlTelInputGlobals.loadUtils("/utils.js");
			var iti = window.intlTelInputGlobals.getInstance(contact_primary_sec_no);
	        var number = iti.getNumber(intlTelInputUtils.numberFormat.E164);
			iti.setNumber(number);
		});

	    var new_tab_count = jQuery(this).parent('li.vertical-tab-button').attr('data-list-count');
	    var current_tab_count = jQuery('li.last-selected-tab').attr('data-list-count');
	    var isValidationRequired = true;

	    if (new_tab_count < current_tab_count) {
			isValidationRequired = false;
		}
	    else if ((+new_tab_count - +current_tab_count) > 1) {
			
            if(current_tab_count !=7){
	    	for (var i = (+current_tab_count+1); i <= (+new_tab_count-1); i++) {
	    		if (!jQuery('li.tab-id-' + i).hasClass('visited')) {
	    			var error_msg_html = '<div class="alert alert-danger alert-dismissible">';
		  			error_msg_html = error_msg_html + '<button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>';
		  			error_msg_html = error_msg_html + '<ul class="item-list item-list--messages">';
		  			error_msg_html = error_msg_html + '<li class="item item--message">Not allowed as there are required fields in steps which you skipped.</li>';
		  			error_msg_html = error_msg_html + '</ul></div>';
		  		    jQuery('div.messages__wrapper').html(error_msg_html);
	    			return false;
	    		}
	    	}
                }
	    }

	    var isValidated = checkIfFormValidated(isValidationRequired);
	    if (isValidated === 'pass') { 
			var pane_id = jQuery('li.last-selected-tab a').attr('data-target-id');
			
			jQuery('li.vertical-tab-button').each(function(){
				if (jQuery(this).hasClass('selected')) {
					jQuery(this).removeClass('selected');
				}
				if (jQuery(this).hasClass('last-selected-tab')) {
					jQuery(this).removeClass('last-selected-tab');
				}
			});
           
			jQuery(this).parent('li.vertical-tab-button').addClass('selected');
			jQuery(this).parent('li.vertical-tab-button').addClass('last-selected-tab');

			jQuery('div' + pane_id).removeClass('active');
			jQuery('div' + pane_id + '--content').removeClass('in');

			jQuery('div' + jQuery(this).attr('data-target-id')).addClass('active');
			jQuery('div' + jQuery(this).attr('data-target-id') + '--content').addClass('in');

			var selected_val = jQuery(this).find('div.summary').text();
			jQuery('a.change-tabs').attr('data-selected-tab', selected_val);
			show_hide_action_buttons(selected_val);
		}
		else { 
			jQuery('li.vertical-tab-button').each(function(){
				if (jQuery(this).hasClass('active')) {
				  jQuery(this).removeClass('active');
				}
			});
			if (!jQuery('li.last-selected-tab').hasClass('active')) {
				jQuery('li.last-selected-tab').addClass('active');
			}
			if (jQuery('div.messages__wrapper').length > 0) {
			  var error_msg_html = '<div class="alert alert-danger alert-dismissible">';
			  error_msg_html = error_msg_html + '<button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>';
			  error_msg_html = error_msg_html + '<ul class="item-list item-list--messages">';
			  error_msg_html = error_msg_html + isValidated;
			  error_msg_html = error_msg_html + '</ul></div>';

		      jQuery('div.messages__wrapper').html(error_msg_html);
		    }
			return false;
		}
	});

	/**** Previous/Next click function ****/
	jQuery('a.change-tabs').click(function(){

	 jQuery('.contact_phone_field').each(function(){
			var contact_primary_sec_no = document.querySelector('#'+jQuery(this).attr('id'));
			//window.intlTelInputGlobals.loadUtils("/utils.js");
			var iti = window.intlTelInputGlobals.getInstance(contact_primary_sec_no);
	        var number = iti.getNumber(intlTelInputUtils.numberFormat.E164);
			iti.setNumber(number);
		});


	  var isValidationRequired = true;
	  var data_button_type = jQuery(this).attr('data-button-type');
	  if (data_button_type === 'prev') {
		isValidationRequired = false;
	  }
	  var isValidated = checkIfFormValidated(isValidationRequired);
	  if (isValidated === 'pass') {
		  var next_prev_counter = 1;
		  var data_selected_tab = jQuery(this).attr('data-selected-tab');
		  var current_tab_class = 'tab-id-' + data_selected_tab;
		  if (data_button_type === 'prev') {
			 var next_tab_val = +data_selected_tab - +next_prev_counter;
		  }
		  else {
			 var next_tab_val = +data_selected_tab + +next_prev_counter;
		  }
		  var next_tab_class = 'tab-id-' + next_tab_val;

		  /*** Change Upper Tab ***/
		  jQuery('li.' + current_tab_class).removeClass('active');
		  jQuery('li.' + current_tab_class).removeClass('selected');

		  if (!jQuery('li.' + current_tab_class).hasClass('visited')) {
			 jQuery('li.' + current_tab_class).addClass('visited')
		  }

		  jQuery('li.' + next_tab_class).addClass('active');
		  jQuery('li.' + next_tab_class).addClass('selected');

		  /*** Change Content based on tabs selected above ***/
		  jQuery(this).attr('data-selected-tab', next_tab_val);
		  jQuery('li.' + next_tab_class).children('a').click();

		  /*** Scroll to top ***/
		  jQuery('html, body').animate({ scrollTop: 0 }, "slow");

		  /*** Update tab index value of Next and previous button ***/
		  jQuery('a.change-tabs').attr('data-selected-tab', next_tab_val);
		  show_hide_action_buttons(next_tab_val);
	  }
	  else {
		  if (jQuery('div.messages__wrapper').length > 0) {
		  var error_msg_html = '<div class="alert alert-danger alert-dismissible">';
		    error_msg_html = error_msg_html + '<button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>';
		    error_msg_html = error_msg_html + '<ul class="item-list item-list--messages">';
		    error_msg_html = error_msg_html + isValidated;
		    error_msg_html = error_msg_html + '</ul></div>';

	        jQuery('div.messages__wrapper').html(error_msg_html);
	      }
		  /*** Scroll to top ***/
		  jQuery('html, body').animate({ scrollTop: 0 }, "slow");

		  return false;
	  }
	});

	/*** Toggle Demo Link field mandatory value based on radiobutton selected ***/
	jQuery("input[name='field_have_demonstration']").click(function(){
	  var isDemo = jQuery(this).val();
	  demo_link_toggle_mandatory_requirement(isDemo);
	});
	
	jQuery("input[name='field_demo_video']").click(function(){
		var isInternalDemoVideo = jQuery(this).val();
		internalDemoVideoMandatory(isInternalDemoVideo);
	});

	jQuery("input[name='field_demo_video_check']").click(function(){
		var isExternalDemoVideo = jQuery(this).val();
		externalDemoVideoMandatory(isExternalDemoVideo);
	}); 

	jQuery("input[name='field_have_video_usecase']").click(function(){
		var isInternalUsecaseVideo = jQuery(this).val();
		internalUsecaseVideoMandatory(isInternalUsecaseVideo);
	});

	jQuery("input[name='field_usecase_video_check']").click(function(){
		var isExternalUsecaseVideo = jQuery(this).val();
		externalUsecaseVideoMandatory(isExternalUsecaseVideo);
	});

	jQuery("input[name='field_live_demo_env_check']").click(function(){
		var externalDemoLinkOption = jQuery(this).val();
		externalDemoLinkMandatory(externalDemoLinkOption);
	});

	 jQuery('a.link_usecase_guide').click(function(){
		var datahelp = jQuery(this).attr('data-target-help-section');

		var url = "/usecase-accelerator-upload-guide#"+datahelp;
		 window.open(url, '_blank');
	  });

    jQuery('a.skip-button-text').click(function(){
	   var next_prev_counter = 1;
		var data_selected_tab = jQuery('a.change-tabs').attr('data-selected-tab');

		var current_tab_class = 'tab-id-' + data_selected_tab;

		var next_tab_val = 'tab-id-' +total_tab_count;

		var next_tab_class = 'tab-id-' + next_tab_val;
        var next_last_child_class = 'tab-id-'+ (total_tab_count-1);
		/*** Change Upper Tab ***/
		jQuery('li.' + current_tab_class).removeClass('active');
		jQuery('li.' + current_tab_class).removeClass('selected');

		if (!jQuery('li.' + current_tab_class).hasClass('visited')) {
		jQuery('li.' + current_tab_class).addClass('visited')
		}

		jQuery('li.'+ next_last_child_class).addClass('visited');


		if (!jQuery('li.tab-id-' + total_tab_count).hasClass('visited')) { alert("laya");
		jQuery('li.tab-id-' + total_tab_count).addClass('active')
		}
        jQuery('a.change-tabs').attr('data-selected-tab', total_tab_count);
		jQuery('li.tab-id-' + total_tab_count).children('a').click();

    });

	jQuery('.contributesave.form-submit').click(function(){
		jQuery('input.contact_phone_field').each(function(){
			var input = document.querySelector('#'+jQuery(this).attr('id'));
            var iti = window.intlTelInputGlobals.getInstance(input);
			var number = iti.getNumber(intlTelInputUtils.numberFormat.E164);
            jQuery(this).val(number);
		});
	});
	//savedraftbutton
	jQuery('.savedraftbutton.form-submit').click(function(){
		jQuery('input.contact_phone_field').each(function(){
			var input = document.querySelector('#'+jQuery(this).attr('id'));
            var iti = window.intlTelInputGlobals.getInstance(input);
			var number = iti.getNumber(intlTelInputUtils.numberFormat.E164);
            jQuery(this).val(number);
		});
	});
  /*** Get Business driver field value and copy to business driver external field ***/
  jQuery('#edit-field-business-driver-check-yes').change(function(){
    var b_driver_data = CKEDITOR.instances["edit-body-0-value"].getData();
    var check_val = CKEDITOR.instances["edit-field-business-driver-duplicate-0-value"].getData();
    if (check_val == '') {
    CKEDITOR.instances["edit-field-business-driver-duplicate-0-value"].setData(b_driver_data);
  }
  });
  /*** Get Solution field value and copy to solution external field ***/
  jQuery('#edit-field-s-yes').change(function(){
    var solution_data = CKEDITOR.instances["edit-field-solution-0-value"].getData();
    var check_val_sol = CKEDITOR.instances["edit-field-solution-duplicate-0-value"].getData();
   if (check_val_sol == '') {
    CKEDITOR.instances["edit-field-solution-duplicate-0-value"].setData(solution_data);
  }
  });

    // Business driver on change status.
    jQuery('#edit-field-business-driver-check').change(function() {
    var check_val = jQuery(this).find(":checked").val();

    if (check_val === 'Yes') {
    jQuery('#edit-field-business-driver-duplicate-0-value').addClass('required');
	  jQuery('.form-item-field-business-driver-duplicate-0-value label').addClass('form-required');
	  jQuery('.form-item-field-business-driver-duplicate-0-value label').addClass('js-form-required');
    }
    if(check_val === 'No') {
    jQuery('#edit-field-business-driver-duplicate-0-value').removeClass('required');
		jQuery('.form-item-field-business-driver-duplicate-0-value label').removeClass('form-required');

    }

});

      // solutions on change status.
    jQuery('#edit-field-s').change(function() {
    var check_val_sal = jQuery(this).find(":checked").val();
    if (check_val_sal == 'Yes') {
    jQuery('#edit-field-solution-duplicate-0-value').addClass('required');
	  jQuery('.form-item-field-solution-duplicate-0-value label').addClass('form-required');
	  jQuery('.form-item-field-solution-duplicate-0-value label').addClass('js-form-required');
	}
	if (check_val_sal === 'No') {
	  jQuery('#edit-field-solution-duplicate-0-value').removeClass('required');
		jQuery('.form-item-field-solution-duplicate-0-value label').removeClass('form-required');
  }

	});

// External video live env field  show/hide.


  jQuery('#edit-field-have-demonstration').click(function() {
    var live_check_val = jQuery(this).find(":checked").val();
    var demo_live_value = jQuery("#edit-field-global-content-yes").is(":checked");

    if (live_check_val == 'yes' && demo_live_value == false) {
		  jQuery('#edit-field-demonstration-details-wrapper').show();

		//	jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }
	   if (live_check_val == 'yes' && demo_live_value == true) {
		  jQuery('#edit-field-demonstration-details-wrapper').show();


			jQuery('#edit-field-live-demo-env-check--wrapper').show();
			//jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }

	  if (live_check_val === 'no') {
		  jQuery('#edit-field-demonstration-details-wrapper').hide();

		  jQuery('#edit-field-live-demo-env-check--wrapper').hide();
		  jQuery('#edit-field-demonstration-details-modi-wrapper').hide();
    }
	});


// External demo video field  show/hide.

 jQuery('#edit-field-demo-video').click(function() {
    var demo_check_val = jQuery(this).find(":checked").val();
    var demo_video_value = jQuery("#edit-field-global-content-yes").is(":checked");
    if (demo_check_val == 'yes' && demo_video_value == false) {
		  jQuery('#edit-field-demo-video-link-wrapper').show();


		//	jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }
	   if (demo_check_val == 'yes' && demo_video_value == true) {
		  jQuery('#edit-field-demo-video-link-wrapper').show();
			jQuery('#edit-field-demo-video-check--wrapper').show();
			jQuery('#edit-field-demo-video-link-wrapper').show();

			//jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }

	  if (demo_check_val === 'no') {
		  jQuery('#edit-field-demo-video-link-wrapper').hide();
		  jQuery('#edit-field-demo-video-check--wrapper').hide();
		  jQuery('#edit-field-demo-video-modified-wrapper').hide();
    }
	});


  jQuery('#edit-field-demo-video-check').click(function() {
    var demo_external = jQuery(this).find(":checked").val();
      if (demo_external == 'Yes') {
		    jQuery('#edit-field-demo-video-modified-wrapper').show();
      }
	    if (demo_external == 'No') {
	  	  jQuery('#edit-field-demo-video-modified-wrapper').hide();
      }
  });

// External usecase video field  show/hide.

jQuery('#edit-field-have-video-usecase').click(function() {
    var usecase_check_val = jQuery(this).find(":checked").val();
    var usecase_video_value = jQuery("#edit-field-global-content-yes").is(":checked");

    if (usecase_check_val == 'yes' && usecase_video_value == false) {
		  jQuery('#edit-field-usecase-video-link-wrapper').show();

		//	jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }
	   if (usecase_check_val == 'yes' && usecase_video_value == true) {
		  jQuery('#edit-field-usecase-video-link-wrapper').show();
			jQuery('#edit-field-usecase-video-check--wrapper').show();
			//jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }

	  if (usecase_check_val === 'no') {
		  jQuery('#edit-field-usecase-video-link-wrapper').hide();
		  jQuery('#edit-field-usecase-video-check--wrapper').hide();
		  jQuery('#edit-field-usecase-video-modified-wrapper').hide();
    }
	});

    jQuery('#edit-field-usecase-video-check').click(function() {
    var usecase_external = jQuery(this).find(":checked").val();
    //var demo_live_value = jQuery("#edit-field-global-content-yes").is(":checked");

    if (usecase_external == 'Yes' || usecase_external == 'yes') {
		  jQuery('#edit-field-usecase-video-modified-wrapper').show();

		//	jQuery('#edit-field-demonstration-details-modi-wrapper').show();
	  }
	 if (usecase_external == 'No') {
	  	jQuery('#edit-field-usecase-video-modified-wrapper').hide();
}

});

// video fields no bahaviour.
	jQuery('#edit-field-demo-video-no').click(function() {
		jQuery('#edit-field-demo-video-check-no').prop('checked', true);
	});
	jQuery('#edit-field-have-demonstration-no').click(function() {
	 jQuery('#edit-field-live-demo-env-check-no').prop('checked', true);
	});
	jQuery('#edit-field-have-video-usecase-no').click(function() {
		jQuery('#edit-field-usecase-video-check-no').prop('checked', true);
	});

 jQuery('#edit-field-have-demonstration-no').click(function() {
        jQuery('#edit-group-demonstration-details-modi').css('display','none');
		jQuery('#edit-group-demonstration-details').children('.panel-heading').css('display','none');
		jQuery('#edit-field-live-demo-env-check--wrapper').removeClass('byDefaultChecked');
      });

      jQuery('#edit-field-demo-video-no').click(function() {
        jQuery('#edit-group-demo-video-modified').css('display','none');
		jQuery('#edit-group-demo-video').children('.panel-heading').css('display','none');
		jQuery('#edit-field-demo-video-check--wrapper').removeClass('byDefaultChecked');
      });

      jQuery('#edit-field-have-video-usecase-no').click(function() {
        jQuery('#edit-group-usecase-video-modi').css('display','none');
		jQuery('#edit-group-usecase-video').children('.panel-heading').css('display','none');
		jQuery('#edit-field-usecase-video-check--wrapper').removeClass('byDefaultChecked');
      });



jQuery(window).on('load', (function() {


	jQuery('ul.nav.nav-tabs.vertical-tabs-list li a').click(function() { 
		preview_select_first();
	});
	jQuery('.usecase-actions-wrapper .prev-button a, .usecase-actions-wrapper .next-button a').click(function() {
		preview_select_first();
	});

	jQuery("#case-study-template-snapshot").click(function() {
		jQuery(this).toggleClass("bigger");
	 });



	function caseStudyGlobalYes() {
        jQuery('.field--name-field-case-study-title-c-study label, .field--name-field-client-c-study label, .field--name-field-business-objective-n- label, .field--name-field-capgemini-solution label, .field--name-field-benefits-values-delivered- label, .field--name-field-tools-and-techniques-n label, .field--name-field-solution-showcase label, .field--name-field-contact-email label').addClass('form-required js-form-required');
        jQuery('.field--name-field-case-study-title-c-study input, .field--name-field-client-c-study input, .field--name-field-business-objective-n- input, .field--name-field-capgemini-solution input, .field--name-field-benefits-values-delivered- input, .field--name-field-tools-and-techniques-n input, .field--name-field-solution-showcase input, .field--name-field-contact-email input').addClass('required');
		jQuery('#edit-field-case-study-details-wrapper').show();
	}
  
    jQuery('#edit-field-do-you-want-to-add-case-st-yes').click(function() {
        caseStudyGlobalYes();  
    });
    
    if(jQuery('#edit-field-do-you-want-to-add-case-st-yes').prop('checked')) {
        caseStudyGlobalYes();
	}
	  
	function caseStudyGlobalNo() {
        jQuery('.field--name-field-case-study-title-c-study label, .field--name-field-client-c-study label, .field--name-field-business-objective-n- label, .field--name-field-capgemini-solution label, .field--name-field-benefits-values-delivered- label, .field--name-field-tools-and-techniques-n label, .field--name-field-solution-showcase label, .field--name-field-contact-email label').removeClass('form-required js-form-required');
		jQuery('.field--name-field-case-study-title-c-study input, .field--name-field-client-c-study input, .field--name-field-business-objective-n- input, .field--name-field-capgemini-solution input, .field--name-field-benefits-values-delivered- input, .field--name-field-tools-and-techniques-n input, .field--name-field-solution-showcase input, .field--name-field-contact-email input').removeClass('required error');
		jQuery('.field--name-field-case-study-title-c-study input, .field--name-field-client-c-study input, .field--name-field-business-objective-n- input, .field--name-field-capgemini-solution input, .field--name-field-benefits-values-delivered- input, .field--name-field-tools-and-techniques-n input, .field--name-field-solution-showcase input, .field--name-field-contact-email input').removeAttr('required');
		jQuery('.field--name-field-case-study-title-c-study input, .field--name-field-client-c-study input, .field--name-field-business-objective-n- input, .field--name-field-capgemini-solution input, .field--name-field-benefits-values-delivered- input, .field--name-field-tools-and-techniques-n input, .field--name-field-solution-showcase input, .field--name-field-contact-email input').parent('div.form-type-textfield').parent('.form-wrapper').removeClass('has-error error');		
		jQuery('#edit-field-case-study-details-wrapper').hide();
	}
  
    jQuery('#edit-field-do-you-want-to-add-case-st-no').click(function() {
    	caseStudyGlobalNo();
    });
    
    if(jQuery('#edit-field-do-you-want-to-add-case-st-no').prop('checked')) {
        caseStudyGlobalNo();
	}
	

	var reff2 = 0;


	// Adding class for tab display
  	jQuery('#edit-group-bd-container').addClass('parent_container_non_duplicate_field');
	jQuery('#edit-group-bd-container-right').addClass('parent_container_duplicate_field');

    jQuery('#edit-group-solution-container-left').addClass('parent_container_non_duplicate_field');
	jQuery('#edit-group-solution-container-right').addClass('parent_container_duplicate_field');

	jQuery('#edit-group-demonstration-details').addClass('parent_container_non_duplicate_field');
	jQuery('#edit-group-demonstration-details-modi').addClass('parent_container_duplicate_field');

	jQuery('#edit-group-demo-video').addClass('parent_container_non_duplicate_field');
	jQuery('#edit-group-demo-video-modified').addClass('parent_container_duplicate_field');

	jQuery('#edit-group-usecase-video').addClass('parent_container_non_duplicate_field');
	jQuery('#edit-group-usecase-video-modi').addClass('parent_container_duplicate_field');



	// By default removing all the tabs on page load
	jQuery('#edit-group-bd-container-right').css('display','none');
	jQuery('#edit-group-bd-container').children('.panel-heading').css('display','none');

	jQuery('#edit-group-solution-container-right').css('display','none');
	jQuery('#edit-group-solution-container-left').children('.panel-heading').css('display','none');

	jQuery('#edit-group-demonstration-details-modi').css('display','none');
	jQuery('#edit-group-demonstration-details').children('.panel-heading').css('display','none');

	jQuery('#edit-group-demo-video-modified').css('display','none');
	jQuery('#edit-group-demo-video').children('.panel-heading').css('display','none');

	jQuery('#edit-group-usecase-video-modi').css('display','none');
	jQuery('#edit-group-usecase-video').children('.panel-heading').css('display','none');

	jQuery('#edit-group-primary-owner-modified-').css('display','none');
	jQuery('#edit-group-primary-contact').children('.panel-heading').css('display','none');



	jQuery("#edit-group-bd-container--content, #edit-group-solution-container-left--content, #edit-group-demonstration-details--content, #edit-group-demo-video--content, #edit-group-usecase-video--content").addClass("tabDisplayBodyContentLeftFields");

	jQuery("#edit-group-bd-container-right--content, #edit-group-solution-container-right--content, #edit-group-demonstration-details-modi--content, #edit-group-demo-video-modified--content, #edit-group-usecase-video-modi--content").addClass("tabDisplayBodyContentRightFields");

	// adding horizontal line after each video section. For Local only
	jQuery('#edit-field-have-video-usecase-wrapper').before('<hr/>');
	jQuery('#edit-field-demo-video-wrapper').before('<hr/>');


	//
	jQuery('#edit-field-demo-video-yes').click(function() {
		jQuery('#edit-field-demo-video-link-wrapper').show();
		jQuery('#edit-field-demo-video-check--wrapper').show();
		jQuery('#edit-group-demo-video--content').addClass('in');
		jQuery('#edit-group-demo-video--content').css('padding','15px');
	});

	jQuery('#edit-field-have-demonstration-yes').click(function() {
		jQuery('#edit-field-demonstration-details-wrapper').show();
		jQuery('#edit-field-live-demo-env-check--wrapper').show();
		jQuery('#edit-group-demonstration-details--content').addClass('in');
		jQuery('#edit-field-live-demo-env-check--wrapper').show();
		jQuery('#edit-group-demonstration-details--content').css('padding','15px');
	});

	jQuery('#edit-field-have-video-usecase-yes').click(function() {
		jQuery('#edit-field-usecase-video-link-wrapper').show();
		jQuery('#edit-field-usecase-video-check--wrapper').show();
		jQuery('#edit-group-usecase-video--content').addClass('in');
		jQuery('#edit-group-usecase-video--content').css('padding','15px');
	});



    // Business driver yes/no click behaviour.
    jQuery('#edit-field-business-driver-check-yes').click(function(event) {
		jQuery('#edit-group-bd-container-right').css('display','block');
		jQuery('#edit-group-bd-container').children('.panel-heading').css('display','block');
		jQuery("body,html").animate({ scrollTop: "600" },900);
		jQuery('#edit-group-bd-container-right .panel-heading a').click();
		event.stopPropagation();
		setTimeout(function() {
			if(reff2 == 0) {
				dialogDescription();
			}
			reff2 = 1;
		}, 1300);

    });
    jQuery('#edit-field-business-driver-check-no').click(function() {
		jQuery('#edit-group-bd-container-right').css('display','none');
		jQuery('#edit-group-bd-container').children('.panel-heading').css('display','none');
    });

    // Solution yes/no click behaviour.
    jQuery('#edit-field-s-yes').click(function(event) {
	  jQuery('#edit-group-solution-container-right').css('display','block');
	  jQuery('#edit-group-solution-container-left').children('.panel-heading').css('display','block');
	  jQuery("body,html").animate({ scrollTop: "1100" },200);
	  jQuery('#edit-group-solution-container-right .panel-heading a').click();
	  event.stopPropagation();
	  setTimeout(function() {
		if(reff2 == 0) {
			dialogDescription();
		}
		reff2 = 1;
	}, 1300);

    });
    jQuery('#edit-field-s-no').click(function() {
	  jQuery('#edit-group-solution-container-right').css('display','none');
	  jQuery('#edit-group-solution-container-left').children('.panel-heading').css('display','none');
    });

	// demonstration details yes/no click behaviour. For local only
	jQuery('#edit-field-live-demo-env-check-yes').click(function() {
		jQuery('#edit-group-demonstration-details-modi').css('display','block');
		jQuery('#edit-group-demonstration-details').children('.panel-heading').css('display','block');
	});
	jQuery('#edit-field-live-demo-env-check-no').click(function() {
		jQuery('#edit-group-demonstration-details-modi').css('display','none');
		jQuery('#edit-group-demonstration-details').children('.panel-heading').css('display','none');
	});

	// demo video yes/no click behaviour. For local only
	jQuery('#edit-field-demo-video-check-yes').click(function() {
		jQuery('#edit-group-demo-video-modified').css('display','block');
		jQuery('#edit-group-demo-video').children('.panel-heading').css('display','block');
	});
	jQuery('#edit-field-demo-video-check-no').click(function() {
		jQuery('#edit-group-demo-video-modified').css('display','none');
		jQuery('#edit-group-demo-video').children('.panel-heading').css('display','none');
	});

	// usecase video yes/no click behaviour. For local only
 	jQuery('#edit-field-usecase-video-check-yes').click(function() {
		jQuery('#edit-group-usecase-video-modi').show();
		jQuery('#edit-group-usecase-video').children('.panel-heading').show();
	});
	  jQuery('#edit-field-usecase-video-check-no').click(function() {
		jQuery('#edit-group-usecase-video-modi').css('display','none');
		jQuery('#edit-group-usecase-video').children('.panel-heading').css('display','none');
	});

	// For dev, stage and prod
	// demonstration details yes/no click behaviour. For dev, stage and prod
	jQuery('#edit-field-live-demo-env-check-yes').click(function(event) {
		jQuery('#edit-group-demonstration-details-modi').css('display','block');
		jQuery('#edit-group-demonstration-details').children('.panel-heading').css('display','block');
		var myDiv = jQuery("#edit-field-have-demonstration-wrapper");
		var finalHeight = 100;
		var scrollto = myDiv.offset().top - (finalHeight);
		jQuery("body,html").animate(
		  {
		  scrollTop: scrollto
		  },
		  200
		);
		jQuery('#edit-group-demonstration-details-modi .panel-heading a').click();
		event.stopPropagation();
		setTimeout(function() {
			if(reff2 == 0) {
				dialogDescription();
			}
			reff2 = 1;
		}, 1300);
	});
	jQuery('#edit-field-live-demo-env-check-no').click(function() {
		jQuery('#edit-group-demonstration-details-modi').css('display','none');
		jQuery('#edit-group-demonstration-details').children('.panel-heading').css('display','none');
	});

	// demo video yes/no click behaviour. For dev, stage and prod
	jQuery('#edit-field-demo-video-check-yes').click(function(event) {
		jQuery('#edit-group-demo-video-modified').css('display','block');
		jQuery('#edit-group-demo-video').children('.panel-heading').css('display','block');
		jQuery('#edit-field-demo-video-modified-wrapper').show();
		var myDiv = jQuery("#edit-field-demo-video-wrapper");
		var finalHeight = 130;
		var scrollto = myDiv.offset().top - (finalHeight);
		jQuery("body,html").animate(
		  {
		  scrollTop: scrollto
		  },
		  200
		);
		jQuery('#edit-group-demo-video-modified .panel-heading a').click();
		event.stopPropagation();
		setTimeout(function() {
			if(reff2 == 0) {
				dialogDescription();
			}
			reff2 = 1;
		}, 1300);
	});
	jQuery('#edit-field-demo-video-check-no').click(function() {
		jQuery('#edit-group-demo-video-modified').css('display','none');
		jQuery('#edit-group-demo-video').children('.panel-heading').css('display','none');
	});

	// usecase video yes/no click behaviour. For dev, stage and prod
	jQuery('#edit-field-usecase-video-check-yes').click(function(event) {
		jQuery('#edit-group-usecase-video-modi').css('display','block');
		jQuery('#edit-group-usecase-video').children('.panel-heading').css('display','block');
		jQuery('#edit-field-usecase-video-modified-wrapper').css('display','block');
		var myDiv = jQuery("#edit-field-have-video-usecase-wrapper");
		var finalHeight = 130;
		var scrollto = myDiv.offset().top - (finalHeight);
		jQuery("body,html").animate(
		  {
		  scrollTop: scrollto
		  },
		  200
		);
		jQuery('#edit-group-usecase-video-modi .panel-heading a').click();
		event.stopPropagation();

		setTimeout(function() {
			if(reff2 == 0) {
				dialogDescription();
			}
			reff2 = 1;
		}, 1300);
	});
	jQuery('#edit-field-usecase-video-check-no').click(function() {
		jQuery('#edit-group-usecase-video-modi').css('display','none');
		jQuery('#edit-group-usecase-video').children('.panel-heading').css('display','none');
	});


    // Business driver tab form.
	jQuery('#edit-group-bd-container-right').click(function() {
        jQuery(this).siblings('#edit-group-bd-container').children('#edit-group-bd-container--content').removeClass('in');
        jQuery('#edit-group-bd-container-right .panel-heading').addClass('bgBlueHeading');
        jQuery('#edit-group-bd-container-right .panel-heading').removeClass('bgLightgrey');
        jQuery('#edit-group-bd-container .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-bd-container .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-bd-container-right--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-bd-container--content').removeClass('referenceContentTabEnabled');
    });

    jQuery('#edit-group-bd-container').click(function() {
        jQuery(this).siblings('#edit-group-bd-container-right').children('#edit-group-bd-container-right--content').removeClass('in');
        jQuery('#edit-group-bd-container .panel-heading').addClass('bgBlueHeading');
        jQuery('#edit-group-bd-container .panel-heading').removeClass('bgLightgrey');
        jQuery('#edit-group-bd-container-right .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-bd-container-right .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-bd-container--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-bd-container-right--content').removeClass('referenceContentTabEnabled');
    });


    // Solution tab form.
	jQuery('#edit-group-solution-container-right').click(function() {
		jQuery(this).siblings('#edit-group-solution-container-left').children('#edit-group-solution-container-left--content').removeClass('in');
		jQuery('#edit-group-solution-container-right .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-solution-container-right .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-solution-container-left .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-solution-container-left .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-solution-container-right--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-solution-container-left--content').removeClass('referenceContentTabEnabled');
	});

	jQuery('#edit-group-solution-container-left').click(function() {
		jQuery(this).siblings('#edit-group-solution-container-right').children('#edit-group-solution-container-right--content').removeClass('in');
		jQuery('#edit-group-solution-container-left .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-solution-container-left .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-solution-container-right .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-solution-container-right .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-solution-container-left--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-solution-container-right--content').removeClass('referenceContentTabEnabled');
	});

	// demonstration detail tab form
	jQuery('#edit-group-demonstration-details-modi').click(function() {
		jQuery(this).siblings('#edit-group-demonstration-details').children('#edit-group-demonstration-details--content').removeClass('in');
		jQuery('#edit-group-demonstration-details-modi .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-demonstration-details-modi .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-demonstration-details .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-demonstration-details .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-demonstration-details-modi--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-demonstration-details--content').removeClass('referenceContentTabEnabled');
	});

	jQuery('#edit-group-demonstration-details').click(function() {
		jQuery(this).siblings('#edit-group-demonstration-details-modi').children('#edit-group-demonstration-details-modi--content').removeClass('in');
		jQuery('#edit-group-demonstration-details .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-demonstration-details .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-demonstration-details-modi .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-demonstration-details-modi .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-demonstration-details--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-demonstration-details-modi--content').removeClass('referenceContentTabEnabled');
	});


	// demo video tab form
	jQuery('#edit-group-demo-video-modified').click(function() {
		jQuery(this).siblings('#edit-group-demo-video').children('#edit-group-demo-video--content').removeClass('in');
		jQuery('#edit-group-demo-video-modified .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-demo-video-modified .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-demo-video .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-demo-video .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-demo-video-modified--content').addClass('referenceContentTabEnabled');
		jQuery('#edit-group-demo-video--content').removeClass('referenceContentTabEnabled');
		jQuery('#edit-field-demo-video-modified-wrapper').show();
	});

	jQuery('#edit-group-demo-video').click(function() {
		jQuery(this).siblings('#edit-group-demo-video-modified').children('#edit-group-demo-video-modified--content').removeClass('in');
		jQuery('#edit-group-demo-video .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-demo-video .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-demo-video-modified .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-demo-video-modified .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-demo-video--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-demo-video-modified--content').removeClass('referenceContentTabEnabled');
	});


	// usecase video tab form.
	jQuery('#edit-group-usecase-video-modi').click(function() {
		jQuery(this).siblings('#edit-group-usecase-video').children('#edit-group-usecase-video--content').removeClass('in');
		jQuery('#edit-group-usecase-video-modi .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-usecase-video-modi .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-usecase-video .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-usecase-video .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-usecase-video-modi--content').addClass('referenceContentTabEnabled');
		jQuery('#edit-group-usecase-video--content').removeClass('referenceContentTabEnabled');
		jQuery('#edit-field-usecase-video-modified-wrapper').show();
	});

	jQuery('#edit-group-usecase-video').click(function() {
		jQuery(this).siblings('#edit-group-usecase-video-modi').children('#edit-group-usecase-video-modi--content').removeClass('in');
		jQuery('#edit-group-usecase-video .panel-heading').addClass('bgBlueHeading');
		jQuery('#edit-group-usecase-video .panel-heading').removeClass('bgLightgrey');
		jQuery('#edit-group-usecase-video-modi .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-usecase-video-modi .panel-heading').removeClass('bgBlueHeading');
		jQuery('#edit-group-usecase-video--content').addClass('referenceContentTabEnabled');
        jQuery('#edit-group-usecase-video-modi--content').removeClass('referenceContentTabEnabled');
	});


	// if modified column is already clicked as yes
	if(jQuery('#edit-field-business-driver-check-yes').prop('checked')) {
		jQuery('#edit-group-bd-container .panel-heading').show();
		jQuery('#edit-group-bd-container-right').show();
		jQuery('#edit-group-bd-container-right .panel-heading').addClass('bgLightgrey');	
	}

	if(jQuery('#edit-field-s-yes').prop('checked')) {
		jQuery('#edit-group-solution-container-left .panel-heading').show();
		jQuery('#edit-group-solution-container-right').show();	
		jQuery('#edit-group-solution-container-right .panel-heading').addClass('bgLightgrey');	
	}

	if(jQuery('#edit-field-live-demo-env-check-yes').prop('checked')) {
		jQuery('#edit-group-demonstration-details .panel-heading').show();
		jQuery('#edit-group-demonstration-details-modi').show();
		// jQuery('#edit-group-demonstration-details-modi .panel-heading').addClass('bgLightgrey');
		jQuery('#edit-group-demonstration-details-modi .panel-heading').addClass('bgLightgrey');		
	}

	if(jQuery('#edit-field-demo-video-check-yes').prop('checked')) {
		jQuery('#edit-group-demo-video .panel-heading').show();
		jQuery('#edit-group-demo-video-modified').show();
		jQuery('#edit-group-demo-video-modified .panel-heading').addClass('bgLightgrey');	
	}

	if(jQuery('#edit-field-usecase-video-check-yes').prop('checked')) {
		jQuery('#edit-group-usecase-video .panel-heading').show();
		jQuery('#edit-group-usecase-video-modi').show();
		jQuery('#edit-group-usecase-video-modi .panel-heading').addClass('bgLightgrey');	
	}
	
	// if yes/no column in video section already clicked as yes
	if(jQuery('#edit-field-have-demonstration-yes').prop('checked')) {
		jQuery('#edit-field-demonstration-details-wrapper,#edit-field-live-demo-env-check--wrapper').css("display","block");
		jQuery('#edit-field-demonstration-details-wrapper,#edit-field-live-demo-env-check--wrapper').addClass('byDefaultChecked');
		jQuery('#edit-group-demonstration-details--content').css('padding','15px');
	}

	if(jQuery('#edit-field-demo-video-yes').prop('checked')) {
		jQuery('#edit-field-demo-video-link-wrapper,#edit-field-demo-video-check--wrapper').css("display","block");
		jQuery('#edit-field-demo-video-link-wrapper, #edit-field-demo-video-check--wrapper').addClass('byDefaultChecked');
		jQuery('#edit-group-demo-video--content').css('padding','15px');
	}

	if(jQuery('#edit-field-have-video-usecase-yes').prop('checked')) {
		jQuery('#edit-field-usecase-video-link-wrapper,#edit-field-usecase-video-check--wrapper').css("display","block");
		jQuery('#edit-field-usecase-video-link-wrapper,#edit-field-usecase-video-check--wrapper').addClass('byDefaultChecked');
		jQuery('#edit-group-usecase-video--content').css('padding','15px');
	}

	if(jQuery('#edit-field-have-demonstration-no').prop('checked')) {
		jQuery('#edit-group-demonstration-details--content').css('padding','0');
		jQuery('#edit-field-live-demo-env-check--wrapper').hide();
		jQuery('#edit-field-live-demo-env-check--wrapper').removeClass('byDefaultChecked');
	}

	if(jQuery('#edit-field-demo-video-no').prop('checked')) {
		jQuery('#edit-group-demo-video--content').css('padding','0');
		jQuery('#edit-field-demo-video-check--wrapper').hide();
		jQuery('#edit-field-demo-video-check--wrapper').removeClass('byDefaultChecked');
	}

	if(jQuery('#edit-field-have-video-usecase-no').prop('checked')) {
		jQuery('#edit-group-usecase-video--content').css('padding','0');
		jQuery('#edit-field-usecase-video-check--wrapper').hide();
		jQuery('#edit-field-usecase-video-check--wrapper').removeClass('byDefaultChecked');
	}

	if(jQuery('#edit-field-have-demonstration-no').prop('checked') && jQuery('#edit-field-live-demo-env-check-yes').prop('checked')) {
		jQuery('#edit-group-demonstration-details').children('.panel-heading').hide();
		jQuery('#edit-group-demonstration-details-modi').hide();
		jQuery('#edit-field-live-demo-env-check--wrapper').removeClass('byDefaultChecked');

	}

	if(jQuery('#edit-field-demo-video-no').prop('checked') && jQuery('#edit-field-demo-video-check-yes').prop('checked')) {
		jQuery('#edit-group-demo-video').children('.panel-heading').hide();
		jQuery('#edit-group-demo-video-modified').hide();
		jQuery('#edit-field-demo-video-check--wrapper').removeClass('byDefaultChecked');
	}

	if(jQuery('#edit-field-have-video-usecase-no').prop('checked') && jQuery('#edit-field-usecase-video-check-yes').prop('checked')) {
		jQuery('#edit-group-usecase-video').children('.panel-heading').hide();
		jQuery('#edit-group-usecase-video-modi').hide();
		jQuery('#edit-field-usecase-video-check--wrapper').removeClass('byDefaultChecked');
	}

	

	jQuery('#edit-field-have-demonstration-no').click(function() {
		jQuery('#edit-field-demonstration-details-wrapper, #edit-field-live-demo-env-check-wrapper').removeClass('byDefaultChecked');
		jQuery('#edit-group-demonstration-details--content').css('padding','0');
		jQuery('#edit-field-live-demo-env-check--wrapper').removeClass('byDefaultChecked');

	})

	jQuery('#edit-field-demo-video-no').click(function() {
		jQuery('#edit-field-demo-video-link-wrapper, #edit-field-demo-video-check-wrapper').removeClass('byDefaultChecked');
		jQuery('#edit-group-demo-video--content').css('padding','0');
		jQuery('#edit-field-demo-video-check--wrapper').removeClass('byDefaultChecked');
	})

	jQuery('#edit-field-have-video-usecase-no').click(function() {
		jQuery('#edit-field-usecase-video-link-wrapper, #edit-field-usecase-video-check-wrapper').removeClass('byDefaultChecked');
		jQuery('#edit-group-usecase-video--content').css('padding','0');
		jQuery('#edit-field-usecase-video-check--wrapper').removeClass('byDefaultChecked');
	})


	// addtional code
	function overviewTabYesClicked() {
		jQuery('#edit-field-business-driver-check--wrapper').addClass('overview_yes_clicked');
		jQuery('#edit-field-business-driver-check-yes').prop('checked','true');
		jQuery('#edit-field-business-driver-duplicate-0-value').addClass('required');
		jQuery('.form-item-field-business-driver-duplicate-0-value label').addClass('form-required');
		jQuery('#edit-group-bd-container-right, #edit-field-business-driver-duplicate-wrapper, #edit-group-bd-container .panel-heading').addClass('showing');

		jQuery('#edit-field-s--wrapper').addClass('overview_yes_clicked');
		jQuery('#edit-field-solution-duplicate-0-value').addClass('required');
		jQuery('.form-item-field-solution-duplicate-0-value label').addClass('form-required');
		jQuery('#edit-field-s-yes').prop('checked','true');
		jQuery('#edit-group-solution-container-left .panel-heading, #edit-group-solution-container-right, #edit-field-solution-duplicate-wrapper').addClass('showing');
		jQuery('#edit-field-select-reason--wrapper').removeClass('required error');
		jQuery('#edit-field-select-reason--wrapper span.fieldset-legend').removeClass('form-required error');
	}

	function overviewTabNoClicked() {
		jQuery('#edit-field-business-driver-check--wrapper').addClass('overview_yes_clicked');
		jQuery('#edit-field-business-driver-check-no').prop('checked','true');
		jQuery('#edit-field-business-driver-duplicate-0-value').removeClass('required');
		jQuery('.form-item-field-business-driver-duplicate-0-value label').removeClass('form-required');
		jQuery('#edit-group-bd-container-right, #edit-field-business-driver-duplicate-wrapper, #edit-group-bd-container .panel-heading').removeClass('showing');
	
		jQuery('#edit-field-s--wrapper').addClass('overview_yes_clicked');
		jQuery('#edit-field-solution-duplicate-0-value').removeClass('required');
		jQuery('.form-item-field-solution-duplicate-0-value label').removeClass('form-required');
		jQuery('#edit-field-s-no').prop('checked','true');
		jQuery('#edit-group-solution-container-left .panel-heading, #edit-group-solution-container-right, #edit-field-solution-duplicate-wrapper').removeClass('showing');
		jQuery('#edit-field-select-reason--wrapper').addClass('required');
		jQuery('#edit-field-select-reason--wrapper span.fieldset-legend').addClass('form-required');

	}

	var c2 = false;
	jQuery('#edit-field-do-you-want-to-showcase-th-yes').click(function() {
		overviewTabYesClicked();
		c2 = true;  
	});
	
	jQuery('#edit-field-do-you-want-to-showcase-th-no').click(function() {
		overviewTabNoClicked();
	});

	if(jQuery('#edit-field-do-you-want-to-showcase-th-yes').prop('checked')) {
		overviewTabYesClicked();
		c2 = true;
	}

	if(jQuery('#edit-field-do-you-want-to-showcase-th-no').prop('checked')) {
		overviewTabNoClicked();
	}


	var c1 = 0;
	jQuery('.next-button').click(function() {
		if(c1==0 && c2) {
			setTimeout(() => {
				dialogpopup();
			}, 2000)        
			c1=1;
		}
	});
	
	function dialogpopup() {
		jQuery('#node-use-case-or-accelerator-edit-form').append('<div id="dialog-confirm-3"></div>');
	  	jQuery('#node-use-case-or-accelerator-form').append('<div id="dialog-confirm-3"></div>');
		jQuery('body').append('<div class="dailog-bg" style="width:100%; height:100%; backdrop-filter: blur(5px); z-index:-1"></div>');
		jQuery("#dialog-confirm-3").html("<div class='dialog_box_wrapper'><p class='dialog_box_heading'><span>Attention</span></p><p class='dialog_box_description'><span>Voila, you have opted to submit use-case/ accelerator for external audience as well. To enable this, an extra tab is made available to enter the information and you can easily switch across the tabs (internal and external).</span></p></div>");
		jQuery("#dialog-confirm-3").dialog({
			autoOpen: true,
			resizable: false,
			modal: true,
			title: "",
			height: 250,
			width: 550,
			});
	}


	

  var usecase_val = jQuery("input[name='edit-field-have-video-usecase']:checked").val();
  if(usecase_val == null) {
	  jQuery('#edit-field-usecase-video-link-wrapper').hide();
	  jQuery('#edit-field-usecase-video-check--wrapper').hide();
	  jQuery('#edit-field-usecase-video-modified-wrapper').hide();
  }
  var demo_live_val = jQuery("input[name='edit-field-have-demonstration']:checked").val();

  if(demo_live_val == null) {
	  jQuery('#edit-field-demonstration-details-wrapper').hide();
	  jQuery('#edit-field-live-demo-env-check--wrapper').hide();
	  jQuery('#edit-field-demonstration-details-modi-wrapper').hide();
  }

  var demo_video_val = jQuery("input[name='edit-field-demo-video']:checked").val();

  if(demo_video_val == null) {
	  jQuery('#edit-field-demo-video-link-wrapper').hide();

		jQuery('#edit-field-demo-video-check--wrapper').hide();
		jQuery('#edit-field-demo-video-modified-wrapper').hide();
  }
  // else
  // {
  // 	jQuery('#edit-field-demo-video-link-wrapper').show();

		// jQuery('#edit-field-demo-video-check--wrapper').show();
		// jQuery('#edit-field-demo-video-modified-wrapper').show();
  // }

  // global yes/no
  jQuery('#edit-field-do-you-want-to-showcase-th-yes').click(function() {
	change_yes_event();
  });

  jQuery('#edit-field-do-you-want-to-showcase-th-no').click(function() {
	change_no_event();
  });

  if(jQuery('#edit-field-do-you-want-to-showcase-th-yes').prop('checked')) {
	change_yes_event();
  }

  if(jQuery('#edit-field-do-you-want-to-showcase-th-no').prop('checked')) {
	change_no_event();
  }

  function change_no_event() {
	// remove external option from preview section
	jQuery("#edit-select-preview option[value='2']").remove();
	jQuery('#edit-group-bd-container-right,#edit-group-bd-container .panel-heading,#edit-field-business-driver-check--wrapper,#edit-group-solution-container-right,#edit-group-solution-container-left .panel-heading,#edit-field-s--wrapper,#edit-group-demonstration-details-modi,#edit-group-demonstration-details .panel-heading,#edit-field-live-demo-env-check--wrapper,#edit-group-demo-video-modified,#edit-group-demo-video .panel-heading,#edit-field-demo-video-check--wrapper,#edit-group-usecase-video-modi,#edit-group-usecase-video .panel-heading,#edit-field-usecase-video-check--wrapper').addClass('global_selected_no');
	jQuery('#edit-group-demo-video--content .panel-heading, #edit-group-usecase-video--content .panel-heading, #edit-group-demonstration-details--content .panel-heading').removeClass('global_selected_no');

	if(jQuery('#edit-field-have-demonstration-yes').prop('checked')) {
		jQuery('#edit-group-demonstration-details--content').addClass('referenceContentTabEnabled');
	}

	if(jQuery('#edit-field-demo-video-yes').prop('checked')) {
		jQuery('#edit-group-demo-video--content').addClass('referenceContentTabEnabled');
	}

	if(jQuery('#edit-field-have-video-usecase-yes').prop('checked')) {
		jQuery('#edit-group-usecase-video--content').addClass('referenceContentTabEnabled');
	}

}

  function change_yes_event() {
	// add external option to preview section
	if(!(jQuery("#edit-select-preview option[value='2']").length > 0)) {
		jQuery('#edit-select-preview').append('<option value="2">External Preview</option>');
	}
	jQuery('#edit-group-bd-container-right,#edit-group-bd-container .panel-heading,#edit-field-business-driver-check--wrapper,#edit-group-solution-container-right,#edit-group-solution-container-left .panel-heading,#edit-field-s--wrapper,#edit-group-demonstration-details-modi,#edit-group-demonstration-details .panel-heading,#edit-field-live-demo-env-check--wrapper,#edit-group-demo-video-modified,#edit-group-demo-video .panel-heading,#edit-field-demo-video-check--wrapper,#edit-group-usecase-video-modi,#edit-group-usecase-video .panel-heading,#edit-field-usecase-video-check--wrapper').removeClass('global_selected_no');
  }
  
  function preview_select_first() {
	jQuery('#edit-select-preview').prop("selectedIndex",0);
  }
}));
});

(function ($, Drupal) {

	Drupal.theme.ajaxProgressBarAicustomprogressbar = function (message, option_custom) {
	  var throbber = '<span class="throbber">Loading</span>';
  
	  return '<span class="ajax-progress ajax-progress-custom">' + '<div class="lds-dual-ring"></div>';
	};
  
	Drupal.Ajax.prototype.setProgressIndicatorAicustomprogressbar = function () {
	  this.progress.element = $(Drupal.theme('ajaxProgressBarAicustomprogressbar', this.progress.message, this.progress.option_custom));
	  $(this.element).after(this.progress.element);
	};
  
  })(jQuery, Drupal);
