function initMosaic($mos)
{
	if($mos.length>0)
	{
		var image_query;
		if (location.search.indexOf("pid") > -1)
			image_query = getUrlValue("pid","search");

		var $gal = $(".set.mosaic");

		// se estamos no set
		if($gal.length>0)
		{
			var $as = $(".mosaic .card-set.type-imagem a").one("mouseenter", function()
			{
				var img_link = $(this);
				preloadImage(img_link.attr("data-realhref"), function()
				{
					img_link.addClass("preloaded");
				});
			});

			var wid = $(window).innerWidth();

			if(wid<768)
				$as.each(function()
				{
					var img = $(this).children("div");
					var url = img.attr("data-pre-image");

					var extension = url.match(/\.[0-9a-z]+$/);
					if(extension.length && extension[0]==".gif")
						return true;

					var newsize = "200x200";
					if($(this).parent().is(".w6"))
						newsize = wid < 480 ? "400x200": "800x200";

					url= url.replace(/(\d{3,})x(\d{3,}).(\w{1,})/,newsize+".$3");
					img.attr("data-pre-image", url)
				});

			configurePhotoSwipe($as);

			if(image_query!==undefined)
			{
				var $imga = $("a[data-img-id="+image_query+"]");

				preloadImage($imga.attr("data-realhref"), function()
				{
					$imga.addClass("preloaded");
				});

				$imga.trigger("click");

				setTimeout(function(){scrollToElem($imga, -50);},500);
			}
		}

		$mos.packery({
			itemSelector: '.mosaic-item',
			columnWidth: '.mosaic-sizer',
			rowHeight: '.mosaic-sizer',
			percentPosition: false
		});

		if (typeof loadSetAdmin == 'function')
			loadSetAdmin();
		$mos.packery( 'layout' );


	}
}

function textResize()
{
	var win_wid = $(window).innerWidth();
	var doFontResize = function(_,__,i,min)
	{
		__.find("p,ul").css("font-size",i+"px");
		var boxheight = _.height();
		var textheight = __.outerHeight();

		/*
		console.log(_.outerHeight());
		console.log(__.outerHeight());
		console.log(i+"px");
		*/

		if(i>min && boxheight<=textheight)
			doFontResize(_,__,i-1,min);
	};

	$(".mosaic .text-sizer").each(function()
	{
		var $textsizer = $(this);
		var $textcontainer = $textsizer.find(".text-container");

		if(win_wid<768)
			$textsizer.find("p,ul").css("font-size","");
		else
			doFontResize($textsizer,$textcontainer, 16, 10);
	});
}

function toggleLoading(elem, show, id, fixed)
{
	fixed = fixed == false || fixed === undefined ? false : true;
	id = id === undefined ? "" : id;

	if(show)
	{
		var $loading = $('<div class="spinner-wrapper" style="display: none;"><div class="spinner"></div></div>');

		if(fixed)
			$loading.css("position", "fixed");

		if(elem.hasClass(".spinner-wrapper").length)
			return;

		elem.addClass("loading-item"+((id!=="")?"-"+id:"")).append($loading).find(".spinner-wrapper").show();
	}
	else
		elem.removeClass("loading-item"+((id!=="")?"-"+id:"")).children(".spinner-wrapper").remove();
}

function preloadImage(_src, callback)
{
	var pre = $('<img/>')[0];
	pre.src = _src;
	if(typeof callback === "function")
		$(pre).load(callback);
}

function scrollToElem(selector, offset)
{
	jQuery('html, body').animate({
		scrollTop: jQuery(selector).offset().top+offset
	}, 500);
}

function configurePhotoSwipe($as)
{
	var getItems = function()
	{
		var items = [];
		$as.each(function() {
			var $href   = $(this).attr('data-realhref'),
			$size   = $(this).attr('data-photo-size').split('x'),
			$width  = $size[0],
			$height = $size[1];

			var item = {
				src : $href,
				w   : $width,
				h   : $height,
				slug: $(this).attr("href")
			}

			items.push(item);
		});
		return items;
	};

	var openPhotoSwipe = function($index,fromURL)
	{
		var options =
		{
			index: $index,
			bgOpacity: 0.7,
			showHideOpacity: true,
			getThumbBoundsFn: function(index)
			{
				var thumbnail = document.querySelectorAll('.mosaic .card-set.type-imagem')[index].querySelectorAll("a")[0];
				var pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
				var rect = thumbnail.getBoundingClientRect();

				return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
			},
			galleryUID: "photo"
		}

		var lightBox = new PhotoSwipe($('.pswp')[0], PhotoSwipeUI_Default, getItems(), options);
		lightBox.listen('close', function()
		{
			changeLocation(set_url);
		});
		lightBox.listen('afterChange', function()
		{
			changeLocation(lightBox.currItem.slug);
			if(typeof window.ga == "function")
				ga('send', 'pageview', lightBox.currItem.slug);
		});
		lightBox.init();
	};

	$as.on("click",function(e)
	{
		e.preventDefault();
		$card = $(this).parent();

		var $index = parseInt($card.index(".type-imagem"));
		openPhotoSwipe($index,false);
		e.stopPropagation();
		return;
	});
}
