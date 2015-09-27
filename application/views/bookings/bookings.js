(function($) {
	$.bookings = {
		section : '',
		page : '',
		init : function() {
			$('.frequency').change(function(){
				$.bookings.frequency_change($(this));
			});
			$.bookings.frequency_change($('.frequency'));
			
			$('.people').change(function(){
				cap = parseInt($(this).val())+1;
				$('.rooms').children('option').each(function(){
					if($(this).attr('capacity') < cap){
						$(this).attr('disabled', 'disabled');
					}else{
						$(this).removeAttr('disabled');
					}
				});
			});
			
		},
		frequency_change : function(frequency){
			if(frequency.val() == 'No repeat'){
				$('.last-date').hide();
			}else{
				$('.last-date').show();
			}
		}
	};
})(jQuery);