<div class="row cu_pr-available_images">
	<input type="hidden" value="<?php echo $ituem_id; ?>" name="cu_pr_item_id">
	<input type="hidden" value="<?php echo $order_id; ?>" name="cu_pr_order_id">
	<input type="hidden" value="<?php echo get_the_ID(); ?>" name="cu_pr_id">
	
	<div class="col-lg-12 col-md-12  col-sm-12  col-xs-12 text-center">
		<?php cu_pr_print_fantasia_header($tpl, $wc_order); ?>
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
		<?php 
			$i = 1;
			while($img = get_field($img_field_name.$i)):
		?>
			<div class="col-lg-4 col-md-4 col-sm-4  col-xs-12 ">
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
							<input 
								type="radio" 
								name="cu_pr_image_choice" 
								value="<?php echo $img_field_name.$i; ?>"
								data-title="<?php the_field($img_field_name.$i.'_title'); ?>"> 
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
		<?php ++$i; endwhile; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	
	<div class="row cu_pr-prod-notice">
	</div>

	<p class="text-center license">
		<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.it"><img alt="Licenza Creative Commons" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" /></a><br />Quest'opera è distribuita con Licenza <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.it">Creative Commons Attribuzione - Non commerciale - Non opere derivate 3.0 Unported</a>.
	</p>
</div>	

<div class="clearfix"></div>

<div class="cu_pr-product_chosen row">	
	<div class="col-lg-12 col-md-12  col-sm-12  col-xs-12 text-center">
		<h1 class="text-center">Acquista questa proposta a 90,00€</h1>
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
			<div class="production"><?php echo $privacy_txt; ?></div>
			<br/>
			<input type="checkbox" name="conditions_terms" value="true"> 
			<div><?php echo $conditions_txt; ?></div>
		</div>
		
		<div class="clearfix"></div>
		<div class="text-center ">
			<a href="#" class="btn btn-default" id="back_to_cu_pr_available_images">&laquo; Indietro</a>
			<a href="#" id="confirm_choice_button" class="btn btn-default">Procedi al pagamento &raquo;</a>
		</div>
	</div>
</div>

<div class="clearfix"></div>

<div class="cu_pr-order_submitted row">
	<div class="col-lg-12 col-md-12  col-sm-12  col-xs-12 text-center">
		<h1 class="submission-complete-msg">Fai vedere ai tuoi amici la tua creazione!</h1>
		<figure class="text-center  cu_pr-chosen-img"></figure>
		<h3 class="submission-complete-msg">Condividi la tua grafica unica con tutti</h3>
	<?php print_social_btns(); ?>

		<input type="button" class="btn btn-default buy-cu_pr" value="vuoi acquistarla?">
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirm_choice_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_choice_modal_label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="confirm_choice_modal_label">Conferma La Scelta</h4>
			</div>
			<div class="modal-body">
				<p>
					Hai scelto la <span class="chosen-version-title"></span>. Ora daremo il via alla produzione della tua t-shirt personalizzata.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancella</button>
				<button type="button" href="#order_submitted" id="submit_choice" class="btn btn-default">Conferma</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
	var _CCTO = 1;
</script>