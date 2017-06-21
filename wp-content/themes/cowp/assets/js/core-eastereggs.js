function initEastereggs()
{
	cheet('b e e r', cheetBeer);
	cheet('h o f f', cheetHoff);
	cheet('m i a u', cheetMiau);
	cheet('c a g e', cheetCage);
	cheet('m e i', cheetCorgi);
	cheet('a n i m a l', cheetCreature);
	cheet('p u g s', cheetPugs);
	cheet('c o m i c', function(){$("*").css("font-family","Comic Sans MS");});
}

function cheetBeer() { changeAllImages(["http://beerhold.it"]); }
function cheetHoff() { changeAllImages(["http://place-hoff.com"]); }
function cheetCreature() { changeAllImages(["http://placecreature.com"]); }
function cheetMiau() { changeAllImages(["http://placekitten.com"]); }
function cheetCage() { changeAllImages(["http://placecage.com","http://placecage.com/g"]); }
function cheetCorgi() { changeAllImages(["http://placecorgi.com"]); }
function cheetPugs() { changeAllImages(["http://placepu.gs"]); }

function changeAllImages(url)
{
	var imgs = $(".item-container");
	var total = url.length-1;
	var rand;

	imgs.each(function()
	{
		if(url.length>1)
			rand = Math.floor((Math.random() * total+1));
		else
			rand = 0;
		var new_url = url[rand]+"/"+$(this).width()+"/"+$(this).innerHeight();

		$(this).attr("data-image", new_url);
		$(this).css("background-image", "url("+new_url+")");
	});
}