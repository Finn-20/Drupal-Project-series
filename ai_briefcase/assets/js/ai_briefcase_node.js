jQuery(document).ready(function () {
    jQuery('div.editable-title-wrapper').hide();
    jQuery('div.editable-description-wrapper').hide();

    /******* Briefcase Title show/hide function starts here *******/

    jQuery('a.edit-title-link').click(function () {
        jQuery('div.non-editable-title-wrapper').hide();
        jQuery('div.editable-title-wrapper').show();
        jQuery('input#edit-briefcase-title').focus();
    });
    jQuery('.cancel-edit').click(function () {
        jQuery('div.non-editable-title-wrapper').show();
        jQuery('div.editable-title-wrapper').hide();
    });

    /******* Briefcase Title show/hide function ends here *******/

    /******* Briefcase Description show/hide function starts here *******/

    jQuery('a.edit-description-link').click(function () {
        jQuery('div.non-editable-description-wrapper').hide();
        jQuery('div.editable-description-wrapper').show();
        jQuery('textarea#edit-briefcase-body').focus();
    });
    jQuery('.cancel-element').click(function () {
        jQuery('div.non-editable-description-wrapper').show();
        jQuery('div.editable-description-wrapper').hide();
    });

    /******* Briefcase Description show/hide function ends here *******/

    /******* Briefcase Title/Description Update Starts here *******/

    jQuery('.save-element').click(function () {
        var element_data_value = '';
        var data_element = jQuery(this).attr('data-element-attribute');
        if (data_element === 'title') {
            element_data_value = jQuery('input#edit-briefcase-title').val();
            var spinner_id = 'spinner-title';
        }
        else if (data_element === 'description') {
            element_data_value = jQuery('textarea#edit-briefcase-body').val();
            var spinner_id = 'spinner-description';
        }

        var briefcase_id = jQuery(this).attr('data-briefcase-id');
        var callback_url = "/modify_briefcase";
        var formData = {briefcase_id:briefcase_id,briefcase_element:data_element,briefcase_data:element_data_value};

        jQuery('#' + spinner_id).show();
        jQuery.ajax({
          url: callback_url,
          type: 'POST',
          data : formData,
          success: function (data, textStatus, jqXHR) {
              bootbox.alert(data);
          },
          error: function (jqXHR, textStatus, errorThrown) {
              jQuery('#' + spinner_id).hide();
          },
          complete: function () {
            if (data_element === 'title') {
                jQuery('div.non-editable-title-wrapper h1.page-header span:first').html(element_data_value);
                jQuery('div.non-editable-title-wrapper').show();
                jQuery('div.editable-title-wrapper').hide();
            }
            else if (data_element === 'description') {
                jQuery('div.non-editable-description-wrapper div.briefcase-description').html(element_data_value);
                jQuery('div.non-editable-description-wrapper').show();
                jQuery('div.editable-description-wrapper').hide();
            }
            jQuery('#' + spinner_id).hide();
          },
        });
    });

    /******* Briefcase Title/Description Update ends here *******/

    /******* Remove usecase from Briefcase start here *******/

    jQuery(document).ajaxComplete(function () {
        var views_classes = jQuery('div.view-display-id-briefcase_content_block').attr('class');
        var view_dom_id = jQuery.grep(views_classes.split(" "), function (v, i) {
           return v.indexOf('js-view-dom-id-') === 0;
        }).join();
        jQuery('a.remove-usecase').once().click(function () {
            var briefcase_id = jQuery(this).attr('data-briefcase-id');
            var node_id = jQuery(this).attr('data-node-id');
            var data_briefcase_title = jQuery(this).attr('data-briefcase-title');
            var data_node_title = jQuery(this).attr('data-node-title');

            var confirmation_msg = 'Are you sure you want to remove "' + data_node_title + '" from  briefcase "' + data_briefcase_title + '"?<br/><br/>This action can not be undone. Press "OK" to continue or press "Cancel"';
           jQuery(this).closest('div.slideup-content-wrapper').css('height', '100%');
            bootbox.confirm(confirmation_msg, function (result) {
              if (result) {
                  var callback_url = "/delete_favorite_from_briefcase/" + node_id + "/" + briefcase_id;
                  jQuery('span#spinner-' + node_id).show();
                  jQuery.ajax({
                      url: callback_url,
                      success: function (data) {
                        /*var para = document.createElement('DIV');
                        para.innerHTML = alert_msg;
                        para.setAttribute('class', 'js-flag-message');
                        jQuery('div.add-to-fav-message-wrapper').html(para).fadeIn('slow');
                        jQuery('div.add-to-fav-message-wrapper').delay(1000).fadeOut('slow');*/
                      },
                      complete: function () {
                          jQuery('span#spinner-' + node_id).hide();
                          jQuery('.' + view_dom_id).trigger('RefreshView');
                          window.location.reload();
                      },
                    });
              }
              else {
                jQuery(this).closest('div.slideup-content-wrapper').css('height', '');
              }
            });
        });
    });
    /******* Remove usecase from Briefcase start here *******/
});
