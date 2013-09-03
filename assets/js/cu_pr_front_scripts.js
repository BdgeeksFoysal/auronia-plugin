jQuery(document).ready(function($) {

	$('.cu_pr-image-choice-button').on('click', function(){
		var item = $(this).data('item'),
			img_id = $(this).data('img_id'),
			img = $(this).data('img'),
			hash = $(this).attr('href');

		$('input[name="cu_pr_image_choice"][value="'+ item +'"]').trigger('click');
		$(document).trigger(hash, [item, img, img_id]);
	});

	$('#submit_choice').on('click', function (e) {
		//e.preventDefault();
		var chosen = $('input[name="cu_pr_image_choice"]:checked'),
			$this = $(this),
			privacy = $('input[name="privacy_terms"]'),
			conditions = $('input[name="conditions_terms"]'),
			data = {
				action: 'cu_pr_add_chosen',
				chosen_image : chosen.val(),
				cu_pr_id	: $('input[name="cu_pr_id"]').val(),
				item_id		: $('input[name="cu_pr_item_id"]').val(),
				order_id	: $('input[name="cu_pr_order_id"]').val(),
			};

		if(chosen.length > 0){
			if(privacy.is(':checked') && conditions.is(':checked')){
				$.post(CPM_Ajax.ajaxurl, data, function(ret) {
					if(ret.status == 'true'){
						$('.cu_pr-product_chosen').fadeOut(250);
						$('.cu_pr-order_submitted').delay(200).fadeIn(300);
					}
				}, 'json');
			}else{
				$this.after('<span class="error"> &nbsp; You Have to accept the privacy terms and conditions.</span>');
			}
		}else{
			$this.after('<span class="error"> &nbsp; No Items Selected!</span>');
		}
		
		setTimeout(function(){
			$this.next('.error').remove();
		}, 1500);
		return true;
	});

	$("a[rel^='prettyPhoto']").prettyPhoto({
		social_tools: false,
		theme: 'pp_woocommerce',
		horizontal_padding: 40,
		opacity: 0.9,
		deeplinking: false
	});


	$(document).on('#product_chosen', function(e, item, img, img_id){
		var share_uri = 'http://dev.auronia.it/?attachment_id='+img_id;

		$('.cu_pr-available_images').fadeOut(250);
		$('.cu_pr-chosen-img').html('<img src="'+ img +'" class="aligncenter">');
		$('.fb-share-btn').attr('href', 'https://facebook.com/sharer/sharer.php?u='+share_uri);
		$('.gplus-share-btn').attr('href', 'https://plus.google.com/share?url='+share_uri);
		$('.twitter-share-btn').attr('href', 'http://twitter.com/intent/tweet?source=auronia.it&url='+share_uri);
		$('.pinterest-share-btn').attr('href', 'http://pinterest.com/pin/create/button/?url='+ share_uri +'&media='+encodeURIComponent(img));
		$('.cu_pr-product_chosen').delay(200).fadeIn(300);
	});

	$('.share-btn').on('click', function(){
		window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
		return false;		
	});

	$('#back_to_cu_pr_available_images').on('click', function(){
		$('.cu_pr-product_chosen').fadeOut(250);
		$('.cu_pr-available_images').delay(200).fadeIn(300);
	});
});