<?php
function print_social_btns($image = NULL){
	$share_uri = ($image == NULL) ? '#' : 'http://dev.auronia.it/?attachment_id='.$image['id'];
	$fb_share_uri = ($image == NULL) ? '#' : 'https://facebook.com/sharer/sharer.php?u='.$share_uri;
	$gplus_share_uri = ($image == NULL) ? '#' : 'https://plus.google.com/share?url='.$share_uri;
	$twitter_share_uri = ($image == NULL) ? '#' : 'http://twitter.com/intent/tweet?source=auronia.it&url='.$share_uri;
	$pinterest_share_uri = ($image == NULL) ? '#' : 'http://pinterest.com/pin/create/button/?url='. $share_uri .'&media='. urlencode($image['url']);

	?>
	<ul class="share-social">
		<li>
			<a class="fb-share-btn share-btn" href="<?php echo $fb_share_uri; ?>" target="_blank">
				<img src="<?php echo CPM_PLUGIN_URL.'assets/img/facebook.png'; ?>" alt="">
			</a>
		</li>
		<li>
			<a class="twitter-share-btn share-btn" href="<?php echo $twitter_share_uri; ?>" target="_blank">
				<img src="<?php echo CPM_PLUGIN_URL.'assets/img/twitter.png'; ?>" alt="">
			</a>
		</li>
		<li>
			<a class="gplus-share-btn share-btn" href="<?php echo $gplus_share_uri; ?>" target="_blank">
				<img src="<?php echo CPM_PLUGIN_URL.'assets/img/googleplus.png'; ?>" alt="">
			</a>
		</li>
		<li>
			<a class="pinterest-share-btn share-btn" href="<?php echo $pinterest_share_uri; ?>" target="_blank">
				<img src="<?php echo CPM_PLUGIN_URL.'assets/img/pinterest.png'; ?>" alt="">
			</a>
		</li>
	</ul>
	<?php
}
function cu_pr_get_prod_title( $id ){	
	$prod_name = "No product found!";

	$order_id = get_post_meta($id, 'cu_pr_order_id', TRUE);
	$item_id = get_post_meta($id, 'cu_pr_item_id', TRUE);

	if($item_id && !empty($item_id)) {
		$item = new WC_Order($order_id);
		$prod_id = $item->get_item_meta($item_id, '_product_id');

		if($prod_id){
			$prod = get_product((int)$prod_id[0]);
			$prod_name = $prod->post->post_title;
		}

	}

	return $prod_name;
}
?>