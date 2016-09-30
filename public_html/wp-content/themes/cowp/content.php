	<article <?php post_class(); ?>>
		<header>
			<div class="title">
				<h3><a href="<?php the_permalink(); ?>"><? the_title(); ?></a></h3>
			</div>
		</header>
		<div class="content">
			<?php the_content(); ?>
		</div>
	</article>