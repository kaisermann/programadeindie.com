<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php print_fotografo(); ?>
			<div class="archive-list has-loading">
				<?php while ( have_posts() ): the_post(); ?>
					<?php print_inner_set(); ?>
				<?php endwhile; ?>
			</div>
			<?php print_next_page_link(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
