(function ($) {
	$.contact = {
		init: function () {
			if ($('#butler_map_container').length > 0) {
				if (typeof google === 'object' && typeof google.maps === 'object') $.contact.mapsStart();
				else $.getScript(window.location.protocol + '//maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyA_numsmkw_vD8StdrvO71SVAwip7XDQLI&callback=$.contact.mapsStart');
			}
			$('select').after($('<button />', { 'type': 'button' }).text('Select multiple').one('click', function () {
				$(this).siblings('select').attr({ 'multiple': 'yes' });
				$(this).click(function () {
					var sel = $(this).siblings('select');
					if (sel.attr('size') == 10) {
						sel.attr('size', 3);
						$(this).text('Expand');
					}
					else {
						sel.attr('size', 10);
						$(this).text('Minimise');
					}
					return false;
				}).click();
				return false;
			}));
		},

		mapsStart: function () {
			var butlerposition = new google.maps.LatLng(54.759451, -1.580143)
			var settings = {
				zoom: 15,
				center: butlerposition,
				mapTypeId: google.maps.MapTypeId.HYBRID
			};
			var Butler_CollegeMarker = new google.maps.Marker({
				position: butlerposition,
				map: new google.maps.Map(document.getElementById("butler_map_container"), settings)
			});
		}
	}
})(jQuery);