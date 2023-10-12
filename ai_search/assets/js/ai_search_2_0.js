jQuery(document).ready(function(){
	
	
    /** Filters Display **/

	var display_clear_link = false;

	/****************************************************************************************************************************/ 
					 				  /*** AI Experience Page starts here ***/
	/****************************************************************************************************************************/

	/** Add wrapper div for AI Experience Search and category **/
    jQuery('#views-exposed-form-ai-experience-2-0-page-1 fieldset').parent('div.views-widget').parent('div.views-exposed-widget').addClass('ai-experince-filters');
	if(jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
    	jQuery('form.bef-exposed-form fieldset.fieldgroup').wrapAll('<div class="ai-filter-container"></div>');
    	jQuery("div.ai-filter-container fieldset.fieldgroup").each(function() {
	      jQuery(this).children('legend').addClass(jQuery(this).children('legend').children('span.fieldset-legend').text().toLowerCase().replace(' ', '_'));
		  if (!jQuery(this).children('legend').hasClass('link-close') && !jQuery(this).children('legend').hasClass('all_types')) {
			jQuery(this).children('legend').addClass('link-close');
			jQuery(this).children('div.fieldset-wrapper').hide();
		  }
		  
		  var data_drupal_selector = jQuery(this).attr('data-drupal-selector');
		  var selected_filters = '';
		  var filter_count = 0;
	      jQuery.each(jQuery(this).find('input.form-checkbox:checked'), function(){
	         var selected_val = jQuery(this).val();
	         var selected_filter_text = jQuery(this).siblings('label').text();
	         selected_filters = selected_filters + '<span class="selected_filter_wrapper"><span class="selected_filter">' + selected_filter_text + '</span><span class="filter_cancel_text" data-attr-id="' + data_drupal_selector + '-' + selected_val + '">x</span></span>';
	         filter_count = filter_count+1;
	         if (display_clear_link === false) {
	           display_clear_link = true;
	         }
	      });
	      
	      // insertAfter 
	      if (selected_filters !== '' && selected_filters !== undefined) {
	    	jQuery('fieldset#' + data_drupal_selector + '--wrapper div.fieldset-wrapper').after('<div class="selected_filters_wrapper">' + selected_filters + '</div>');
	      }
	      if (filter_count > 0) {
	    	jQuery('fieldset#' + data_drupal_selector + '--wrapper span.fieldset-legend').after('<span class="filter_count">' + filter_count + '</span>');
	      }
	    });
    	
    	/** Add 'Filter' Label before Usecase or Accelerator Type filter **/
    	jQuery('fieldset#edit-field-usecase-or-accelerator-value--wrapper').wrap('<div class="filter_by_type_wrapper" />');
        jQuery('fieldset#edit-field-usecase-or-accelerator-value--wrapper').before('<div class="filter-by-type-label">TYPE</div>');
    	
        /** Add 'Refine By' Label before Taxonomy Term filters **/
        jQuery('fieldset#edit-field-usecase-industry-target-id--wrapper, fieldset#edit-field-usecase-domain-target-id--wrapper, fieldset#edit-field-usecase-aifeatures-target-id--wrapper, fieldset#edit-field-usecase-framework-target-id--wrapper, fieldset#edit-field-offer-target-id--wrapper, fieldset#edit-field-usecase-technology-target-id--wrapper').wrapAll('<div class="filters_by_terms_wrapper" />');
        jQuery('fieldset#edit-field-usecase-industry-target-id--wrapper').before('<div class="filter-by-term-label">Refine By</div>');
        
        /** Show/Hide on click of taxonomy term filters **/
        jQuery('div.ai-filter-container fieldset.fieldgroup').on('click', 'legend', function(e){
    	  if (jQuery(this).hasClass('link-close')) {
    		jQuery(this).removeClass('link-close');
    	    jQuery(this).addClass('link-open');
    	    if (jQuery(this).siblings('div.fieldset-wrapper').length > 0) {
    	      jQuery(this).siblings('div.fieldset-wrapper').slideDown('slow');
    	    }
          }
    	  else if (jQuery(this).hasClass('link-open')) {
    		jQuery(this).removeClass('link-open');
    	    jQuery(this).addClass('link-close');
    	    if (jQuery(this).siblings('div.fieldset-wrapper').length > 0) {
    	      jQuery(this).siblings('div.fieldset-wrapper').slideUp('slow');
    	    }
    	  }
    	});
    }
 
  /** Sort By Filter **/
  if (jQuery('form#views-exposed-form-search-content-2-0-my-feed-search').length > 0 ||
          jQuery('form#views-exposed-form-search-feed-content-2-0-my-feed').length > 0 || jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search').length > 0 || jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').length > 0 || jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all').length > 0 || jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
    jQuery('.form-item-sort-order').hide();
	var sort_by = jQuery('.form-item-sort-by .select-wrapper').clone();
	jQuery('.sort_by_wrapper').html(sort_by);
	jQuery('div.sort_by_wrapper #edit-sort-by--2').change(function() {
      if (jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search').length > 0) {
        jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search').submit();
      }
      else if (jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').length > 0) {
        jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').submit();
      }
      else if (jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all').length > 0) {
        jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all').submit();
      }
	  else if (jQuery('form#views-exposed-form-search-feed-content-2-0-my-feed').length > 0) {
        jQuery('form#views-exposed-form-search-feed-content-2-0-my-feed select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-feed-content-2-0-my-feed').submit();
      }
      else if (jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
        jQuery('form#views-exposed-form-ai-experience-2-0-page-1 select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-ai-experience-2-0-page-1').submit();
      }
      else if (jQuery('form#views-exposed-form-search-content-2-0-my-feed-search').length > 0) {
        jQuery('form#views-exposed-form-search-content-2-0-my-feed-search select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-content-2-0-my-feed-search').submit();
      }      
    });  
	jQuery('div.sort_by_wrapper #edit-sort-by').change(function() { 
		if (jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
			jQuery('form#views-exposed-form-ai-experience-2-0-page-1 select#edit-sort-by').val(jQuery(this).val());
			jQuery('form#views-exposed-form-ai-experience-2-0-page-1').submit();
		}	
    });  
  }
  
  if (jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
	jQuery("form[data-drupal-selector=views-exposed-form-ai-experience-2-0-page-1]").on("change", "input:checkbox", function(){
	 // jQuery("button[data-drupal-selector=edit-submit-ai-eai-use-case-search?experience-2-0]").click(); 
	  jQuery("button[data-drupal-selector=edit-submit-ai-experience-2-0]").click();
    });
  }
  
  
/****************************************************************************************************************************/ 
	  							/*** AI Experience Page Ends here ***/
/****************************************************************************************************************************/

  
  
/****************************************************************************************************************************/ 
						/*** Usecase and Accelerator Search Page starts here ***/
/****************************************************************************************************************************/

  /** Filters Default Collapsed **/
  jQuery("div.facets-title-wrapper h3.collapsible-link").each(function() {
    if (jQuery(this).hasClass('link-open')) {
      if (jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').length > 0) {
        jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').show();
      }
    }
    else if (jQuery(this).hasClass('link-close')) {
      if (jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').length > 0) {
        jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').hide();
      }
    }
  });
  
  /** Filters Default Collapsed **/
  jQuery(document).ajaxComplete(function(){
    jQuery("div.facets-title-wrapper h3.collapsible-link").each(function() {
      if (jQuery(this).hasClass('link-open')) {
        if (jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').length > 0) {
          jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').show();
        }
      }
      else if (jQuery(this).hasClass('link-close')) {
        if (jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').length > 0) {
          jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').hide();
        }
      }
    });
  });

  /** Click on Filters lable to expand and collapse **/
  jQuery('div.search_content_wrapper').on('click', 'h3.collapsible-link', function(e){
  	e.preventDefault();
    if (jQuery(this).hasClass('link-open')) {
      jQuery(this).removeClass('link-open');
      jQuery(this).addClass('link-close');
      if (jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').length > 0) {
        jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').slideUp("slow");
      }
    }
    else if (jQuery(this).hasClass('link-close')) {
      jQuery(this).removeClass('link-close');
      jQuery(this).addClass('link-open');
      if (jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').length > 0) {
        jQuery(this).parent('div.facets-title-wrapper').siblings('div.facets-items-wrapper').slideDown("slow");
      }
    }
  });
  
  
  /** Selected Filters display **/
  
  jQuery(".block-facets").each(function() {
    var selected_filters = '';
	var filter_count = 0;
	jQuery(this).find('ul.js-facets-checkbox-links input:checked').each(function() {
	  var data_drupal_facet_id = jQuery(this).parent('li.facet-item').parent('ul.js-facets-checkbox-links').attr('data-drupal-facet-id');
	  var selected_filter_text = jQuery(this).siblings('label').find('span.facet-item__value').text();
      var filter_id = jQuery(this).attr('id');
      selected_filters = selected_filters + '<span class="selected_filter_wrapper"><span class="selected_filter">' + selected_filter_text + '</span><span class="filter_cancel_text" data-attr-id="' + filter_id + '">x</span></span>';
      filter_count = filter_count+1;
      if (display_clear_link === false) {
        display_clear_link = true;
      }
	});
    // insertAfter 
	
    if (selected_filters !== '' && selected_filters !== undefined) {
      jQuery(this).find('div.facets-widget-checkbox').after('<div class="selected_filters_wrapper">' + selected_filters + '</div>');
    }
    if (filter_count > 0) {
    	jQuery(this).children('div.facets-widget-checkbox').children('div.facets-title-wrapper').children('h3.collapsible-link').after('<span class="filter_count">' + filter_count + '</span>');
    }
  });
  
  /** Cancel Filter **/
  jQuery('span.filter_cancel_text').click(function(){ 
    var cancel_filter_id = jQuery(this).attr('data-attr-id');
    jQuery('#' + cancel_filter_id).trigger('click').prop("checked", false);
  });
  
  if (display_clear_link) {
    if (jQuery('.filters-clear').hasClass('invisible-clear')) {
  	  jQuery('.filters-clear').removeClass('invisible-clear');
  	  jQuery('.filters-clear').addClass('visible-clear');
    }
  }
  else {
    if (jQuery('.filters-clear').hasClass('visible-clear')) {
	  jQuery('.filters-clear').removeClass('visible-clear');
	  jQuery('.filters-clear').addClass('invisible-clear');
    }
  }
  
  jQuery(document).ajaxComplete(function(){
	jQuery(".block-facets").once().each(function() {
	  var selected_filters = '';
	  var filter_count = 0;
	  jQuery(this).find('ul.js-facets-checkbox-links input:checked').each(function() {
	    var data_drupal_facet_id = jQuery(this).parent('li.facet-item').parent('ul.js-facets-checkbox-links').attr('data-drupal-facet-id');
	   // alert(data_drupal_facet_id);
		if(data_drupal_facet_id === 'usecase_experience_2_0' || data_drupal_facet_id === 'accelerator_experience_2_0' || data_drupal_facet_id === 'browse_all_experience_2_0'){
			var selected_filter_texts = jQuery(this).siblings('label').find('span.facet-item__value').text("AI Experience(Live Demo)");
			var selected_filter_text = "AI Experience(Live Demo)";
			//var selected_filters = selected_filter_text;
		} 
		else if(data_drupal_facet_id === 'accelerator_casestudy_2_0' || data_drupal_facet_id === 'usecase_casestudy_2_0' || data_drupal_facet_id === 'browse_all_casestudy_2_0'){
			var selected_filter_texts = jQuery(this).siblings('label').find('span.facet-item__value').text("AI Experience(Live Demo)");
			var selected_filter_text = "Case Study";
		}
		else if(data_drupal_facet_id === 'accelerator_demovideoyees_2_0' || data_drupal_facet_id === 'usecase_demovideoyees_2_0' || data_drupal_facet_id === 'browse_all_demovideoyees_2_0'){
			var selected_filter_texts = jQuery(this).siblings('label').find('span.facet-item__value').text("Demo Video");
			var selected_filter_text = "Demo Video";
		}
		else if(data_drupal_facet_id === 'accelerator_usecasevideoyes_2_0' || data_drupal_facet_id === 'usecase_usecasevideoyes_2_0' || data_drupal_facet_id === 'browse_all_usecasevideoyes_2_0'){
			var selected_filter_texts = jQuery(this).siblings('label').find('span.facet-item__value').text("Usecase Video");
			var selected_filter_text = "Usecase Video";
		}
		else{
			var selected_filter_text = jQuery(this).siblings('label').find('span.facet-item__value').text();
		}
		
	    var data_drupal_facet_item_value = jQuery(this).siblings('a.is-active').attr('data-drupal-facet-item-value');
		
        var filter_id = jQuery(this).attr('id');
        selected_filters = selected_filters + '<span class="selected_filter_wrapper"><span class="selected_filter">' + selected_filter_text + '</span><span class="filter_cancel_text" data-attr-id="' + filter_id + '">x</span></span>';
		//alert(selected_filters+filter_id);
        filter_count = filter_count+1;
        if (display_clear_link === false) {
    	  display_clear_link = true;
        }
	  });
      // insertAfter 
      if (selected_filters !== '' && selected_filters !== undefined) {
    	  if (jQuery(this).find('div.selected_filters_wrapper').length > 0) {
    		jQuery(this).find('div.facets-widget-checkbox').find('div.selected_filters_wrapper').html(selected_filters);
    	  }
    	  else {
    		jQuery(this).find('div.facets-widget-checkbox').after('<div class="selected_filters_wrapper">' + selected_filters + '</div>');
    	  }
      }
      if (filter_count > 0) {
      	jQuery(this).children('div.facets-widget-checkbox').children('div.facets-title-wrapper').children('h3.collapsible-link').after('<span class="filter_count">' + filter_count + '</span>');
      }
    });
    
	if (jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search').length > 0 || jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').length > 0 || jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all').length > 0 || jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
	    var sort_by = jQuery('.form-item-sort-by .select-wrapper').clone();
		
		jQuery('.sort_by_wrapper').html(sort_by);
		jQuery('div.sort_by_wrapper #edit-sort-by--2').change(function() {  
	      if (jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search').length > 0) {
	        jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search select#edit-sort-by--2').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-search-content-2-0-ai-use-case-search').submit();
	      }
	      else if (jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').length > 0) {
	        jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search select#edit-sort-by--2').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').submit();
	      }
	      else if (jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all').length > 0) {
	        jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all select#edit-sort-by--2').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-search-content-2-0-ai-browse-all').submit();
	      }
	      else if (jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
	        jQuery('form#views-exposed-form-ai-experience-2-0-page-1 select#edit-sort-by').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-ai-experience-2-0-page-1').submit();
	      }
	    });  
	jQuery('div.sort_by_wrapper #edit-sort-by').change(function() {
	  if (jQuery('form#views-exposed-form-ai-experience-2-0-page-1').length > 0) {
		jQuery('form#views-exposed-form-ai-experience-2-0-page-1 select#edit-sort-by').val(jQuery(this).val());
		jQuery('form#views-exposed-form-ai-experience-2-0-page-1').submit();
	  }
	});   		
	}
    /** Cancel Filter **/
    jQuery('span.filter_cancel_text').click(function(){ 
      var cancel_filter_id = jQuery(this).attr('data-attr-id');
      jQuery('#' + cancel_filter_id).trigger('click').prop("checked", false);
    }); 
    
    if (jQuery('.selected_filter_wrapper').length > 0) {
      if (jQuery('.filters-clear').hasClass('invisible-clear')) {
  	    jQuery('.filters-clear').removeClass('invisible-clear');
  	    jQuery('.filters-clear').addClass('visible-clear');
      }
    }
    else {
      if (jQuery('.filters-clear').hasClass('visible-clear')) {
	    jQuery('.filters-clear').removeClass('visible-clear');
	    jQuery('.filters-clear').addClass('invisible-clear');
      }
    }
  });
  
