(function ($) {
	$.ballot = {
		init: function () {
			var availableTags = [];
			$('#users-list').children('p').each(function(){
				availableTags.push({value:$(this).text(),id:$(this).attr('value')});
			});
        	$('.name-selection').autocomplete({
		      	source: availableTags,
		      	change: function(event, user){
		      		if(user.item == null){
		    			$(this).siblings('.user-id').val('');
		    		}else{
		    			$(this).siblings('.user-id').val(user.item.id);
		    		}
		      	}
		    });
		    
		    
		    $('.person-option select').change(function(){
		    	$.ballot.update_price($(this).closest('.person-option'));
		    });
		    
		},
		
		update_price: function(span_elem){
			
		}
	}
})(jQuery);