<?php get_header(); ?>

<?php while ( have_posts() ): the_post(); ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="archive-list no-gray">
					<?php $eventos = get_terms(array("evento")); ?>
					<?php foreach($eventos as $evento): ?>
						<?php 
						$q = new WP_Query( array( 'posts_per_page' => 1, 'post_type' =>'set', 'tax_query' => array( array('taxonomy' => 'evento','field' => 'slug', 'terms' => $evento->slug ) )) );
						if($q->have_posts()): $q->the_post();
						$img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'set-thumb-w2 h2' );
						?>
						<a href="<?php echo get_term_link($evento);?>" class="card-set gray-item item-evento tile-3-item type-imagem">
							<div class="item-container img-bg-<?php echo random_color_number(0,4) ?>" data-pre-image="<?php echo $img[0]; ?>">
								<?php print_loading(); ?>
							</div>
							<div class="title-wrapper set-grad-<?php echo random_color_number(0,4) ?>">
								<div class="title">
									<div class="title-cell">
										<h2><?php echo $evento->name; ?></h2>
									</div>
								</div>
							</div>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
