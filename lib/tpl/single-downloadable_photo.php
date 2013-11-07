<?php get_header(); ?>
	
	<?php if(have_posts()): while(have_posts()): the_post(); ?>
		<div class="container" id="content">	
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?php $img = get_field( 'foto' ); if($img && $img != ''): ?>
				<div class="col-md-8 col-sm-8 col-xs-10 col-md-push-2 col-sm-push-2 col-xs-push-1 downloadable-image-container">
				<a href="<?php echo $img['sizes']['large']; ?>" rel="prettyPhoto">
					<img 
						class="img-responsive" 
						src="<?php echo $img['sizes']['large']; ?>">
				</a>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-12 col-sm-12 col-xs-12 text-center">
				<a href="<?php echo $img['sizes']['large']; ?>" class="btn btn-default" download><?php _e( 'Download:', 'woocommerce' ); ?></a>
				</div>
			<?php endif; ?>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 text-center">
				<?php
					echo do_shortcode( '[cu_pr_download_photo_content_sc][/cu_pr_download_photo_content_sc]' ); 
					echo do_shortcode( '[cu_pr_download_photo_button_sc][/cu_pr_download_photo_button_sc]' ); 
				?>
			</div>
		</div>
	<?php endwhile; endif; ?>

<?php get_footer(); ?>