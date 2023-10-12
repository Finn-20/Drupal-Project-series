/**
 * @file
 */
jQuery(document).ready(function () {
  	jQuery( ".vertical-tabs__menu" ).wrap( "<div class='tab_wrapper'></div>" );
  	
  	
  	/** 
  	 * 
  	 * Select secondary domain on selection of primary domain.
  	 * 
  	 **/
  	
  	/**************** Domain selection on page load ****************/
  	
    var defaultPrimaryDomain = jQuery("select#edit-field-primary-domain").children("option:selected").val();
    console.log("primary Domain :"+defaultPrimaryDomain);
  var callback_url = "/get_parent_primary_term/"+defaultPrimaryDomain;
    jQuery.ajax({
      url: callback_url,
      contentType: "application/html; charset=utf-8",
      success: function(data){
       var parent_term = data;
      
      if (jQuery('ul.term-reference-tree-level #edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).length > 0) {
        jQuery('ul.term-reference-tree-level #edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).prop('checked', false).filter(function(){
      return this.value === parent_term;
      }).prop('checked', true);
        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).prop('checked', true);
        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).attr('disabled','disabled');
      }
       var selectedIndustry = jQuery("select#edit-field-primary-domain").children("option:selected").val();
      
      if (jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).length > 0) {
      jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', false).filter(function(){
      return this.value === selectedIndustry;
      }).prop('checked', true);
      jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', true);
      jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).attr('disabled','disabled');
      }
      
      },
      });

  	/**************** On dropdown selection ****************/
    jQuery("select#edit-field-primary-domain").change(function(){
    var parent_term = jQuery(this).children("option:selected").val();
    
    var callback_url = "/get_parent_primary_term/"+parent_term;
    jQuery.ajax({
      url: callback_url,
      contentType: "application/html; charset=utf-8",
      success: function(data){
       var parent_term = data;
      if (jQuery('ul.term-reference-tree-level #edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).length > 0) {
                jQuery('ul.term-reference-tree-level #edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).prop('checked', false).filter(function(){
      return this.value === parent_term;
      }).prop('checked', true);
        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).prop('checked', true);
        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).addClass('checkbox-grayed');
        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).attr('disabled','disabled');
          
      }else{
        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term).addClass('checkbox-grayed');
      }
       var selectedIndustry = jQuery("select#edit-field-primary-domain").children("option:selected").val();
      if (jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).length > 0) {
                        jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', false).filter(function(){
      return this.value === selectedIndustry;
      }).prop('checked', true);
      jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', true);
      jQuery('#edit-field-usecase-domain-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).attr('disabled','disabled');
      }
      
      },
      });
  
      jQuery('#edit-field-usecase-domain input[type=checkbox]').each(function () {
        if (jQuery(this).is(':disabled')) {
          jQuery(this).prop('checked', false);
          jQuery(this).attr('disabled','disabled');
        }
      });
     // var selectedIndustry = jQuery(this).children("option:selected").val();

     
    });
  	
  	/** 
  	 * 
  	 * Select secondary industry on selection of primary industry.
  	 * 
  	 **/
  	
  	/**************** industry selection on page load ****************/
  	var defaultPrimaryIndustry = jQuery("select#edit-field-primary-industry").children("option:selected").val();
  	
	var callback_url = "/get_parent_primary_term/"+defaultPrimaryIndustry;
	  jQuery.ajax({
		  url: callback_url,
		  contentType: "application/html; charset=utf-8",
		  success: function(data){
			 var parent_term = data;
			
			if (jQuery('ul.term-reference-tree-level #edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).length > 0) {
				jQuery('ul.term-reference-tree-level #edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).prop('checked', false).filter(function(){
			return this.value === parent_term;
			}).prop('checked', true);
				jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).prop('checked', true);
				jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).attr('disabled','disabled');
			}
			 var selectedIndustry = jQuery("select#edit-field-primary-industry").children("option:selected").val();
			
			if (jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).length > 0) {
			jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', false).filter(function(){
			return this.value === selectedIndustry;
			}).prop('checked', true);
			jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', true);
			jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).attr('disabled','disabled');
			}
			
		  },
      });
	
	
  	/**************** On dropdown selection ****************/
  	jQuery("select#edit-field-primary-industry").change(function(){
	  var parent_term = jQuery(this).children("option:selected").val();
	  
	  var callback_url = "/get_parent_primary_term/"+parent_term;
	  jQuery.ajax({
		  url: callback_url,
		  contentType: "application/html; charset=utf-8",
		  success: function(data){
			 var parent_term = data;
			if (jQuery('ul.term-reference-tree-level #edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).length > 0) {
                jQuery('ul.term-reference-tree-level #edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).prop('checked', false).filter(function(){
			return this.value === parent_term;
			}).prop('checked', true);
				jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).prop('checked', true);
				jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).addClass('checkbox-grayed');
				jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).attr('disabled','disabled');
					
			}else{
				jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term).addClass('checkbox-grayed');
			}
			 var selectedIndustry = jQuery("select#edit-field-primary-industry").children("option:selected").val();
			if (jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).length > 0) {
                        jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', false).filter(function(){
			return this.value === selectedIndustry;
			}).prop('checked', true);
			jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).prop('checked', true);
			jQuery('#edit-field-usecase-industry-0-'+parent_term+'-'+parent_term+'-children-'+selectedIndustry+'-'+selectedIndustry).attr('disabled','disabled');
			}
			
		  },
      });
	
      jQuery('#edit-field-usecase-industry input[type=checkbox]').each(function () {
        if (jQuery(this).is(':disabled')) {
          jQuery(this).prop('checked', false);
          jQuery(this).attr('disabled','disabled');
        }
      });
     // var selectedIndustry = jQuery(this).children("option:selected").val();

     
    });
});
