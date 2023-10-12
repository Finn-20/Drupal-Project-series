(function ($, Drupal) {
  $(document).on('click', '#asset-state-archive', function (event) {
    var path = $("#archive_current_path").val();
    window.location.href = path;
  });
})(jQuery, Drupal);
