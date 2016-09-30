(function()
{
	function ImageSeeker()
	{
		var shouldSeek = true;
		var shouldUnbind = false;
		var load_interval = 400, check_interval = 5000, scroll_interval = 200;
		var loadTimeout, checkTimeout;
		var load_count = 0, last_load_count = -1;

		var scrollTimeout;
		var scroll_velocity = 0;
		var last_scroll_position = 0;
		var scroll_dir = 0;

		var max_velocity = 4;

		var visibility_offset = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		visibility_offset *= 0.8;

		var stopSeeker = function()
		{
			shouldSeek = false;
			shouldUnbind = true;
			loadTimeout && clearTimeout(loadTimeout);
			checkTimeout && clearTimeout(checkTimeout);
			scrollTimeout && clearTimeout(scrollTimeout);
		};

		var seekVisibleImages = function() 
		{
			loadTimeout && clearTimeout(loadTimeout);

			//console.log("seeking - cur vel:" + scroll_velocity);

			if(shouldSeek && loadVisibleImages())
				loadTimeout = setTimeout(seekVisibleImages, load_interval);
		};

		var checkLoadCount = function()
		{
			checkTimeout && clearTimeout(checkTimeout);

			if(shouldSeek==false)
				return;

			if(load_count==last_load_count)
			{
				shouldSeek = false;
				return;
			}

			last_load_count = load_count;
			checkTimeout = setTimeout(checkLoadCount, check_interval);
		}

		var scrollVelocity = function()
		{
			scrollTimeout && clearTimeout(scrollTimeout);

			scroll_current_pos  = window.pageYOffset ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;

			scroll_dir = (scroll_current_pos - last_scroll_position);
			scroll_velocity = Math.abs(scroll_dir);
			scroll_velocity /= scroll_interval;

			last_scroll_position = scroll_current_pos;

			//console.log("velocity: " + scroll_velocity + " px/100ms");

			
			scrollTimeout = setTimeout(scrollVelocity, scroll_interval);
		}

		var loadVisibleImages = function()
		{

			if(scroll_velocity>max_velocity)
				return true;

			var $setitems = $('.item-container[data-pre-image]');

			if($setitems.length<1 && (max_pages==0 || cur_page == max_pages))
			{
				stopSeeker();
				return false;
			}

			$setitems.each(function()
			{
				var _ = $(this);

				if(_.is("[preloading]"))
					return true;

				var _img = _.attr('data-pre-image');

				if(_.isOnScreen(visibility_offset, scroll_dir))
				{
					_.attr("preloading","");
					preloadImage(_img, function()
					{
						_.css("background-image", "url("+_img+")")
						.removeAttr("data-pre-image")
						.removeAttr("preloading");

						toggleLoading(_, false);
						load_count++;
						//console.log(load_count + " - " + _img);
					});
				}
			});
			return true;
		};


		var init = function()
		{
			seekVisibleImages();
			checkLoadCount();
			scrollVelocity();

			$(window).on("scroll touchmove lookup resize", function sch(e)
			{
				if(shouldUnbind)
				{
					$(window).off(e);
					return;
				}

				if(!shouldSeek)
				{
					shouldSeek = true;
					seekVisibleImages();
					checkTimeout = setTimeout(checkLoadCount, check_interval);
				}
			});
		};

		init();
	}

	ImageSeeker.Start = function()
	{
		return new ImageSeeker();
	}
	window.ImageSeeker = ImageSeeker;
})();