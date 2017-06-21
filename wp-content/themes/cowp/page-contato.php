<?php get_header(); ?>

<?php while ( have_posts() ): the_post(); ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="normal-page">
					<div class="contato-form-wrapper">
						<?php echo do_shortcode('[contact-form-7 id="485"]'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endwhile; ?>
<?php get_footer(); ?>
