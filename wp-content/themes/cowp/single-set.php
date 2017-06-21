<?php get_header(); ?>

<?php
 if(post_password_required()):
the_content(); 
else:
?>

<?php $canedit = (Programadeindie::$can_edit_photos)? true : false; ?>
<script>var pid = <?php echo $post->ID; ?>;var can = <?php echo json_encode($canedit); ?>;var set_url="<?php echo get_the_permalink($post->ID); ?>";</script>

<div class="container set reset-pd-xs">
	<div class="row">
		<div class="col-md-12">
			<?php
			if(isset($_GET['photo']))
			{
				$url = wp_get_attachment_image_src($_GET['photo'], Programadeindie::getFullSize(true));
				$url = $url[0];
				echo '<img src="'.$url.'" style="display: none;"/>';
			}
			?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php 
				$set_info = ProgramadeIndie::get_set_info($post->ID, "w6 h3");
				?>
				<div class="stamp card-set w6 h3 mosaic-item">
					<div class="item-container" style="background-image:url(<?php echo $set_info["thumb"]; ?>);"></div>
					<div class="title-wrapper set-grad-<?php echo  random_color_number(0,4) ?>">
						<div class="title-cell">
							<h1><?php the_title(); ?></h1>
							<div class="set-info">
								<div class="more-info">
									<?php
									$link_lugar = get_term_link($set_info['lugar']);

									echo '<span class="date">'.$set_info['data'].'</span>';
									echo '<span class="at">@<a href="'.esc_url($link_lugar).'">'.$set_info['local'].'</a>';
									echo ' - '.$set_info['bairro'].' | '.$set_info['cidade'].'</span>';

									$extras = get_field("extra_info");
									if($extras!="")
										echo '<div class="extra-info">'.$extras."</div>";
									
									?>
								</div>
								<div class="fotografos">
									<?php 
									$fotografos = (get_coauthors());
									foreach($fotografos as $f)
									{
										echo '<a href="'.get_author_posts_url($f->ID).'" data-name="'.$f->display_name.'">';	
										echo get_avatar( $f->ID, 64 );
										echo '</a>';
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="set mosaic has-loading<?php echo $canedit?" isadmin":""?>">
					<div class="mosaic-sizer"></div>
					<?php
					function get_item_size($item_size, $img_meta)
					{
						$url_base = Programadeindie::GetUploadDir();

						$display_size = array("thumb"=> "set-thumb-".$item_size, "full" => Programadeindie::getFullSize(true));
						
						$urls = array("thumb"=>"", "full"=>"");

						//var_dump($img_meta);
						if(array_key_exists("sizes", $img_meta))
						{
							$type = strtolower($img_meta["sizes"]['thumbnail']["mime-type"]);
							$pwidth = @$img_meta["sizes"][$display_size["full"]]["width"];
							$pheight = @$img_meta["sizes"][$display_size["full"]]["height"];
						}

						// Imagens que não tem o tamanho do crop. Se nao tiver, pega o tamanho original dela usa ele na lightbox.
						foreach ($urls as $tamanho => $val) 
						{
							if(array_key_exists("sizes", $img_meta) 
								&& array_key_exists($display_size[$tamanho], $img_meta["sizes"]) 
								&& $type!="image/gif")
							{
								$urls[$tamanho] = join("/", array($url_base,dirname($img_meta["file"]),$img_meta["sizes"][$display_size[$tamanho]]["file"]));
							}
							else
							{
								$urls[$tamanho] = join("/", array($url_base,$img_meta["file"]));
								$pwidth = @$img_meta["width"];
								$pheight = @$img_meta["height"];
							}
						}

						return array("w" => $pwidth, "h" => $pheight, "urls" => $urls);
					}

					$items = get_field("fotos");
					$imgids[] = array();
					$n_fotos = count($items);

					foreach ($items as $foto) 
						$imgids[] = $foto['imagem'];
					$cache = get_posts(array('post_type' => 'attachment', 'numberposts' => -1, 'post__in' => $imgids));

					for ($i = 0; $i < $n_fotos; $i++) 
					{
						$set_classes = array("card-set","mosaic-item");

						$item = $items[$i];
						$card_type = $item["tipo"];
						$item_content = $item[$card_type];
						$size_class = trim($item["tamanho"]);

						if($card_type=="imagem")
						{
							$set_classes[] = "scale-item";
							$set_classes[] = "gray-item";
						}
						$set_classes[] = "type-".$card_type;
						$set_classes[] = $size_class;

						echo '<div class="'.implode(" ",$set_classes).'"'
						.((Programadeindie::$can_edit_photos)?(' data-type="'.$card_type.'" data-order="'.$i.'" data-size="'.$size_class.'"'):(''))						
						.'>';

						switch($card_type)
						{
							case 'texto':
							$content = apply_filters('the_content', $item_content);
							echo '<div class="item-container"></div>';
							echo '<div class="text-sizer"><div class="text-container">'.$content.'</div></div>';
							break;

							case 'imagem':
							$img_id = $item_content;
							$image_info = get_item_size($size_class, wp_get_attachment_metadata($img_id, false));

							echo '
							<a data-realhref="'.$image_info["urls"]["full"].'" href="'.get_attachment_link($img_id).'" data-img-id="'.$img_id.'" data-photo-size="'.$image_info["w"].'x'.$image_info["h"].'">
								<div class="item-container img-bg-'.random_color_number(0, 4).'" data-pre-image="'.$image_info["urls"]["thumb"].'">';
							print_loading();
							echo '</div></a>';
							break;

							case 'video':
							echo '<div class="item-container"></div>';
							echo wp_oembed_get($item_content);
							break;

							case 'void':
							echo '<div class="item-container"></div>';
							break;
						}
						
						// Spacer

						if(Programadeindie::$can_edit_photos) 
							print_admin_set_controls($item["tamanho"]);

						echo '</div>';
					}
					?>

				<?php endwhile;  ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php get_footer(); ?>