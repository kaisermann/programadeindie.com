<?php get_header(); ?>

<?php if(!have_posts()) wp_redirect(home_url('/nenhum-resultado-por-'.get_search_query())); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
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
