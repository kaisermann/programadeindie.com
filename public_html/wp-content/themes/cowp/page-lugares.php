<?php get_header(); ?>

<?php while ( have_posts() ): the_post(); ?>
	<script>
		var infoBoxes = [];		
		var pinUrl = '<?php echo get_stylesheet_directory_uri()."/assets/img/pin.png" ?>';
		var closeBtnUrl = '<?php echo get_stylesheet_directory_uri()."/assets/img/map-close.png" ?>';

		<?php $lugares = get_terms(array("lugar")); ?>


		<?php foreach($lugares as $lugar): ?>
		<?php 
		$coords = get_field("coordenadas", $lugar);
		$cidade = get_field("cidade", $lugar);
		$bairro = get_field("bairro", $lugar);
		$img = get_field("imagem_do_lugar", $lugar);
		$img = wp_get_attachment_image_src($img, "thumbnail");
		$img = $img[0];
		?>
		infoBoxes.push(["<?php echo $lugar->name; ?>", <?php echo $coords['lat'] ?>, <?php echo $coords['lng'] ?>, 1,
			'<a href="<?php echo get_term_link($lugar); ?>" class="info-wrapper">' +
			'<img src="<?php echo $img; ?>">' + 
			'<div class="info-container"> ' +
			'<div class="info-table"> ' +
			'<div class="info-cell"> ' +
			'<h3><?php echo $lugar->name; ?></h3>' +
			'<h4><?php echo $cidade." - ".$bairro; ?></h4>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</a>'
			]);
		(new Image()).src = "<?php echo $img; ?>";
	<?php endforeach; ?>
</script>
<div id="map-canvas">
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
