(function($) {
	$.bookings = {
		section : '',
		page : '',
		init : function() {
			$('.frequency').change(function(){
				$.bookings.frequency_change($(this));
			});
			$.bookings.frequency_change($('.frequency'));
			
			$('.rooms').change(function(){
				$.bookings.rooms_change($(this));
				$('.equiptment_selection').children('option').each(function(){
					if($(this).attr('which_room') !== 'id'){
						$(this).attr('disabled', 'disabled');
					}else{
						$(this).removeAttr('disabled');
					}
				});
				$('.layout_selection').children('option').each(function(){
					if($(this).attr('which_room') !== 'id'){
						$(this).attr('disabled', 'disabled');
					}else{
						$(this).removeAttr('disabled');
					} 
				});
			});
			$.bookings.rooms_change($('.rooms'));

			$('.people').change(function(){
				cap = parseInt($(this).val());
				$('.rooms').children('option').each(function(){
					if($(this).attr('capacity') < cap){
						$(this).attr('disabled', 'disabled');
					}else{
						$(this).removeAttr('disabled');
					}
				});
			});
			
			$('.room-booking-form').submit(function(event){
                            if(typeof(event.originalEvent) === 'undefined'){
                                return;
                            }
                            event.preventDefault();
                            $('<div/>').html('Confirm\n\
                                <table>\n\
                                <tr><td>Title</td><td>'+$(".title").val()+'</td>\n\
                                <td style="border:0"></td>\n\
                                <td>First date</td><td>'+$(".start-date").children('input').val()+'</td>\n\
                                <tr><td>Phone number</td><td>'+$(".phone").val()+'</td>\n\
                                <td style="border:0"></td>\n\
                                <td>Start time</td><td>'+$(".start-hour").val()+":"+$(".start-min").val()+'</td>\n\
                                <tr><td>Room</td><td>'+$(".rooms").children('[value='+$(".rooms").val()+']').html()+'</td>\n\
                                <td style="border:0"></td>\n\
                                <td>End time</td><td>'+$(".end-hour").val()+":"+$(".end-min").val()+'</td>\n\
                                <tr><td>Number of people</td><td>'+$(".people").val()+'</td>\n\
                                <td style="border:0"></td>\n\
                                <td>Frequency</td><td>'+$(".frequency").children('[value='+$(".frequency").val()+']').html()+'</td>\n\
                                <tr><td>Layout</td><td>'+$(".layouts").children('[value='+$(".layouts").val()+']').html()+'</td>\n\
                                <td style="border:0"></td>\n\
                                <td>Last date</td><td>'+$(".last-date").children('input').val()+'</td>\n\
                                <tr><td>Equiptment</td><td>'+$('input[room-id='+$(".rooms").val()+']:checked').map(function(){ return $(this).val() }).get().join(', ')+'</td>\n\
                                </table>'
                            ).dialog({
                                buttons: [
                                  {
                                    text: "Ok",
                                    icons: {
                                      primary: "ui-icon-check"
                                    },
                                    click: function() {
                                      $( this ).dialog( "close" );
                                      $('.room-booking-form').submit();
                                    }
                                    // Uncommenting the following line would hide the text,
                                    // resulting in the label being used as a tooltip
                                    //showText: false
                                  },
                                  {
                                    text: "Cancel",
                                    icons: {
                                      primary: "ui-icon-close"
                                    },
                                    click: function() {
                                      $( this ).dialog( "close" );
                                    }
                                    // Uncommenting the following line would hide the text,
                                    // resulting in the label being used as a tooltip
                                    //showText: false
                                  }
                                ],
                                width:1000
                             });
			});	

			
		},
		frequency_change : function(frequency){
			if(frequency.val() == 0){
				$('.last-date').hide();
			}else{
				$('.last-date').show();
			}
		},
		
		rooms_change : function(room_id){
			$('.equiptment_selection').children().each(function(){
				if($(this).children('input').attr('room-id') == $('.rooms').val()){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
			//$('.layout_selection').show();
			$('.layout_selection').children('select').children().each(function(){
				if($(this).attr('room-id') == $('.rooms').val()){
					$(this).show();
					$(this).addClass('visible-layout');
				}else{
					$(this).hide();
					$(this).removeClass('visible-layout');
				}
			});
			if($('.visible-layout').length == 0){
				$('.layout_selection').hide();
			}else{
				$('.layout_selection').show();
			}
			$('.layouts').val('');
			
		}
	};
})(jQuery);