(function($) {
	$.extend($.expr[":"], {
		"containsIN": function(elem, i, match, array) {
			return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
	});
	$.markets = {
		accordion: true,
		list: function() {
			$('#grocery-order').html('');
			var noGrocery = true;
			$('.grocery-amount').map(function() {
				var groceryValue = $(this).val();
				var groceryItem = $(this).siblings('label').children('.grocery-name').html();
				if(groceryValue != '') {
					noGrocery = false
					$('#grocery-order').append('<li>' + groceryValue + ' ' + groceryItem +'</li>');
				}
			});
			if(noGrocery == true) {
				$('#grocery-order').append('<li>No groceries</li>');
			}
		},
		init : function() {
			$.markets.list();
			$('#grocery-search').on('input change', function() {
				$.markets.search();
			});
			$('.grocery-amount').on('change', function() {
				$.markets.list();
			});
			if($('.grocery-favourite').length > 0) {
				var showFav = 'Show only items from your past orders';
				var showAll = 'Show all items';
				$('#grocery-search').parent().after('<li><a href="#" id="grocery-favourite-button" class="jcr-button inline-block" title="' + showFav + '"><span class="ui-icon ui-icon-star inline-block"></span>Show Favourites</a>');
				$('#grocery-favourite-button').tooltip().on('click touch', function(e) {
					e.preventDefault();
					$.markets.showFavourites();
				});
			}
		},
		favourites: false,
		showFavourites: function() {
			if($.markets.favourites === false) {
				$.markets.reset();
				$('#accordion').accordion("destroy");
				$('#grocery-favourite-button').html('<span class="ui-icon ui-icon-star inline-block"></span>Show All');
				$('#accordion h3').hide();
				$('#accordion label').closest('li').hide();
				$('#accordion label').has('.grocery-favourite').closest('li').show();
				$.markets.favourites = true;
			} else {
				$.markets.reset();
			}
		},
		reset: function() {
			$('#grocery-favourite-button').html('<span class="ui-icon ui-icon-star inline-block"></span>Show Favourites');
			$('#grocery-clear').remove();
			$('#grocery-search').val('');
			$('#accordion li, #accordion h3').show();
			$('#accordion').accordion({
				heightStyle: "content"
			});
			$.markets.accordion = true;
			$.markets.favourites = false;
		},
		search : function() {
			var searchbox = $('#grocery-search').val();
			if(searchbox == '') {
				$.markets.reset();
			} else {
				if($.markets.accordion) {
					$.markets.reset();
					$('#accordion').accordion("destroy");
					$.markets.accordion = false;
					$('#grocery-search').val(searchbox);
					$('#grocery-search').after('<a href="#" id="grocery-clear" class="jcr-button inline-block" title="Clear the search field and show all groceries"><span class="ui-icon ui-icon-close inline-block"></span>Show all</a>');
					$('#grocery-clear').tooltip().on('click touch', function(e) {
						e.preventDefault();
						$.markets.reset();
					});
				}
				$('#accordion h3').hide();
				$('#accordion label:not(:containsIN('+ searchbox +'))').closest('li').hide();
				$('#accordion label:containsIN('+ searchbox +')').closest('li').show();
				
			}
		}
	};
})(jQuery);