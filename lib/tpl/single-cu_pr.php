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
				
				if(!$chosen || empty($chosen)):
			?>  
			<div class="row cu_pr-available_images">
				<input type="hidden" value="<?php echo $item_id; ?>" name="cu_pr_item_id">
				<input type="hidden" value="<?php echo $order_id; ?>" name="cu_pr_order_id">
				<input type="hidden" value="<?php echo get_the_ID(); ?>" name="cu_pr_id">
				
				<?php cu_pr_print_fantasia_header($tpl); ?>

				<!--<ul class="clearfix available-image-list">-->
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="row">
					<?php 
						$i = 1;
						while($img = get_field($img_field_name.$i)):
					?>
						<!--<li>-->
						<div class="col-md-4 ">
							<h5 class="text-center">
									<?php the_field($img_field_name.$i.'_title'); ?>
								</h5>
							<div class="thumbnail">
								

								<a href="<?php echo $img['sizes']['large']; ?>" rel="prettyPhoto">
									<img class="img-responsive" src="<?php echo $img['sizes']['large']; ?>" alt="<?php the_field($img_field_name.$i.'_title'); ?>">
								</a>
								
								<div class="cu_pr-select-item caption text-center">
									<a 
										href="#product_chosen" 
										class="cu_pr-image-choice-button" 
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
							</div>
						<?php if (get_field($img_field_name.$i.'_note')): ?>
							<p class="nota">
								<i>Dettagli proposta:</i>
								<?php the_field($img_field_name.$i.'_note'); ?>
							</p>
						<?php endif; ?>
						</div>
						<!--</li>-->
					<?php ++$i; endwhile; ?>
				<!--</ul>-->
					</div>
				</div>
				<div class="clearfix"></div>
				<?php cu_pr_print_fantasia_footer($tpl); ?>

				<div class="row cu_pr-prod-notice">
					<div class="col-md-12">
					Se nessuna delle proposte creative dovesse piacerti, 
					per piacere contattaci alla casella di posta servizio.clienti@auronia.it e indicaci 
					come vorresti che AURONIA realizzasse il trattamento della 
					tua immagine. Provvederemo a creare delle nuove proposte 
					più in linea con la tua personalità per realizzare la tua 
					t-shirt proprio come la desideri. 
					</div>
				</div>
				<p class="text-center license">
					<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.it"><img alt="Licenza Creative Commons" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" /></a><br />Quest'opera è distribuita con Licenza <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.it">Creative Commons Attribuzione - Non commerciale - Non opere derivate 3.0 Unported</a>.
				</p>
			</div>	

			<div class="clearfix"></div>
			
			<div class="cu_pr-product_chosen row">	
				<div class="col-md-12 text-center">
					<h1 class="text-center">Anteprima del tuo ordine</h1>
					<figure class="text-center cu_pr-chosen-img"></figure>
					<div class="privacy-terms">
						<?php 
							$privacy_txt_lang = get_post_meta(get_the_ID(), 'privacy_txt', TRUE);
							
							$privacy_txt = ($privacy_txt_lang == 'it') ? 'cpm_privacy_txt_it' : 'cpm_privacy_txt_en';
							$privacy_txt = stripslashes( get_option($privacy_txt) ); 

							$conditions_txt_lang = get_post_meta(get_the_ID(), 'conditions_txt', TRUE);

							$conditions_txt = ($conditions_txt_lang == 'it') ? 'cpm_conditions_txt_it' : 'cpm_conditions_txt_en';
							$conditions_txt = stripslashes( get_option($conditions_txt) ); 
						?>
						<input type="checkbox" name="privacy_terms" value="true"> 
						<div><?php echo $privacy_txt; ?></div>
						<br/>
						<input type="checkbox" name="conditions_terms" value="true"> 
						<div><?php echo $conditions_txt; ?></div>
					</div>
					
					<div class="clearfix"></div>
					<div class="text-center ">
						<a href="#" class="btn btn-default" id="back_to_cu_pr_available_images">&laquo; Indietro</a>
						<a href="#order_submitted" class="btn btn-default" id="submit_choice">Invia ordine &raquo;</a>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>

			<div class="cu_pr-order_submitted row">
				<div class="col-md-12 text-center">
					<h1 class="submission-complete-msg">Fai vedere ai tuoi amici la tua creazione!</h1>
					<figure class="text-center  cu_pr-chosen-img"></figure>
					<h3 class="submission-complete-msg">Condividi la tua grafica unica con tutti</h3>
				<?php print_social_btns(); ?>
				</div>
			</div>

			<?php else: ?>
				<div class="row">
					<?php $img1 = get_field($chosen); if($img1): ?>
					<div class="col-md-12 text-center">
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