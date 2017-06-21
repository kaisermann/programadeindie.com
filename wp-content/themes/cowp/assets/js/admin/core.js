
var $gal = $(".mosaic");
var $sizer = $('.mosaic-sizer');

var cols = 6;
var rows = 3;

function loadSetAdmin()
{
	$gal.find('.card-set').each( function( i, itemElem ) {
		
		$gal.packery( 'bindDraggabillyEvents', new Draggabilly(itemElem,
		{
			handle: '.move-handler'
		}));
	});

	$gal.packery( 'on', 'dragItemPositioned',itemDragged);

	$("body")
	.on("click", '.foto-select-btn', function(e){e.preventDefault();})
	.on("click", '.foto-select-btn li', function()
	{		
		var $li = $(this);
		var $size = $li.attr("value");
		var $ul = $li.parent();
		var $card = $ul.parent().parent();

		var request = '';

		var cur_col = parseInt($li.attr("data-col"));
		var cur_row = parseInt($li.attr("data-row"));

		toggleLoading($card, true, "size-change");

		$ul.find(".current").removeClass("current");
		$li.addClass('current');

		$ul.find(".selected").removeClass("selected");

		for(var i = 1; i <= cur_row; i++){
			for(var ii = 1; ii <= cur_col; ii++){
				$ul.find("li").eq((ii + cols*(i-1))-1).addClass("selected");
			}
		}

		var $item_type = $card.attr("data-type");
		var $order = $card.attr("data-order");

		switch($item_type)
		{
			case 'imagem':
			var requeststr;

			$imgid = $card.find("a").attr("data-img-id");
			requeststr = 'action=update_card_set_size&set_id='+pid+'&order='+$order+"&img_id="+$imgid+"&type="+$item_type+"&size="+$size;

			$.get( ajax_request_url,requeststr, function(response)
			{
				if(response.url!=null)
				{
					$card.find(".item-container").css("background-image","url("+response.url+")");
					$card.removeClass($card.attr("data-size"))
					.addClass($size)
					.attr("data-size", $size);
					toggleLoading($card, false, "size-change");
					$gal.packery( 'layout' );
				}
			}, "json");
			break;

			case 'texto':
			case 'video':
			case 'void':
			var requeststr = 'action=update_card_set_size&set_id='+pid+'&order='+$order+"&type="+$item_type+"&size="+$size;

			$.get(ajax_request_url,requeststr, function(response)
			{
				$card.removeClass($card.attr("data-size"))
				.addClass($size)
				.attr("data-size", $size);

				$gal.packery( 'layout' );
				toggleLoading($card, false, "size-change");

				textResize();

			}, "json");
			break;
		}
	});

$(".foto-select-btn li").hover(function()
{
	var $li = $(this);
	var $ul = $li.parent();

	var cur_col = parseInt($li.attr("data-col"));
	var cur_row = parseInt($li.attr("data-row"));

	$ul.find(".hover").removeClass("hover");

	for(var i = 1; i <= cur_row; i++)
		for(var ii = 1; ii <= cur_col; ii++)
			$ul.find("li").eq((ii + cols*(i-1))-1).addClass("hover");
	})
.parent()
.mouseout(function()
{
	$(this).find("li").removeClass("hover");
});
}

function itemDragged(draggedItem)
{
	var $moveditem = $(draggedItem.element);

	var old_poss = [];
	var new_poss = [];

	var counter = 0;
	$gal.packery('getItemElements').forEach(function(i)
	{
		var oldorder = parseInt($(i).attr("data-order"));
		if(oldorder!=counter)
		{
			old_poss.push(oldorder);
			new_poss.push(counter);
		}
		$(i).attr("data-order", counter++);
	});

	if(old_poss.length==0 || new_poss.length==0 || old_poss.length!=new_poss.length)
	{
		setTimeout(function(){$gal.packery('layout');},100);
		return;
	}

	console.log(old_poss.toString());
	console.log(new_poss.toString());

	var requeststr = 'action=update_set_order&pid='+pid + '&old_list='+old_poss.toString()+"&new_list="+new_poss.toString();

	toggleLoading($('body'), true, "dragged", true);

	$.get( ajax_request_url, requeststr, 
		function( response )
		{
			setTimeout(function()
			{
				toggleLoading($('body'), false, "dragged");
				$gal.packery( 'layout' );
			}, 100)
		});
}