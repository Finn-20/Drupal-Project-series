
(function ($, Drupal) {
	var initialized;
	function deleteTagsOfCategory(category,checked_status){
		$.ajax({
			type: "POST",
			url: drupalSettings.ai_contribute_usecase_setting.callbackUrl,
			data: {'uniqid':drupalSettings.ai_contribute_usecase_setting.uniqid,'type':category,'checked_status':checked_status},
			dataType: 'json',
			success: function(data){ 
				console.log(data);
			},
			complete: function(data) {
			}
		});
	}
	function otherFieldHideShow(checkbox_value,event){
			switch(checkbox_value){
				case '521': //other partner
					$("#edit-other-partner").appendTo(".form-item-field-usecase-technology-521");
					$("#edit-other-partner").show();
					deleteTagsOfCategory(1,event);
					if(!event){
						$("#edit-other-partner").hide();
					}
				break;

				case '439': // . feature
					$("#edit-other-feature").appendTo(".form-item-field-usecase-aifeatures-439");
					$("#edit-other-feature").show();
					deleteTagsOfCategory(2,event);
					if(!event){
						$("#edit-other-feature").hide();
					}
				break;

				case '440': // . framework
					$("#edit-other-framework").appendTo(".form-item-field-usecase-framework-440");
					$("#edit-other-framework").show();
					deleteTagsOfCategory(3,event);
					if(!event){
						$("#edit-other-framework").hide();
					}
				break;
			}
		}
	function otherFieldInit() {
	    if (!initialized) {
	    initialized = true;
	      var show_all_link = drupalSettings.ai_contribute_usecase_setting.links_visibility.all;
			if(show_all_link){
				otherFieldHideShow('521',drupalSettings.ai_contribute_usecase_setting.links_visibility.partner_link);
				otherFieldHideShow('439',drupalSettings.ai_contribute_usecase_setting.links_visibility.feature_link);
				otherFieldHideShow('440',drupalSettings.ai_contribute_usecase_setting.links_visibility.framework_link);
			}else{
				// hide on page load
				$("#edit-other-partner").hide();
				$("#edit-other-feature").hide();
				$("#edit-other-framework").hide();
			}
	    }
  }
  Drupal.behaviors.other_field = {
    attach: function(context, settings) {
    	otherFieldInit();
		$("input.form-checkbox").off('click').on('click',function(){
			var checkbox_value = $(this).val();
			if($(this).prop("checked") == true){
				otherFieldHideShow(checkbox_value,true);
			}else{
				otherFieldHideShow(checkbox_value,false);
			}
		}); 
    }
  };
})(jQuery, Drupal);
