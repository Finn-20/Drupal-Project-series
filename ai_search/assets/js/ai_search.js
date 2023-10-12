jQuery(document).ready(function(){
  /** Default value of search textbox **/
  function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  }
  
  if (jQuery('form#search-block-form--2 input[name="keys"]').length > 0) {
    var key_value = getUrlVars()["keys"];
    if (key_value !== '' || key_value != undefined) {
      jQuery('form#search-block-form--2 input[name="keys"]').attr("value", key_value.replace('+', ' '));
    }
  }

  /** Default value of search textbox **/
  
  /** Filters Display **/
  if (jQuery("#itemListClone").length > 0) {
    jQuery("#itemListClone").empty();
  }
  jQuery('ul.js-facets-checkbox-links input:checked').each(function() {
    var item = (jQuery(this).siblings('label').find('span.facet-item__value').text());
    jQuery("#itemListClone").append('<p><a href="#" title="' + item + '">' + item + '</a></p>');
  });
  
  jQuery(document).ajaxComplete(function(){
	if (jQuery("#itemListClone").length > 0) {
	  jQuery("#itemListClone").empty();
	}
    jQuery('ul.js-facets-checkbox-links input:checked').each(function() {
      var item = jQuery(this).siblings('label').find('span.facet-item__value').text();
      var filter_id = jQuery(this).attr('id');
      if (jQuery("#itemListClone").length > 0) {
    	jQuery("#itemListClone").append('<p><span class="filter-text"><a title="' + item + '">' + item + '</a></span><span class="filter_cancel_wrapper"><a class="filter_cancel_text" title="Remove this filter" data-attr-id="' + filter_id + '">X</a></p>');
      }
    });
    
    /** Cancel Filter **/
    jQuery('a.filter_cancel_text').click(function(){
      var cancel_filter_id = jQuery(this).attr('data-attr-id');
      jQuery('#' + cancel_filter_id).trigger('click').prop("checked", false);
    });
  });
  
  
  
  /** Filters Display **/

  /** Add wrapper div for AI Experience Search and category **/
  
  jQuery('#views-exposed-form-search-content-page-use-case input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Search</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
  jQuery('#views-exposed-form-search-content-accelerators input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Search</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
  jQuery('#views-exposed-form-search-content-page-asset-search input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Search</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
  jQuery('#views-exposed-form-search-content-page-ai-exp-usecase input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Search</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
  if (jQuery('div#ai-exp-search-wrapper').length <= 0) {
    jQuery('#views-exposed-form-search-content-page-use-case input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Searbbbch</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
    jQuery('#views-exposed-form-search-content-accelerators input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Searbbbch</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
    jQuery('#views-exposed-form-search-content-page-asset-search input').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Searbbbch</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');

    
    jQuery('.form-item-aidemofilter').wrap('<div id="ai-exp-search-wrapper"/>');
    jQuery('#ai-exp-search-wrapper').append('<div class="ai-exp-morefilter"><a class="more-filters-button more-filters-closed">More Filters</a></div><div style="clear: both;"></div>');
    jQuery('#ai-exp-search-wrapper #edit-aidemofilter').after('<span class="input-group-btn"><button type="submit" value="Search" class="button js-form-submit form-submit btn-primary btn icon-only" name=""><span class="sr-only">Searbbbch</span><span class="icon glyphicon glyphicon-search" aria-hidden="true"></span></button></span>');
    jQuery('fieldset.bef-select-as-checkboxes-fieldset').parent('div.views-widget').parent('div.views-exposed-widget').addClass('ai-experince-filters');
    jQuery('.ai-experince-filters').wrapAll('<div class="ai-filter-container"></div>');  
   
    jQuery("div.ai-experince-filters").each(function() {
      var expose_widget_id = 'ai-experince-zone-filter-'+jQuery(this).find('span.panel-title').text().toLowerCase();
      jQuery(this).attr('id', expose_widget_id.replace(' ', '-'));
    });
    jQuery('#ai-experince-zone-filter-industry, #ai-experince-zone-filter-framework').wrapAll('<div class="ai-left-filters" />');
    jQuery('#ai-experince-zone-filter-domain, #ai-experince-zone-filter-offer').wrapAll('<div class="ai-middle-filters" />');
    jQuery('#ai-experince-zone-filter-ai-features, #ai-experince-zone-filter-partner').wrapAll('<div class="ai-right-filters" />');
    jQuery('<div style="clear: both;"></div>').insertAfter(jQuery('.ai-right-filters'));
  }

  if (jQuery('div#ai-experince-zone-filter-framework').find('legend.panel-heading').hasClass('show') == false) {
    jQuery('div#ai-experince-zone-filter-framework').find('legend.panel-heading').addClass('show');
	jQuery('div#ai-experince-zone-filter-framework').find('div.panel-body').hide();
  }
  if (jQuery('div#ai-experince-zone-filter-offer').find('legend.panel-heading').hasClass('show') == false) {
	jQuery('div#ai-experince-zone-filter-offer').find('legend.panel-heading').addClass('show');
	jQuery('div#ai-experince-zone-filter-offer').find('div.panel-body').hide();
  }
  if (jQuery('div#ai-experince-zone-filter-partner').find('legend.panel-heading').hasClass('show') == false) {
	jQuery('div#ai-experince-zone-filter-partner').find('legend.panel-heading').addClass('show');
    jQuery('div#ai-experince-zone-filter-partner').find('div.panel-body').hide();
  }
	
  /** Manage Filters Button Click **/
  if (jQuery('div.search_content_top').length > 0) {
    jQuery('div.search_content_top').hide();
  }
  else if (jQuery('div.ai-filter-container').length > 0) {
    jQuery('div.ai-filter-container').hide();
  }
  jQuery('a.more-filters-button').click(function() {
    if (jQuery(this).hasClass('more-filters-closed')) {
      jQuery(this).removeClass('more-filters-closed');
      jQuery(this).addClass('more-filters-open');
      if (jQuery('div.search_content_top').length > 0) {
        jQuery('div.search_content_top').slideDown("slow");
      }
      else if (jQuery('div.ai-filter-container').length > 0) {
        jQuery('div.ai-filter-container').slideDown("slow");
      }
      
      var category_class = "";
      var offer_class = "";
      if (jQuery('.block-facet-blockuse-case-category').length > 0) {
        category_class = "block-facet-blockuse-case-category";
        offer_class = "block-facet-blockoffer";
      }
      else if (jQuery('.block-facet-blockuse-case-page-category').length > 0) {
        category_class = "block-facet-blockuse-case-page-category";
        offer_class = "block-facet-blockuse-case-offer";
      }
      else if (jQuery('.block-facet-blockaccelerators-category').length > 0) {
        category_class = "block-facet-blockaccelerators-category";
        offer_class = "block-facet-blockaccelerators-offer";
      }
      else if (jQuery('.block-facet-blockasset-search-category').length > 0) {
        category_class = "block-facet-blockasset-search-category";
        offer_class = "facet-blockasset-offer";
      }
      
      if (category_class !== "" && offer_class !== "") {
    	var category_section_height = jQuery('.' + category_class + ' div.facets-items-wrapper').height();
        var offer_section_height = jQuery('.' + offer_class + ' div.facets-items-wrapper').height();
        if (category_section_height > offer_section_height) {
          jQuery('.' + category_class + ' div.facets-items-wrapper').height(category_section_height);
          jQuery('.' + offer_class + ' div.facets-items-wrapper').height(category_section_height);
        }
        else {
          jQuery('.' + category_class + ' div.facets-items-wrapper').height(offer_section_height);
          jQuery('.' + offer_class + ' div.facets-items-wrapper').height(offer_section_height);
        }
      }
    }
    else if (jQuery(this).hasClass('more-filters-open')) {
      jQuery(this).removeClass('more-filters-open');
      jQuery(this).addClass('more-filters-closed');
      if (jQuery('div.search_content_top').length > 0) {
        jQuery('div.search_content_top').slideUp("slow");
      }
      else if (jQuery('div.ai-filter-container').length > 0) {
        jQuery('div.ai-filter-container').slideUp("slow");
      }
    }  
  });
 
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
  
  
  jQuery("form[data-drupal-selector=views-exposed-form-ai-experience-zone-page-1]").on("change", "input:checkbox", function(){
	jQuery("button[data-drupal-selector=edit-submit-ai-experience-zone]").click();    
  });
  /** Added wrpper div for Aiexeriance Search and category **/
  
});
