jQuery(document).ready(function(){ 
jQuery(".checklist_action_wrapper #edit-accpidea").mouseover(function(){ 
        jQuery(".checklist_action_wrapper #edit-accpidea").attr('title', 'Accept idea for further Asset implementation');
    });
    jQuery(".checklist_action_wrapper #edit-save").mouseover(function(){
        jQuery(".checklist_action_wrapper #edit-save").attr('title', 'Add dummy txt , priya/nitin will provide');
    });
   
	jQuery('div.subcategory-wrapper').each(function () {
	  var target_class = jQuery(this).attr('data-class');
	  var div_height = jQuery(this).height();
	 // alert(div_height);
	  jQuery('div.' + target_class).css('height', div_height);
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer').css('height', (div_height-52));
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer.full-height').css('height', '96%');
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer.full-height').css('padding-top', '2%');
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer.full-height').css('padding-bottom', '2%');
	});
});
