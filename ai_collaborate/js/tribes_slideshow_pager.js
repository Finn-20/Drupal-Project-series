(function ($, Drupal, drupalSettings) {

  $(document).on('click', '.collaborate-page-category-container', function (event) {

    jQuery('<div class="pager_in_pager_wrapper"><span id="prev_pager"></span><span id="next_pager"></span></div>').insertAfter('.views_slideshow_pager_field');

    jQuery(".views_slideshow_cycle_slide .views-row").each(function () {
      jQuery(this).children().wrapAll('<a class="wrap-href-call-cycle" />');
      var data_href_link = jQuery(this).find('.use_case_accelerator_title a').prop('href');
      jQuery(this).children(".wrap-href-call-cycle").attr('href', data_href_link);
      jQuery(this).children(".wrap-href-call-cycle").attr('target', '_blank');
    });
  });
  if (jQuery('.views_slideshow_cycle_main_tribes_asset_listing-block_1').length > 0) {
    if (jQuery('.views-slideshow-controls-bottom').length > 0) {
      var fullHeight = jQuery('#widget_pager_bottom_tribes_asset_listing-block_1').height();
      if (jQuery('.views_slideshow_pager_field_item').length > 0) {
        var innerDivMaxHeight = 0;
        jQuery(".views_slideshow_pager_field_item").each(function () {
          var currentDivHeight = jQuery(this).height();
          if (currentDivHeight > innerDivMaxHeight) {
            innerDivMaxHeight = currentDivHeight;
          }
        });
        if (innerDivMaxHeight > 0) {
          innerDivMaxHeight = innerDivMaxHeight + 10;
          jQuery('.views_slideshow_pager_field').height(innerDivMaxHeight);
        }
      }
    }
  }

  Drupal.viewsSlideshowPager = Drupal.viewsSlideshowPager || {};
  Drupal.viewsSlideshowPager.transitionBegin = function (options) {
    var active_slide_num;
    for (pagerLocation in drupalSettings.viewsSlideshowPager[options.slideshowID]) {
      if (drupalSettings.viewsSlideshowPager[options.slideshowID]) {
        // Remove active class from pagers
        $('[id^="views_slideshow_pager_field_item_' + pagerLocation + '_' + options.slideshowID + '"]').removeClass('active');
        // Add active class to active pager.
        $('#views_slideshow_pager_field_item_' + pagerLocation + '_' + options.slideshowID + '_' + options.slideNum).addClass('active');
        active_slide_num = options.slideNum;
      }
    }

    var total_slides = $('.views_slideshow_pager_field_item').length;
    var active_slide_id = $('.views_slideshow_pager_field_item.active').attr('id');

    var counter = 0;

    if (active_slide_num > 3) {
      for (var slides = 0; slides < active_slide_num; slides++) {
        if (slides < 4) {
          $('#views_slideshow_pager_field_item_bottom_tribes_asset_listing-block_1_' + slides).hide();
        } else {
          $('#views_slideshow_pager_field_item_bottom_tribes_asset_listing-block_1_' + slides).show();
        }
      }
    } else {
      for (var slides = 3; slides >= active_slide_num; slides--) {
        if (slides > 3) {
          $('#views_slideshow_pager_field_item_bottom_tribes_asset_listing-block_1_' + slides).hide();
        } else {
          $('#views_slideshow_pager_field_item_bottom_tribes_asset_listing-block_1_' + slides).show();
        }
      }
    }
  };

  $(document).on('click', '#prev_pager', function (event) {
    event.preventDefault();
    $("#views_slideshow_controls_text_previous_tribes_asset_listing-block_1 a").click();
  });

  $(document).on('click', '#next_pager', function (event) {
    event.preventDefault();
    $("#views_slideshow_controls_text_next_tribes_asset_listing-block_1 a").click();
  });

})(jQuery, Drupal, drupalSettings);


