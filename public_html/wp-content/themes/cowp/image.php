<!DOCTYPE html>
<html lang="en">
<?php the_post(); ?>
<head>
	<?php $img = wp_get_attachment_image_src( $post->ID, "set-thumb-w3h3", false ); $img = $img[0]; ?>
	<?php $set_info = Programadeindie::get_set_info($post->post_parent); ?>
	<meta charset="UTF-8">

	<!-- Google -->
	<meta name="description" content="<?php the_title() ?>" />
	<meta name="application-name" content="<?php wp_title('|', true, 'right'); ?>" />

	<!-- Facebook -->
	<meta property="og:title" content="<?php echo get_the_title($post->post_parent); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="<?php echo the_permalink(); ?>" />
	<meta property="og:image" content="<?php echo $img; ?>" />
	<meta property="og:description" content="<?php echo $set_info['data'].' - '. '@'.$set_info['local'].' - '.$set_info['bairro'].' | '.$set_info['cidade']; ?>" />

	<!-- Twitter -->
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="<?php echo get_the_title($post->post_parent) ?>" />
	<meta name="twitter:description" content="<?php echo $set_info['data'].' - '. '@'.$set_info['local'].' - '.$set_info['bairro'].' | '.$set_info['cidade']; ?>" />
	<meta name="twitter:image" content="<?php echo $img; ?>" />

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-53306913-2', 'auto');
		ga('require', 'displayfeatures');
		ga('require', 'linkid', 'linkid.js');
		ga('send', 'pageview');
	</script>
	<meta http-equiv="refresh" content="0;URL=<?php echo get_post_permalink($post->post_parent)."?pid=".$post->ID; ?>"> 
</head>
<body></body>
</html>