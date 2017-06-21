<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>

<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<title><?php bloginfo('name'); ?> | <?php is_home() ? bloginfo('description') : wp_title(''); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="shortcut icon" href="<?php echo bloginfo('url'); ?>/favicon.png"/>
	<?php wp_head(); ?>
	<?php $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; ?>
	<script>

	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	ga('create', 'UA-75837777-2', 'auto');
	ga('require', 'displayfeatures');
	ga('require', 'linkid', 'linkid.js');
	ga('send', 'pageview');

	var ishome = <?php echo (is_front_page())?"true":"false"; ?>;
	var isset = <?php echo (is_singular("set"))?"true":"false"; ?>;
	var cur_page = <?php echo $paged; ?>;
	var max_pages = <?php echo $wp_query->max_num_pages; ?>;
	var home_url = "<?php echo bloginfo('url'); ?>";
	var current_url = window.location.href.replace(/\/$/, "");
	</script>
</head>

<body <?php body_class(); ?>>
	<div id="page-wrapper">
		<header>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="header-content">
							<div class="mobile-top">
								<div><a href="<?php echo bloginfo('url'); ?>" class="logo"><?php bloginfo('name'); ?></a></div>
								<div class="text-right">
									<div class="mobile-btn-wrapper">
										<span class="bar-icon"></span>
										<span class="bar-icon"></span>
										<span class="bar-icon"></span>
									</div>
								</div>
							</div>
							<div class="main-menu">
								<nav>
									<?php wp_nav_menu(array("theme_location"=>"principal", "container" => ""));?>
								</nav>
							</div>
							<div class="search-wrapper"><?php get_search_form(); ?></div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<main>
			<div id="page-content">
