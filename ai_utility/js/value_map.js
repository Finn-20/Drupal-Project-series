(function ($, Drupal, drupalSettings) {
  jQuery(document).ready(function(){
    jQuery(function() {
      jQuery(".tab-label-green").click(function() {
        jQuery(this)
          .next("div")
          .toggle();  
      });

      jQuery("#toggle_expand").click(function() {
        jQuery(".tab-label-green")
        .next("div")
        .toggle();
      });
    });
  });
})(jQuery, Drupal, drupalSettings);
