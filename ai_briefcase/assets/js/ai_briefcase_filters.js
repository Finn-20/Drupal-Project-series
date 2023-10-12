jQuery(document).ready(function () {

    /** Filters Display **/

    var display_clear_link = false;

/****************************************************************************************************************************/
                                       /*** My Briefcase Details Page starts here ***/
/****************************************************************************************************************************/

    /** Add wrapper div for My Briefcase and category **/
    if(jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block').length > 0) {
        jQuery('form.bef-exposed-form fieldset.fieldgroup').wrapAll('<div class="ai-filter-container"></div>');
        jQuery("div.ai-filter-container fieldset.fieldgroup").each(function () {
          jQuery(this).children('legend').addClass(jQuery(this).children('legend').children('span.fieldset-legend').text().toLowerCase().replace(' ', '_'));
          if (!jQuery(this).children('legend').hasClass('link-close') && !jQuery(this).children('legend').hasClass('type')) {
            jQuery(this).children('legend').addClass('link-close');
            jQuery(this).children('div.fieldset-wrapper').hide();
          }

          var data_drupal_selector = jQuery(this).attr('data-drupal-selector');
          var selected_filters = '';
          var filter_count = 0;
          jQuery.each(jQuery(this).find('input.form-checkbox:checked'), function () {
             var selected_val = jQuery(this).val();
             var selected_filter_text = jQuery(this).siblings('label').text();
             selected_filters = selected_filters + '<span class="selected_filter_wrapper"><span class="selected_filter">' + selected_filter_text + '</span><span class="filter_cancel_text" data-attr-id="' + data_drupal_selector + '-' + selected_val + '">x</span></span>';
             filter_count = filter_count + 1;
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
        jQuery('div.ai-filter-container fieldset.fieldgroup').on('click', 'legend', function (e) {
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

    jQuery('.filter_cancel_text').click(function () {
      var cancel_filter_id = jQuery(this).attr('data-attr-id');
      jQuery('input#' + cancel_filter_id).trigger('click').prop("checked", false);
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

  /** Sort By Filter **/
  if (jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block').length > 0) {
    jQuery('.form-item-sort-order').hide();
    var sort_by = jQuery('.form-item-sort-by .select-wrapper').clone();
    jQuery('.sort_by_wrapper').html(sort_by);
    jQuery('div.sort_by_wrapper #edit-sort-by').change(function () {
      if (jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block').length > 0) {
        jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block').submit();
      }
      else if (jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').length > 0) {
        jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-search-content-2-0-ai-accelerators-search').submit();
      }
      else if (jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block').length > 0) {
        jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block select#edit-sort-by').val(jQuery(this).val());
        jQuery('form#views-exposed-form-my-briefcase-details-view-briefcase-content-block').submit();
      }
    });
  }

  jQuery("form[data-drupal-selector=views-exposed-form-my-briefcase-details-view-briefcase-content-block]").on("change", "input:checkbox", function () {
    jQuery("button[data-drupal-selector=edit-submit-my-briefcase-details-view]").click();
  });

/****************************************************************************************************************************/
                                  /*** My Briefcase Details Page Ends here ***/
/****************************************************************************************************************************/

});
