(function($) {
	$.liversout = {
		init : function() {
			if($('#map_container').length > 0) {
				if(typeof google === 'object' && typeof google.maps === 'object') $.liversout.mapsStart();
				else $.getScript(window.location.protocol + '//maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyA_numsmkw_vD8StdrvO71SVAwip7XDQLI&callback=$.liversout.mapsStart');
			}
			
			$.each([{id1:'#bills',id2:'#bills-cost-label',va:'0'},{id1:'#area-label',id2:'#other-label',va:'Other'}], function(i, v) {
				if($(v.id1).val() != v.va) $(v.id2).attr("disabled", "disabled");
				$(v.id1).on("change", function() {
					if($(v.id1).val() == v.va) $(v.id2).removeAttr("disabled");
					else $(v.id2).attr("disabled", "disabled");
				});
			});
		},
		mapsStart : function() {
			var settings = {
				zoom: 13,
				center: new google.maps.LatLng(54.771831, -1.569586),
				mapTypeId: google.maps.MapTypeId.HYBRID
			};
			var map = new google.maps.Map(document.getElementById("map_container"), settings);
			
			var locations = [
				{ name: 'viaduct', lat: 54.776311, lng: -1.585636 },
				{ name: 'nevilles_cross', lat: 54.766879, lng: -1.595035 },
				{ name: 'gilesgate', lat: 54.780593, lng: -1.545296 },
				{ name: 'claypath', lat: 54.779603, lng: -1.569843 },
				{ name: 'elvet', lat: 54.771831, lng: -1.569586 }
			];
						
			$.each(locations, function(i, v) {
				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(v.lat, v.lng),
					map: map,
					icon: new google.maps.MarkerImage(
						view_url+'liversout/img/areas.png',
						new google.maps.Size(100,50),
						new google.maps.Point(0,i*50),
						new google.maps.Point(50,50)
					),
					zIndex: 5000,
					shape : {coord: [1,1, 1,25, 100,25, 100,1], type: 'poly'}
				});
				google.maps.event.addListener(marker, 'click', function() { $.common.goTo(script_url+'liversout/area/'+v.name); });
			});
						
			//Butler
			var Butler_CollegeMarker = new google.maps.Marker({
				position: new google.maps.LatLng(54.759451, -1.580143),
				map: map,
				icon: new google.maps.MarkerImage(
					view_url+'liversout/img/areas.png',
					new google.maps.Size(100,50),
					new google.maps.Point(0,250),
					new google.maps.Point(50,50)
				),
				shape : {coord: [1,1,1,1], type: 'poly'}
			});
			
			n = 1;
			var image = view_url+'liversout/img/';		
			$.each($('.property-data'), function(i, v) {
				setTimeout(function() {
					if(($(v).text().split(';'))[1] != '' && ($(v).text().split(';'))[2] != '') {
						var marker = new google.maps.Marker({
							position: new google.maps.LatLng(($(v).text().split(';'))[1],($(v).text().split(';'))[2]),
							map: map,
							title: ($(v).text().split(";",4))[3],
							animation: google.maps.Animation.DROP,
							icon: image+($(v).text().split(';'))[4].split('/').join('')+'.png',
							zIndex: n
						});
						google.maps.event.addListener(marker, 'click', function() { $.common.goTo(script_url+'liversout/view_property/'+($(v).text().split(';'))[0]); });
					}
				}, 100*n++);
			});
		}
	};	
})(jQuery);