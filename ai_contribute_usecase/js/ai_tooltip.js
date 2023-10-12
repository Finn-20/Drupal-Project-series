(function ($, Drupal, drupalSettings) {
	jQuery(document).ready(function(){
	  jQuery('label[for="edit-field-geography"]').after('<span id="tooltip-icon"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-icon").mouseover(function(){
		jQuery("#tooltip-icon").attr('title', jQuery('input[name=associated_coe]').val());
	  });
	  jQuery('#edit-field-demo-video--wrapper span.fieldset-legend').after('<span id="tooltip-demo-video"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-demo-video").mouseover(function(){
	    jQuery("#tooltip-demo-video").attr('title', jQuery('input[name=help_text_for_demo]').val());
	  });
	  jQuery('#edit-field-have-video-usecase--wrapper span.fieldset-legend').after('<span id="tooltip-icon-usecase"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-icon-usecase").mouseover(function(){
	    jQuery("#tooltip-icon-usecase").attr('title', jQuery('input[name=help_text_for_usecase]').val());
	  });
	  jQuery('#edit-field-usecase-industry--wrapper div.panel-heading div.panel-title').after('<span id="tooltip-icon-industry"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-icon-industry").mouseover(function(){
	    jQuery("#tooltip-icon-industry").attr('title', jQuery('input[name=help_text_for_industry]').val());
	  });
	  jQuery('#edit-field-usecase-domain--wrapper div.panel-heading div.panel-title').after('<span id="tooltip-icon-domain"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-icon-domain").mouseover(function(){
		jQuery("#tooltip-icon-domain").attr('title', jQuery('input[name=help_text_for_domain]').val());
	  });	  
	  jQuery('#edit-field-offer--wrapper span.fieldset-legend').after('<span id="tooltip-icon-offer"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-icon-offer").mouseover(function(){
	    jQuery("#tooltip-icon-offer").attr('title', jQuery('input[name=help_text_for_offer]').val());
	  });	  
	  jQuery('#edit-field-have-demonstration--wrapper span.fieldset-legend').after('<span id="tooltip-live-video"><i class="fas fa-info"></i></span>');
	  jQuery("#tooltip-live-video").mouseover(function(){
	    jQuery("#tooltip-live-video").attr('title', jQuery('input[name=help_text_for_live_demo]').val());
	  });
	  			

		jQuery('#edit-field-offer label, #edit-field-usecase-domain label, #edit-field-usecase-industry label').on('mouseover', function(){
		var myObject = jQuery('input[name="help_text_for_checkboxes"]').val();
		var selectCheckbox = jQuery(this).prev('input').val();
		var selectCheckboxID = jQuery(this).attr('for');
		var result = jQuery.parseJSON(myObject);
		  jQuery.each(result, function(key, value) {
		  if ( selectCheckbox == key) {
		    jQuery('label[for="'+ selectCheckboxID + '"]').attr('title', value);
		  }
		 });
		});
	});
})(jQuery, Drupal, drupalSettings);