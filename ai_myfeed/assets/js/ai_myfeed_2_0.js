jQuery(document).ready(function(){
	
	jQuery('.spb-controls').find('.spb_close').click(function(){
		jQuery(this).closest(".my_feed_term_select-modal.spb_overlay").addClass("hidingpopcls");
	});
  
jQuery("#myfeed_open").click(function(){
	jQuery(".simple-popup-blocks-global .my_feed_term_select-modal.spb_overlay").removeClass('hidingpopcls');
	jQuery("#my_feed_term_select").show();
});
jQuery("#edit_pref_buttpopup").click(function(event){ event.preventDefault();
	jQuery(".simple-popup-blocks-global .my_feed_term_select-modal.spb_overlay").removeClass('hidingpopcls');
	jQuery("#my_feed_term_select").removeClass("hidepopup_termselected");
	jQuery("#my_feed_term_select").show();
});
if(jQuery("#my_feed_term_select").hasClass("hidepopup_termselected")){
	jQuery(".my_feed_term_select-modal.spb_overlay").addClass("hidingpopcls");
}

});
