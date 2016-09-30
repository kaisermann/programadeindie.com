</div>
</main>

<footer>
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<nav>
						<?php wp_nav_menu(array("theme_location"=>"principal", "container" => ""));?>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="copyright">
					<span>COPYRIGHT © <?php echo date('Y'); ?> <?php bloginfo('name'); ?> | TODOS OS DIREITOS RESERVADOS</span>
				</div>
				<div class="credits">
					<script src="https://signature.kaisermann.me/#bc=#111&responsive=true" async></script>
				</div>
			</div>
		</div>
	</div>
</footer>
<div id="topbutton"></div>
<?php if (get_post_type()=='set') get_template_part("includes/front/photoswipe"); ?>
</div>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54c2ae666f928c38"></script>

<?php wp_footer(); ?>
<script>
	jQuery(document).ready(function(){jQuery("a").each(function(){var e=jQuery(this);var t=e.attr("href");if(t==undefined||t=="")return;var n=t.replace("http://","").replace("https://","");var r=t.split(".").reverse();var i=r[0].toLowerCase();var r=t.split("/").reverse();var s=r[2];var o=false;if(typeof analyticsFileTypes!="undefined"){if(jQuery.inArray(i,analyticsFileTypes)!=-1){o=true;e.click(function(){if(analyticsEventTracking=="enabled"){if(analyticsSnippet=="enabled"){_gaq.push(["_trackEvent","Downloads",i.toUpperCase(),t])}else{ga("send","event","Downloads",i.toUpperCase(),t)}}else{if(analyticsSnippet=="enabled"){_gaq.push(["_trackPageview",analyticsDownloadsPrefix+n])}else{ga("send","pageview",analyticsDownloadsPrefix+n)}}})}}if(t.match(/^http/)&&!t.match(document.domain)&&o==false){e.click(function(){if(analyticsEventTracking=="enabled"){if(analyticsSnippet=="enabled"){_gaq.push(["_trackEvent","Outbound Traffic",t.match(/:\/\/(.[^/]+)/)[1],t])}else{ga("send","event","Outbound Traffic",t.match(/:\/\/(.[^/]+)/)[1],t)}}else if(analyticsSnippet=="enabled"){_gaq.push(["_trackPageview",analyticsOutboundPrefix+n])}else{ga("send","pageview",analyticsOutboundPrefix+n)}})}})})
</script>
</body>
</html>