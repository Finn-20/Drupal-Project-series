(function ($, Drupal) {
    //'use strict';
   Drupal.behaviors.privacyPolicyLinked = {
    attach: function (context) {
      Drupal.Ajax.prototype.setProgressIndicatorFullscreen = function () {
        this.progress.element = $('<div class="ajax-progress ajax-progress-fullscreen">&nbsp;</div>');
        $('body .pager').before(this.progress.element);
      };
    }
  };
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {

       if(jQuery('div').hasClass('add-to-briefcase')){

            jQuery('div.views-row .add-to-briefcase', context).once('div.views-row .add-to-briefcase').each(function () {
               //console.log('Hi');

                var nid = jQuery(this).attr('id');

                jQuery('#' + nid + ' div.favorite-into-briefcase--block').hide();

               var selector_class = '';
               if (jQuery(this).find('div.view-display-id-block_3 .view-content .js-flag-favourites-' + nid + '.action-flag a').length > 0) {
                  selector_class = '.view-display-id-block_3 .view-content .js-flag-favourites-' + nid + '.action-flag a';
                }
                else if (jQuery(this).find('div.view-display-id-block_3 .view-content .js-flag-favourites-' + nid + '.action-unflag a').length > 0) {
                  selector_class = '.view-display-id-block_3 .view-content .js-flag-favourites-' + nid + '.action-unflag a';
                }

                if (selector_class !== '') {
                    var href_val = jQuery(selector_class).attr('href');
                    jQuery(selector_class).removeAttr('href');
                    jQuery(selector_class).removeAttr('title')
                    jQuery(selector_class).removeClass('use-ajax');
                    jQuery(selector_class).addClass('closed-briefcase-list');
                }

                jQuery(context).on('mouseup',  selector_class, function (e) {
                   if (jQuery(this).hasClass('closed-briefcase-list')) {
                        jQuery(this).removeClass('closed-briefcase-list').addClass('open-briefcase-list');

                       if (jQuery('div.link-flag-wrapper').hasClass('link-flag-wrapper-closed')) {
                           jQuery('div.link-flag-wrapper').removeClass('link-flag-wrapper-closed')
                        }
                        jQuery('div.link-flag-wrapper').addClass('link-flag-wrapper-open');

                        if (jQuery('#' + nid + ' div.favorite-into-briefcase--block').length > 0) {
                           jQuery('#' + nid + ' div.favorite-into-briefcase--block').show();
                        }
                    }
                    else if (jQuery(this).hasClass('open-briefcase-list')) {
                        jQuery(this).removeClass('open-briefcase-list').addClass('closed-briefcase-list');

                        if (jQuery('div.link-flag-wrapper').hasClass('link-flag-wrapper-open')) {
                          jQuery('div.link-flag-wrapper').removeClass('link-flag-wrapper-open')
                        }
                        jQuery('div.link-flag-wrapper').addClass('link-flag-wrapper-closed');

                        if (jQuery('#' + nid + ' div.favorite-into-briefcase--block').length > 0) {
                          jQuery('#' + nid + ' div.favorite-into-briefcase--block').hide();
                        }
                    }
                });
            /**** Add to favorite button Ends Here ****/

                /**** Add node to Briefcase start here ****/
                jQuery('.add-to-fav-chk-' + nid ).change(function () {

                    var chk_id = jQuery(this).attr('id');

                    var briefcase_id = jQuery(this).val();
                    var node_id = jQuery(this).attr('data-node-id');
                    //alert(node_id);
                    var data_selected = jQuery(this).attr('data-selected-value');
                    var data_briefcase_title = jQuery(this).attr('data-briefcase-title');
                    var data_node_title = jQuery(this).attr('data-node-title');

                    if (data_selected === 'yes') {
                        console.log('Inside yes');
                      var callback_url = "/delete_favorite_from_briefcase/" + node_id + "/" + briefcase_id;
                      var alert_msg = "The content has been <span class=\"removed-red\">removed</span> from briefcase " + data_briefcase_title;
                      var target_data_selected_val = 'no';
                      var checked_to = false;
                      //console.log(callback_url+"="+alert_msg);
                    }
                    else {
                        console.log('Inside no');
                      var callback_url = "/add_favorite_to_briefcase/" + node_id + "/" + briefcase_id;
                      var alert_msg = "The content has been <span class=\"added-blue\">added</span> to briefcase " + data_briefcase_title;
                      var target_data_selected_val = 'yes';
                      var checked_to = true;
                      //console.log(callback_url+"="+alert_msg);
                    }

                    jQuery('#spinner-' + briefcase_id + node_id ).show();
                    jQuery.ajax({
                        url: callback_url,
                        success: function (data) {
                            var para = document.createElement('DIV');
                            para.innerHTML = alert_msg;

                            para.setAttribute('class', 'js-flag-message');
                            //jQuery('div.add-to-fav-message-wrapper#current_msg_'+node_id).html(para).fadeOut(5000);
                            jQuery('div.add-to-fav-message-wrapper#current_msg_' + node_id).html(para).fadeIn('slow');
                            jQuery('div.add-to-fav-message-wrapper#current_msg_' + node_id).delay(1000).fadeOut('fast');
                            console.log(jQuery('div.add-to-fav-message-wrapper#current_msg_' + node_id).html(para));
                        },
                        complete: function () {
                            if (checked_to) {
                              jQuery('input#' + chk_id).attr('checked', 'true');
                            }
                            else {
                              jQuery('input#' + chk_id).removeAttr('checked');
                            }
                            jQuery('input#' + chk_id).attr('data-selected-value', target_data_selected_val);
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
                jQuery(".add-to-briefcase .flag a").attr('title', 'Add to briefcase');
              /**** Add node to Briefcase ends here ****/
            });

        }else{
                //console.log('this is from else Hi');
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

            jQuery(selector_class).once(selector_class).on('mouseup', function () {
                if (jQuery(this).hasClass('closed-briefcase-list')) {
                    //console.log("inside click");
                    jQuery(this).removeClass('closed-briefcase-list');
                    jQuery(this).addClass('open-briefcase-list');
                    //console.log(this);
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
                        jQuery('div.add-to-fav-message-wrapper').delay(500).fadeOut('slow');
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

        }
      }
    }

     Drupal.behaviors.formBehavior = {
       attach: function (context, settings) {

           jQuery('div.views-row .stars-rating-row .star-rating form', context).once('div.views-row .stars-rating-row .star-rating form').each(function (i) {
                  var timestamp = Date.now() + Math.random();
                jQuery(this).removeAttr('class');
                var newClass = "fivestar-form-" + timestamp;
                jQuery(this).attr('class', newClass);
                var item = jQuery(this).attr('class');
                //console.log(item);
                jQuery(this).removeAttr('data-drupal-selector');
                jQuery(this).attr('data-drupal-selector', newClass);

                jQuery(this).find('input[name= "form_token"]').removeAttr('data-drupal-selector');
                var newDDS = "edit-fivestar-form-" + timestamp;
                jQuery(this).find('input[name= "form_token"]').attr('data-drupal-selector', newDDS);

                jQuery(this).find('input[name="form_id"]').removeAttr('data-drupal-selector');
                var newDDSelector = "edit-fivestar-form-" + timestamp;
                jQuery(this).find('input[name="form_id"]').attr('data-drupal-selector', newDDSelector);
                jQuery(this).find('input[name="form_id"]').removeAttr('value');
                var newVal = "fivestar_form_" + '_' + timestamp;
                jQuery(this).find('input[name="form_id"]').attr('value', newVal);

                var content_id = jQuery(this).closest('div.content').attr("id");
                //console.log(content_id);
                var callbackurl = "/usecase_rating" + '/' + content_id;
                //console.log(callbackurl);
                //getting average number of votes here
                var avgval = jQuery(this).find('.form-item-vote.form-type-select .description.help-block .average-rating > span').text();
            //     console.log(avgval);
        //         console.log('check');
                var avgval = parseFloat(avgval);
                var newavgval = avgval.toPrecision(2);
                var newHTML = "<span class='original-avg-rating'>" + newavgval + "</span><span class='new-avg-vote'>" + avgval + "</span>";
                jQuery(this).find('.form-item-vote.form-type-select .description.help-block .average-rating > span').html(newHTML);

                //getting total views here
                var total_vote = jQuery(this).find('.form-item-vote.form-type-select .description.help-block .total-votes').text();
                var str = total_vote;
                var result = str.match(/\d+/);
                //var patt1 = /[0-9]/g;

                //var result = str.match(patt1);
                var vote_cnt = result.toString();
            //    console.log(vote_cnt);
                 jQuery(this).find('select').on('change', function () {
                     var rate_value = jQuery(this).val();
                     //console.log(rate_value);

                     //set avg rate value here
                     avgval = rate_value;

                     //console.log(avgval);
                     var post_url = callbackurl + '/' + rate_value;
                     //console.log(post_url);
                     var avg_rating;
                     var vote_cnt;
                    // var votevalues;
                     jQuery.ajax({
                         url: post_url,
                         method: "POST",
                         async: false,
                         dataType: "json",
                         success: function (data) {
                             avg_rating = data.averg_count;
                             vote_cnt = data.vote_count;
                         }

                     });
                    var avg_htm_text = "<span class='updated-avg-vote'>" + avg_rating.toPrecision(2) + "</span><span class='todigit-avg-vote'>" + avg_rating + "</span>";
                    jQuery(this).closest('.form-item-vote.form-type-select').find('.help-block .fivestar-summary .average-rating span').html(avg_htm_text);
                    var total_votes_htm = "<span class='total-votes-new'>" + '( ' + vote_cnt + " votes )" + "</span>"
                    jQuery(this).closest('.form-item-vote.form-type-select').find('.help-block .fivestar-summary .total-votes').html( total_votes_htm);

                });
           });
        }
    }

    Drupal.behaviors.tooltipBehavior = {
        attach: function (context, settings) {
        jQuery('div.views-row .stars-rating-row' , context).once('div.views-row .stars-rating-row').each(function () {
            var content_id = jQuery(this).closest('div.content').attr("id");

            var callbackurl = "/tooltip_rating" + '/' + content_id;
            var avgval = jQuery(this).find('.form-item-vote.form-type-select .description.help-block .average-rating > span').text();
            if(avgval != 0 ) {
               jQuery(this).find('.form-item-vote.form-type-select .description.help-block .fivestar-summary.fivestar-summary-average-count').append("<i class='fas fa-angle-down'></i>");
            }
            jQuery(this).find('.fivestar-summary.fivestar-summary-average-count').append('<div class="tooltip-main-container"><div id="loader"><div class="triangle"></div></div></div>');
            jQuery(this).find('.description.help-block .fivestar-summary.fivestar-summary-average-count i').on('mousedown', function () {
                var post_url = callbackurl;
                //console.log(post_url);
                if(jQuery('.tooltip-container').length > 0 ) {
                    //console.log('inside if to check existance');
                   jQuery('.tooltip-container').remove();
                }

                var votevalues;
                jQuery.ajax({
                    url: post_url,
                    method: "POST",
                    //async: false,
                    dataType: "json",
                    beforeSend: function () {
                        var url1 = 'div#' + content_id + ' ' + '.description.help-block .fivestar-summary.fivestar-summary-average-count';
                        jQuery(url1).find('.tooltip-main-container div#loader').show();
                    },
                    success: function (data) {
                        votevalues = data.vote_values;
                        console.log(votevalues);
                        ratingUpdate(votevalues);
                    },
                    complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                       var url2 = 'div#' + content_id + ' ' + '.description.help-block .fivestar-summary.fivestar-summary-average-count';
                       console.log(url2);
                       jQuery(url2).find('.tooltip-main-container div#loader').hide();
                    },

                });
                function ratingUpdate(data){
                    var votevalues = data;
                    console.log(votevalues);
                    var val_100 = 0; var val_80 = 0; var val_60 = 0; var val_40 = 0; var val_20 = 0;
                var vote_cnt = votevalues.length;
                var i;
                 for (i = 0; i <= votevalues.length; i++) {
                    if (votevalues[i] == '100') {
                         val_100++;
                     }
                    else if (votevalues[i] == '80') {
                         val_80++;
                     }
                    else if (votevalues[i] == '60') {
                         val_60++;
                     }
                     else if (votevalues[i] == '40') {
                        val_40++;
                    }
                    else if (votevalues[i] == '20') {
                         val_20++;
                     }
                 }
                if (val_100 == 1) {
                     var fivenvotes = 'vote';
                } else {
                     var fivenvotes = 'votes';
                }
                if (val_80 == 1) {
                     var fournvotes = 'vote';
                } else {
                     var fournvotes = 'votes';
                }
                if (val_60 == 1) {
                     var threenvotes = 'vote';
                } else {
                     var threenvotes = 'votes';
                }
                if (val_40 == 1) {
                     var twonvotes = 'vote';
                } else {
                     var twonvotes = 'votes';
                }
                if (val_20 == 1) {
                    var onenvotes = 'vote';
                } else {
                    var onenvotes = 'votes';
                }
                if (val_100 == 0) {
                    fivenvotes = '';
                    val_100 = '';
                }
                if (val_80 == 0) {
                     fournvotes = '';
                     val_80 = '';
                }
                if (val_60 == 0) {
                     threenvotes = '';
                     val_60 = '';
                }
                if (val_40 == 0) {
                     twonvotes = '';
                     val_40 = '';
                }
                if (val_20 == 0) {
                     onenvotes = '';
                     val_20 = '';
                }
                //console.log(nvotes);
                var percent_100 = val_100 * 100 / vote_cnt;
                var percent_80 = val_80 * 100 / vote_cnt;
                var percent_60 = val_60 * 100 / vote_cnt;
                var percent_40 = val_40 * 100 / vote_cnt;
                var percent_20 = val_20 * 100 / vote_cnt;

                var maxVal = Math.max(percent_100,percent_80,percent_60,percent_40,percent_20);
                var fiveStarWidth = 100 - percent_100;
                var fourStarWidth = 100 - percent_80;
                var threeStarWidth = 100 - percent_60;
                var twoStarWidth = 100 - percent_40;
                var oneStarWidth = 100 - percent_20;
                var highestrate;

                var parentDiv = 'div#' + content_id + ' ' + '.star-rating form';
                var tooltip_html = "<div class='tooltip-container'><div class='five-star'><div class='progress hundred'><div class='progress-bar'></div><span class='middle-layer'></span></div><span class='label'>5 Star</span></div><div class='four-star'><div class='progress eighty'><div class='progress-bar'></div><span class='middle-layer'></span></div><span class='label'>4 Star</span></div><div class='three-star'><div class='progress sixty'><div class='progress-bar'></div><span class='middle-layer'></span></div><span class='label'>3 Star</span></div><div class='two-star'><div class='progress fourty'><div class='progress-bar'></div><span class='middle-layer'></span></div><span class='label'>2 Star</span></div><div class='one-star'><div class='progress twenty'><div class='progress-bar'></div><span class='middle-layer'></span></div><span class='label'>1 Star</span></div></div>";
                jQuery(parentDiv).find('.form-item-vote.form-type-select .help-block .fivestar-summary .tooltip-main-container').append(tooltip_html);
                console.time('.tooltip-container');
                jQuery('.hundred .progress-bar').html('<span>' + val_100 + ' ' + fivenvotes + ' </span>');
                jQuery('.eighty .progress-bar').html('<span>' + val_80 + ' ' + fournvotes + ' </span>');
                jQuery('.sixty .progress-bar').html('<span>' + val_60 + ' ' + threenvotes + ' </span>');
                jQuery('.fourty .progress-bar').html('<span>' + val_40 + ' ' + twonvotes + ' </span>');
                jQuery('.twenty .progress-bar').html('<span>' + val_20 + ' ' + onenvotes + ' </span>');
                jQuery('.five-star span.middle-layer').css('width',fiveStarWidth + '%');
                jQuery('.four-star span.middle-layer').css('width',fourStarWidth + '%');
                jQuery('.three-star span.middle-layer').css('width',threeStarWidth + '%');
                jQuery('.two-star span.middle-layer').css('width',twoStarWidth + '%');
                jQuery('.one-star span.middle-layer').css('width',oneStarWidth + '%');
                }
                //console.log(votevalues);

            });
        });
    }
}

})(jQuery, Drupal);
