function fixMapHeight()
{
	var h = jQuery("main").innerHeight();
	jQuery("#map-canvas").css("height", (h < 300) ? 300 : h + "px");
}

jQuery(document).ready(function ($)
{
	var ib = new InfoBox();
	fixMapHeight();
	$(window).on("resize", fixMapHeight);

	var map;
	var INDIE_MAP_ID = 'indie_map';
	var mapOptions = 
	{
		zoom: 9,
		center: new google.maps.LatLng(-22.743454,-43.167343),
		panControl: false,
		zoomControl: true,
		mapTypeControl: false,
		scaleControl: false,
		streetViewControl: false,
		overviewMapControl: false,
		mapTypeControlOptions:
		{
			mapTypeIds: [google.maps.MapTypeId.ROADMAP, INDIE_MAP_ID]
		},
		mapTypeId: INDIE_MAP_ID
	};
	var featureOpts=[{featureType:"water",elementType:"geometry",stylers:[{color:"#000000"},{lightness:17}]},{featureType:"landscape",elementType:"geometry",stylers:[{color:"#000000"},{lightness:20}]},{featureType:"road.highway",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:17}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:29},{weight:.2}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#000000"},{lightness:18}]},{featureType:"road.local",elementType:"geometry",stylers:[{color:"#000000"},{lightness:16}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#000000"},{lightness:21}]},{elementType:"labels.text.stroke",stylers:[{visibility:"on"},{color:"#000000"},{lightness:16}]},{elementType:"labels.text.fill",stylers:[{saturation:36},{color:"#000000"},{lightness:40}]},{elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"geometry",stylers:[{color:"#000000"},{lightness:19}]},{featureType:"administrative",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:20}]},{featureType:"administrative",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:17},{weight:1.2}]}];
	
	var styledMapOptions = {name: "Indie Map"};

	var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	map.mapTypes.set(INDIE_MAP_ID, customMapType);

	setMarkers(ib, map, infoBoxes);
});

function setMarker(map, lat, lon, title, zindex, html)
{
	return new google.maps.Marker(
	{
		position: new google.maps.LatLng(lat, lon),
		map: map,
		title: title,
		zIndex: zindex,
		html: html,
		icon: pinUrl
	});

}

function setMarkers(ib, map, markers)
{

	for (var i = 0; i < markers.length; i++)
	{
		var marker = setMarker(map, markers[i][1], markers[i][2], markers[i][0], markers[i][3], markers[i][4]);

		google.maps.event.addListener(marker, "click", function (e)
		{
			var boxText = document.createElement("div");
			boxText.style.cssText = "border: none; background: transparent;";
			boxText.innerHTML = this.html;
			ib.close();
			ib.setOptions(
			{
				content: boxText,
				disableAutoPan: false,
				maxWidth: 0,
				pixelOffset: new google.maps.Size(-125,-135),
				zIndex: null,
				boxStyle:
				{
					opacity: 1,
					width: "250px",
				},
				closeBoxMargin: "15px 7px 2px 2px",
				closeBoxURL: closeBtnUrl,
				infoBoxClearance: new google.maps.Size(1, 1),
				isHidden: false,
				pane: "floatPane",
				enableEventPropagation: false
			});
			ib.open(map, this);
		});
	}
}