FastClick.attach(document.body);
!function(a){var b=/iPhone/i,c=/iPod/i,d=/iPad/i,e=/(?=.*\bAndroid\b)(?=.*\bMobile\b)/i,f=/Android/i,g=/IEMobile/i,h=/(?=.*\bWindows\b)(?=.*\bARM\b)/i,i=/BlackBerry/i,j=/BB10/i,k=/Opera Mini/i,l=/(?=.*\bFirefox\b)(?=.*\bMobile\b)/i,m=new RegExp("(?:Nexus 7|BNTV250|Kindle Fire|Silk|GT-P1000)","i"),n=function(a,b){return a.test(b)},o=function(a){var o=a||navigator.userAgent;return this.apple={phone:n(b,o),ipod:n(c,o),tablet:n(d,o),device:n(b,o)||n(c,o)||n(d,o)},this.android={phone:n(e,o),tablet:!n(e,o)&&n(f,o),device:n(e,o)||n(f,o)},this.windows={phone:n(g,o),tablet:n(h,o),device:n(g,o)||n(h,o)},this.other={blackberry:n(i,o),blackberry10:n(j,o),opera:n(k,o),firefox:n(l,o),device:n(i,o)||n(j,o)||n(k,o)||n(l,o)},this.seven_inch=n(m,o),this.any=this.apple.device||this.android.device||this.windows.device||this.other.device||this.seven_inch,this.phone=this.apple.phone||this.android.phone||this.windows.phone,this.tablet=this.apple.tablet||this.android.tablet||this.windows.tablet,"undefined"==typeof window?this:void 0},p=function(){var a=new o;return a.Class=o,a};"undefined"!=typeof module&&module.exports&&"undefined"==typeof window?module.exports=o:"undefined"!=typeof module&&module.exports&&"undefined"!=typeof window?module.exports=p():"function"==typeof define&&define.amd?define("isMobile",[],a.isMobile=p()):a.isMobile=p()}(this);

(function($)
{
	var $mos = $(".mosaic");
	var $ms = $(".main-slider");
	var $ap = $(".archive-list");
	var $mobilebtn = $(".mobile-btn-wrapper");

	var $titles = $(".title-wrapper");
	var $logo = $(".logo");

	// LOGO
	if(!isMobile.any && $logo.length>0)
	{
		var logo_content = $logo.html();
		$logo.html("");

		for(var i=0;i<logo_content.length;i++)
			$logo.append("<span>"+logo_content[i]+"</span>");
	}

	$(window).on("scroll", function(){
		($(this).scrollTop() > 50)?$('#topbutton').addClass('active'):$('#topbutton').removeClass('active');
	});

	$('#topbutton').on("click",function(){
		$('html, body').animate({scrollTop : 0},500);
		return false;
	});

	$mobilebtn.on("click",function(){$mobilebtn.toggleClass("active");$("header nav").toggleClass("active");});

	//Home
	initHome($mos, $ms, $ap);

	//Mosaic & set
	initMosaic($mos);

	//Eastereggs
	initEastereggs();

	//Loading generico
	/*
	$(window).on("scroll resize lookup", loadVisibleImages);
	loadVisibleImages();
	*/
	ImageSeeker.Start();


	$(window).on("resize", checkResize);
	checkResize();

})(jQuery);

function checkResize()
{
	textResize();

	var mainheight = $(window).innerHeight()-$("footer").innerHeight()-$("header").innerHeight()-$("#wpadminbar").innerHeight();
	$("main").css("min-height",mainheight);

	var $form = $(".wpcf7 .row");
	if($form.length>0)
	{
		$text = $form.find("textarea");
		$text.css("height", "auto");
		$text.css("height", mainheight - ($form.find("input").innerHeight())*3);
	}
}

function changeLocation(url)
{
	var nohttp = url.replace("http://","").replace("https://","");
	var firstsla = nohttp.indexOf("/");
	var pathpos = url.indexOf(nohttp);
	var path = url.substring(pathpos + firstsla);

	if (typeof window.history.replaceState == "function") 
	{
		history.replaceState('', document.title, path);

		if(window.addthis!==null && window.addthis!==undefined)
		{
			addthis.ost = 0;
			addthis.update('share', 'url', window.location.href);
			addthis.update('share', 'title', window.document.title); 
			addthis.ready();
		}
	}
}

function getUrlValue(name, urlpart){
	if(name=(new RegExp('[#?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location[urlpart]))
		return decodeURIComponent(name[1]);
}
