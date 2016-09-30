<?php
srand((double) microtime() * 1000000);

function random_color_gradient($min,$max)
{
	static $last = -1;
	while(($ret = mt_rand($min,$max))==$last);
	return($last = $ret);
}

function random_color_number($min,$max)
{
	static $lastgrade = -1;
	while(($ret = mt_rand($min,$max))==$lastgrade);
	return($lastgrade = $ret);
}

function print_loading()
{
	echo '<div class="spinner-wrapper"><div class="spinner"></div></div>';
}

function print_inner_set()
{
	global $post;
	$set_info = ProgramadeIndie::get_set_info($post->ID, "w3 h3");
	?>

	<a href="<?php the_permalink(); ?>" class="card-set infinite-item gray-item blur-item scale-item evento-set tile-3-item type-imagem">
		<div class="item-container img-bg-<?php echo random_color_number(0,4) ?>" data-pre-image="<?php echo $set_info["thumb"]; ?>">
			<?php print_loading(); ?>
		</div>
		<div class="title-wrapper set-grad-<?php echo random_color_number(0,4) ?>">
			<div class="title">
				<div class="title-cell">
					<h2><?php the_title(); ?></h2>
					<div class="set-info">
						<div class="more-info">
							<?php $link_lugar = get_term_link($set_info['lugar']); ?>
							<span class="date"><?php echo $set_info['data']; ?></span>
							<?php 
							echo '<span class="at">@'.$set_info['local'].' - '.$set_info['bairro'].' | '.$set_info['cidade'].'</span>';
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</a>
	<?php
}

function print_home_set($setsize, $isslide = false) 
{
	global $post;
	$set_info = ProgramadeIndie::get_set_info($post->ID, $setsize);
	?>

	<a href="<?php the_permalink(); ?>" class="card-set infinite-item gray-item blur-item scale-item mosaic-item mobile-full <?php echo $setsize; ?> type-imagem">
		
		<div class="item-container <?php echo "img-bg-".random_color_number(0,4); ?>" data-pre-image="<?php echo $set_info["thumb"]; ?>">
			<?php print_loading(); ?>
		</div>
		<div class="title-wrapper set-grad-<?php echo random_color_gradient(0,4) ?>">
			<div class="title-cell">
				<h2><?php the_title(); ?></h2>
				<div class="set-info">
					<div class="more-info">
						<?php $link_lugar = get_term_link($set_info['lugar']); ?>
						<span class="date"><?php echo $set_info['data']; ?></span>
						<?php 
						echo '<span class="at">@'.$set_info['local'].' - '.$set_info['bairro'].' | '.$set_info['cidade'].'</span>';
						?>
					</div>
				</div>
			</div>
		</div>
	</a>
	<?php 
}

function print_admin_set_controls($sizeclass, $icon_move=true, $icon_size=true)
{
	$size = Programadeindie::get_size_by_class($sizeclass);

	if($icon_move)
		echo '<span class="move-handler"><i class="icon-move"></i></span>';

	if($icon_size)
	{
		echo "<div class='foto-select-btn'><i class='icon-arrow'></i><ul class='foto-select' >";
		for($j=1;$j<=Programadeindie::n_rows; $j++)
		{
			for($jj=1;$jj<=Programadeindie::n_cols;$jj++)
			{
				$tam = 'w'.$jj.' h'.$j;

				$is_current = (($sizeclass == $tam)?"current":"");
				$selected = (($jj <= $size["cols"] && $j<=$size["rows"])?"selected":"");

				echo "<li data-row='$j' data-col='$jj' value='$tam' class='$is_current $selected'></li>";
			}
		}
		echo "</ul></div>";
	}
}

function print_next_page_link()
{
	if(get_next_posts_link()!="")
		echo '<div class="next-page-link">'.get_next_posts_link().'</div>';
}

function print_fotografo()
{
	?>
	<div class="author-meta-box">
		<section class="avatar">	
			<?php echo get_avatar(get_the_author_meta("ID"), 256); ?>
		</section>
		<section class="bio">
			<h2>
				<?php echo get_the_author_meta("display_name");  ?>
			</h2>
			<p>
				<a href="<?php echo get_the_author_meta('user_url'); ?>" target="_blank"><?php echo get_the_author_meta('user_url'); ?></a>
			</p>
		</section>
	</div>
	<div class="item-container img-bg-<?php echo random_color_number(0,4) ?>" style="background-image:url(<?php echo $set_info["thumb"]; ?>);"></div>

	<?php
}

?>