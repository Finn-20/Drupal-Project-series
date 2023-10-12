jQuery(document).ready(function(){
	jQuery('[data-toggle=modal]').on('click', function(e) {
	  var target = jQuery(jQuery(this).data('target'));
	  target.data('triggered', true);
	  
	  var target_id = jQuery(this).attr('data-target-id');
	  jQuery('#' + target_id)[0].click();
	  
	  setTimeout(function() {
	    if (target.data('triggered')) {
	      target.modal('show').data('triggered', false);
	    };
	  }, 5000); // milliseconds
	  return false;
	});

	/*jQuery('#usecase_content_feedback_modal').on('show.bs.modal', function () {
	  jQuery('.download')[0].click();
	});*/
});