<?php get_header(); ?>
	<?php if(have_posts()): while(have_posts()): the_post(); ?>
	<div id="content" class="container cu_pr_preview">
		<div id="cu_pr_preview" class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<?php 
				require_once('tpl-loop-cu_pr.php');

				$tpl = get_field('cu_pr_single_tpl');
				$chosen = get_post_meta(get_the_ID(), 'cu_pr_chosen_img', TRUE);  
				$order_id = get_post_meta(get_the_ID(), 'cu_pr_order_id', TRUE); 
				$item_id = get_post_meta(get_the_ID(), 'cu_pr_item_id', TRUE); 
				$img_field_name = 'cu_pr_image_';

				$wc_order = new WC_Order($order_id);
				$qty = $wc_order->get_item_meta($item_id, '_qty');
				
				if(true):
				//if(!$chosen || empty($chosen)):
					if( WC_Pre_Orders_Order::order_contains_pre_order( $wc_order )  ){
						include 'trial-cu_pr-content.php';
					}else{
						include 'default-cu_pr-content.php';
					}
			?>  
			
			<?php else: ?>
				<div class="row">
					<?php $img1 = get_field($chosen); if($img1): ?>
					<div class="col-lg-12 col-md-12  col-sm-12  col-xs-12 text-center">
						<figure>
							<a href="<?php echo $img1['sizes']['large']; ?>" rel="prettyPhoto">
								<img src="<?php echo $img1['sizes']['large']; ?>" alt="preview image of the customized product">
							</a>
						</figure>

						<h3 class="submission-complete-msg">Condividi la tua creazione Auronia con i tuoi amici</h3>
					</div>
					<?php endif; ?>
				</div>
				
				<?php print_social_btns($img1); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
	<?php endwhile; endif; ?>

<?php get_footer(); ?>