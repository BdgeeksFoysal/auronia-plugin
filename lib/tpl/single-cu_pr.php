<?php get_header(); ?>
	
	<?php if(have_posts()): while(have_posts()): the_post(); ?>
		<div id="cu_pr_preview" class="woocommerce">
			<?php 
				require_once('tpl-loop-cu_pr.php');

				$tpl = get_field('cu_pr_single_tpl');
				$chosen = get_post_meta(get_the_ID(), 'cu_pr_chosen_img', TRUE);  
				$order_id = get_post_meta(get_the_ID(), 'cu_pr_order_id', TRUE); 
				$item_id = get_post_meta(get_the_ID(), 'cu_pr_item_id', TRUE); 
				$img_field_name = 'cu_pr_image_';

				$wc_order = new WC_Order($order_id);
				$qty = $wc_order->get_item_meta($item_id, '_qty');

				if($chosen || empty($chosen)):
			?> 
			<div class="cu_pr-available_images">
				<input type="hidden" value="<?php echo $item_id; ?>" name="cu_pr_item_id">
				<input type="hidden" value="<?php echo $order_id; ?>" name="cu_pr_order_id">
				<input type="hidden" value="<?php echo get_the_ID(); ?>" name="cu_pr_id">
				
				<?php cu_pr_print_fantasia_header($tpl); ?>

				<ul class="clear available-image-list">
					<?php 
						$i = 1;
						while($img = get_field($img_field_name.$i)):
					?>
						<li>
							<h4 class="cu_pr-image-title">
								<?php the_field($img_field_name.$i.'_title'); ?>
							</h4>

							<figure>
								<a href="<?php echo $img['sizes']['large']; ?>" rel="prettyPhoto">
									<img src="<?php echo $img['sizes']['medium']; ?>" alt="preview image of the customized product">
								</a>
							</figure>

							<div class="cu_pr-select-item">
								<a 
									href="#product_chosen" 
									class="button cu_pr-image-choice-button" 
									data-item="<?php echo $img_field_name.$i; ?>" 
									data-img="<?php echo $img['sizes']['large']; ?>"
									data-img_id="<?php echo $img['id']; ?>">
									Scegli
								</a>
								<p>
									<input type="radio" name="cu_pr_image_choice" value="<?php echo $img_field_name.$i; ?>"> 
									Choose This Design
								</p>
							</div>
						</li>
					<?php ++$i; endwhile; ?>
				</ul>

				<?php cu_pr_print_fantasia_footer($tpl); ?>

				<div class="cu_pr-prod-notice">
					Se nessuna delle proposte creative dovesse piacerti, 
					per piacere contattaci alla casella di posta…… e indicaci 
					come vorresti che AURONIA realizzasse il trattamento della 
					tua immagine. Provvederemo a creare delle nuove proposte 
					più in linea con la tua personalità per realizzare la tua 
					t-shirt proprio come la desideri. 
				</div>

			</div>	

			<div class="clear"></div>
			
			<div class="cu_pr-product_chosen">	
				<figure class="cu_pr-chosen-img"></figure>
				<div class="privacy-terms">
					<?php 
						$privacy_txt_lang = get_post_meta(get_the_ID(), 'privacy_txt', TRUE);
						
						$privacy_txt = ($privacy_txt_lang == 'it') ? 'cpm_privacy_txt_it' : 'cpm_privacy_txt_en';
						$privacy_txt = get_option($privacy_txt); 

						$conditions_txt_lang = get_post_meta(get_the_ID(), 'conditions_txt', TRUE);

						$conditions_txt = ($conditions_txt_lang == 'it') ? 'cpm_conditions_txt_it' : 'cpm_conditions_txt_en';
						$conditions_txt = get_option($conditions_txt); 
					?>
					<input type="checkbox" name="privacy_terms" value="true"> 
					<div><?php echo $privacy_txt; ?></div>
					<br/>
					<input type="checkbox" name="conditions_terms" value="true"> 
					<div><?php echo $conditions_txt; ?></div>
				</div>
				
				<div class="clear"></div>
				<a href="#" class="button alignleft" id="back_to_cu_pr_available_images">&laquo; Back</a>
				<a href="#order_submitted" class="button alignright" id="submit_choice">Submit Order &raquo;</a>
			</div>

			<div class="cu_pr-order_submitted">
				<h3 class="submission-complete-msg">Fai vedere ai tuoi amici la tua creazione!</h3>
				<figure class="cu_pr-chosen-img"></figure>
				<h3 class="submission-complete-msg">Condividi la tua grafica unica con tutti</h3>
				
				<?php print_social_btns(); ?>
			</div>

			<?php else: ?>
				<ul>
					<?php $img1 = get_field($chosen); if($img1): ?>
					<li>
						<figure>
							<a href="<?php echo $img1['sizes']['large']; ?>" rel="prettyPhoto">
								<img src="<?php echo $img1['sizes']['medium']; ?>" alt="preview image of the customized product">
							</a>
						</figure>

						You've Already Choose This Design
					</li>
					<?php endif; ?>
				</ul>
				
				<?php print_social_btns($img1); ?>
			<?php endif; ?>
		</div>
	<?php endwhile; endif; ?>

<?php get_footer(); ?>