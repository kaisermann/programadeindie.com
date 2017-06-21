<?php get_header(); ?>

<div class="page-set container">
	<div class="row">
		<div class="col-md-12">
			<?php $eids = array(); ?>

			<div class="main-slider has-loading">
				<?php $q = new WP_Query(array("post_type" => "set", "posts_per_page" => -1, 'orderby' => 'date', 'order' => 'DESC', 'featured' => 'yes')); ?>
				<?php while($q->have_posts()):  $q->the_post();
				$eids[] = $post->ID;
				print_home_set("w6 h2", true);
				endwhile; 
				?>
			</div>

			<div class="archive-list has-loading mosaic home">
				<div class="mosaic-sizer mobile-full"></div>
				<?php
				$sizes = array(
					"w3 h2","w3 h2", 
					"w2 h2", "w2 h2", "w2 h2", 
					"w2 h3", "w4 h1", "w2 h2", 
					"w2 h2");
				while ( have_posts() )
				{ 
					the_post(); 
					print_home_set(array_shift($sizes));
				}
				?>
			</div>
			<?php print_next_page_link(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
