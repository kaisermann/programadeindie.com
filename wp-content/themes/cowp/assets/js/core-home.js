function initHome($mos, $ms, $ap)
{
	if($ms.length>0)
		$ms.slick(
		{
			slide: 'a',
			slidesToShow: 1,
			arrows: false,
			dots: true,
			autoplay: true,
			autoplaySpeed: 4000,
			centerMode: true,
			centerPadding: '0'
		}).show();

	if($ap.length>0 && $(".next-page-link").length>0)
	{
		var $il = $('<div class="infinite-loading"></div>').insertAfter($ap);
		var next = ".next-page-link a";
		var infiniteselector = ".archive-list .infinite-item";
		var isLoadingPage = false;

		$(window).on("scroll", function()
		{
			if($(next).length>0 && $("footer").isOnScreen(500) && !isLoadingPage)
			{
				isLoadingPage = true;
				toggleLoading($il, true);

				$.get($(next).attr("href"), function( data ) 
				{
					var newelems = $(infiniteselector, data);
					var newlink = $(next, data);

					if(newlink.length<1)
						$(next).parent().remove();
					else
						$(next).attr("href", newlink.attr("href"));

					$ap.append(newelems);

					if($mos.length>0)
						$mos.packery( 'appended', newelems );

					toggleLoading($il, false);
					//loadVisibleImages();

					cur_page++;
					isLoadingPage = false;
					changeLocation(current_url.replace("/page/","")+"/page/"+cur_page);

				});
			}
		}); 
	}
}