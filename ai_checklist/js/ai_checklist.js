jQuery(document).ready(function(){
	jQuery('div.subcategory-wrapper').each(function () {
	  var target_class = jQuery(this).attr('data-class');
	  var div_height = jQuery(this).height();
	  jQuery('div.' + target_class).css('height', div_height);
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer').css('height', (div_height-50));
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer.full-height').css('height', '96%');
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer.full-height').css('padding-top', '2%');
	  jQuery('div.' + target_class).children('div.sub-category-answers-outer.full-height').css('padding-bottom', '2%');
	});

	let demonstrationHeight = jQuery('#category-height-reference-Demo .subcategory-questions-wrapper').height();
	jQuery('#subcategory-ans-Demo').css('height',demonstrationHeight);
	jQuery('#subcategory-reviewer-Demo').css('height',demonstrationHeight);

	let assetsHeight = jQuery('#category-height-reference-Asse .subcategory-questions-wrapper').height();
	jQuery('#subcategory-ans-Asse').css('height',assetsHeight);
	jQuery('#subcategory-reviewer-Asse').css('height',assetsHeight);

	let reusableHeight = jQuery('#category-height-reference-Reus .subcategory-questions-wrapper').height();
	jQuery('#subcategory-ans-Reus').css('height',reusableHeight);
	jQuery('#subcategory-reviewer-Reus').css('height',reusableHeight);

	let otherHeight = jQuery('#category-height-reference-Othe .subcategory-questions-wrapper').height();
	jQuery('#subcategory-ans-Othe').css('height',otherHeight);
	jQuery('#subcategory-reviewer-Othe').css('height',otherHeight);

});