/****************************************************************************************************************************/ 
  								/*** Usecase and Accelerator Search Page end here ***/
/****************************************************************************************************************************/
  
  
/****************************************************************************************************************************/ 
								/*** Main Vocabularies Page start here ***/
/****************************************************************************************************************************/
  
  /** Sort By Filter **/
  if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0 || jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0 || jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
    
	jQuery('.form-item-sort-by').hide();
    jQuery('.gallery-search-filters-actions').hide();
	
    var sort_by = jQuery('.form-item-sort-by .select-wrapper').clone();
	jQuery('.sort_by_wrapper').html(sort_by);
	
	jQuery('div.sort_by_wrapper #edit-sort-by--2').change(function() {
      if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0) {
        jQuery('form#views-exposed-form-search-explore-details-search-in-industries select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-explore-details-search-in-industries').submit();
      }
      else if (jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0) {
        jQuery('form#views-exposed-form-search-explore-details-search-in-domain select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-explore-details-search-in-domain').submit();
      }
      else if (jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
        jQuery('form#views-exposed-form-search-explore-details-search-in-offers select#edit-sort-by--2').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-explore-details-search-in-offers').submit();
      }
    });
	jQuery('div.sort_by_wrapper #edit-sort-by').change(function() {
      if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0) {
        jQuery('form#views-exposed-form-search-explore-details-search-in-industries select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-explore-details-search-in-industries').submit();
      }
      else if (jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0) {
        jQuery('form#views-exposed-form-search-explore-details-search-in-domain select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-explore-details-search-in-domain').submit();
      }
      else if (jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
        jQuery('form#views-exposed-form-search-explore-details-search-in-offers select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-explore-details-search-in-offers').submit();
      }
    });
	
	/** On Checkbox selection **/
	if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0) {
	  jQuery("form#views-exposed-form-search-explore-details-search-in-industries").on("change", "input:checkbox", function(){
	    jQuery("button.gallery-search-filters-submit").click();    
	  });
	}
    else if (jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0) {
      jQuery("form[data-drupal-selector=views-exposed-form-search-explore-details-search-in-domain]").on("change", "input:checkbox", function(){
  	    jQuery("button.gallery-search-filters-submit").click();    
      });
    }
    else if (jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
      jQuery("form[data-drupal-selector=views-exposed-form-search-explore-details-search-in-offers]").on("change", "input:checkbox", function(){
  	    jQuery("button.gallery-search-filters-submit").click(); 
      });
    }
  }
  
  jQuery(document).ajaxComplete(function(){
	  /** Sort By Filter **/
	  if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0 || jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0 || jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
	    
		jQuery('.form-item-sort-by').hide();
	    jQuery('.gallery-search-filters-actions').hide();
		
	    var sort_by = jQuery('.form-item-sort-by .select-wrapper').clone();
		jQuery('.sort_by_wrapper').html(sort_by);
		
		jQuery('div.sort_by_wrapper select[data-drupal-selector=edit-sort-by]').change(function() {
	      if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0) {
	        jQuery('form#views-exposed-form-search-explore-details-search-in-industries select[data-drupal-selector=edit-sort-by]').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-search-explore-details-search-in-industries').submit();
	      }
	      else if (jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0) {
	        jQuery('form#views-exposed-form-search-explore-details-search-in-domain select[data-drupal-selector=edit-sort-by]').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-search-explore-details-search-in-domain').submit();
	      }
	      else if (jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
	        jQuery('form#views-exposed-form-search-explore-details-search-in-offers select[data-drupal-selector=edit-sort-by]').val(jQuery(this).val());
	        jQuery('form#views-exposed-form-search-explore-details-search-in-offers').submit();
	      }
	    });
		
		/** On Checkbox selection **/
		if (jQuery('form#views-exposed-form-search-explore-details-search-in-industries').length > 0) {
		  jQuery("form#views-exposed-form-search-explore-details-search-in-industries").on("change", "input:checkbox", function(){
		    jQuery("button.gallery-search-filters-submit").click();    
		  });
		}
	    else if (jQuery('form#views-exposed-form-search-explore-details-search-in-domain').length > 0) {
	      jQuery("form[data-drupal-selector=views-exposed-form-search-explore-details-search-in-domain]").on("change", "input:checkbox", function(){
	  	    jQuery("button.gallery-search-filters-submit").click();    
	      });
	    }
	    else if (jQuery('form#views-exposed-form-search-explore-details-search-in-offers').length > 0) {
	      jQuery("form[data-drupal-selector=views-exposed-form-search-explore-details-search-in-offers]").on("change", "input:checkbox", function(){
	  	    jQuery("button.gallery-search-filters-submit").click(); 
	      });
	    }
	  }
  });
  
  

/****************************************************************************************************************************/ 
								/*** Main Vocabularies Page end here ***/
/****************************************************************************************************************************/

});
