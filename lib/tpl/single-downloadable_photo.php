<?php get_header(); ?>
	
	<?php if(have_posts()): while(have_posts()): the_post(); ?>
		<div class="container" id="content">	
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?php $img = get_field( 'foto' ); if($img && $img != ''): ?>
				<a href="<?php echo $img['sizes']['large']; ?>" rel="prettyPhoto">
					<img 
						class="img-responsive" 
						src="<?php echo $img['sizes']['large']; ?>">
				</a>
				<a href="<?php echo $img['sizes']['large']; ?>" class="btn btn-default" download>Scarica</a>
			<?php endif; ?>
			</div>
		</div>
	<?php endwhile; endif; ?>

<?php get_footer(); ?>