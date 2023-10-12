jQuery(document).ready(function () {
    /**** Add to favorite button ****/

    jQuery('div.favorite-into-briefcase--block').hide();

    var selector_class = '';

    //alert(jQuery('.view-display-id-block_3 .view-content .action-flag a').length);
    if (jQuery('.view-display-id-block_3 .view-content .action-flag a').length > 0) {

      selector_class = '.view-display-id-block_3 .view-content .action-flag a';
    }
    else if (jQuery('.view-display-id-block_3 .view-content .action-unflag a').length > 0) {
      selector_class = '.view-display-id-block_3 .view-content .action-unflag a';
    }

    if (selector_class !== '') {
      var href_val = jQuery(selector_class).attr('href');
      jQuery(selector_class).removeAttr('href');
      jQuery(selector_class).removeAttr('title')
      jQuery(selector_class).removeClass('use-ajax');
      jQuery(selector_class).addClass('closed-briefcase-list');
    }

    jQuery(selector_class).click(function () {
      if (jQuery(this).hasClass('closed-briefcase-list')) {
        jQuery(this).removeClass('closed-briefcase-list');
        jQuery(this).addClass('open-briefcase-list');

        if (jQuery('div.link-flag-wrapper').hasClass('link-flag-wrapper-closed')) {
          jQuery('div.link-flag-wrapper').removeClass('link-flag-wrapper-closed')
        }
        jQuery('div.link-flag-wrapper').addClass('link-flag-wrapper-open');

        if (jQuery('div.favorite-into-briefcase--block').length > 0) {
          jQuery('div.favorite-into-briefcase--block').show();
        }
      }
      else if (jQuery(this).hasClass('open-briefcase-list')) {
        jQuery(this).removeClass('open-briefcase-list');
        jQuery(this).addClass('closed-briefcase-list');

        if (jQuery('div.link-flag-wrapper').hasClass('link-flag-wrapper-open')) {
          jQuery('div.link-flag-wrapper').removeClass('link-flag-wrapper-open')
        }
        jQuery('div.link-flag-wrapper').addClass('link-flag-wrapper-closed');

        if (jQuery('div.favorite-into-briefcase--block').length > 0) {
          jQuery('div.favorite-into-briefcase--block').hide();
        }
      }
    });

  /**** Add to favorite button Ends Here ****/

  /**** Add node to Briefcase start here ****/
  jQuery('.add-to-fav-chk').change(function () {
    var chk_id = jQuery(this).attr('id');
    var briefcase_id = jQuery(this).val();
    var node_id = jQuery(this).attr('data-node-id');
    var data_selected = jQuery(this).attr('data-selected-value');
    var data_briefcase_title = jQuery(this).attr('data-briefcase-title');
    var data_node_title = jQuery(this).attr('data-node-title');

    if (data_selected === 'yes') {
      var callback_url = "/delete_favorite_from_briefcase/" + node_id + "/" + briefcase_id;
      var alert_msg = "The content has been <span class=\"removed-red\">removed</span> from briefcase " + data_briefcase_title;
      var target_data_selected_val = 'no';
      var checked_to = false;
    }
    else {
      var callback_url = "/add_favorite_to_briefcase/" + node_id + "/" + briefcase_id;
      var alert_msg = "The content has been <span class=\"added-blue\">added</span> to briefcase " + data_briefcase_title;
      var target_data_selected_val = 'yes';
      var checked_to = true;
    }

    jQuery('#spinner-' + briefcase_id).show();
    jQuery.ajax({
      url: callback_url,
      success: function (data) {
        var para = document.createElement('DIV');
        para.innerHTML = alert_msg;
        para.setAttribute('class', 'js-flag-message');
        jQuery('div.add-to-fav-message-wrapper').html(para).fadeIn('slow');
        jQuery('div.add-to-fav-message-wrapper').delay(2000).fadeOut('slow');
      },
      complete: function () {
        if (checked_to) {
          jQuery('#' + chk_id).attr('checked', 'true');
        }
        else {
          jQuery('#' + chk_id).removeAttr('checked');
        }
        jQuery('#' + chk_id).attr('data-selected-value', target_data_selected_val);
        jQuery('#spinner-' + briefcase_id).hide();
        jQuery('div.favorite-into-briefcase--block').hide();

        if (jQuery('div.link-flag-wrapper').hasClass('link-flag-wrapper-open')) {
            jQuery('div.link-flag-wrapper').removeClass('link-flag-wrapper-open')
            jQuery('div.link-flag-wrapper').addClass('link-flag-wrapper-closed');
          }

        if (jQuery('div.flag a').hasClass('open-briefcase-list')) {
          jQuery('div.flag a').removeClass('open-briefcase-list');
          jQuery('div.flag a').addClass('closed-briefcase-list');
        }
      },
    });
  });
  /**** Add node to Briefcase ends here ****/

  /**** Add Featured Briefcase start here ****/
  jQuery('.spinner').hide();
  jQuery('.add-to-featured').change(function () {
    var chk_id = jQuery(this).attr('id');
    var briefcase_id = jQuery(this).val();
    var data_selected = jQuery(this).attr('data-selected-value');
    var callback_url = "/modify_featured_briefcases/" + briefcase_id;
    var target_data_selected_val = 'yes';

    if(jQuery(this).is(':checked')){
      jQuery('.add-to-featured').prop("checked",false);
      jQuery('#' + chk_id).prop("checked",true);
    }

    if (data_selected == 'yes') {
      callback_url = "/modify_featured_briefcases";
      target_data_selected_val = 'no';
    }

    jQuery('#spinner-' + briefcase_id).show();

    jQuery.ajax({
      url: callback_url,
      success: function (data){ },
      complete: function () {
        jQuery('#' + chk_id).attr('data-selected-value', target_data_selected_val);
        jQuery('#spinner-' + briefcase_id).hide();
        window.location.reload();
      },
    });
  });
  /**** Add  Featured Briefcase ends here ****/

});
