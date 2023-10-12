jQuery(document).ready(function(){
  /**** Add Featured usecase start here ****/
  jQuery('.spinner').hide();
  jQuery('.add-to-featured').change(function() {
    var chk_id = jQuery(this).attr('id');
    var usecase_id = jQuery(this).val();
    var data_selected = jQuery(this).attr('data-selected-value');
    var callback_url = "/modify_featured_usecase/" + usecase_id + "/add";
    var target_data_selected_val = 'yes';
    var selection_msg = 'Added into';
    if (data_selected == 'yes') {
      callback_url = "/modify_featured_usecase/" + usecase_id + "/remove";
      target_data_selected_val = 'no';
      selection_msg = 'Removed from';
    }
    
    jQuery('#spinner-' + usecase_id).show();
    
    jQuery.ajax({
      url: callback_url,
      success: function(data){ },
      complete: function(){
    	jQuery('#' + chk_id).attr('data-selected-value', target_data_selected_val);
        jQuery('#spinner-' + usecase_id).hide();
        //window.location.reload();
        alert('Usecase ' + selection_msg + ' featured list');
      },
    });
  });
  /**** Add  Featured usecase ends here ****/
  
});