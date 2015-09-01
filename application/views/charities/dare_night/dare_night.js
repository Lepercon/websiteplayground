(function ($) {
	$.dare_night = {
		init: function () {
		
			$('#dare-team-info').submit(function(event){				
				$('#team-form-helper').html('');
				$('#team-form-helper').css({display:'none'});
				if($('#teamname').val().length > 50){
					$('#team-form-helper').html($('#team-form-helper').html()+'Your Team Name Must Be Under 50 Characters<br>');
				}
				for(var i=1;i<=6;i++){
					name = $('#member-'+i).val();
					if(name == ''){
						$('#member-'+i+'-hidden').val('');

					}else{
						id = -1;
						$('#fullnameslist').children().each(function(){
							if($(this).html() == name){
								id = $(this).attr('id');
							}
						});
						if(id == -1){
							$('#team-form-helper').html($('#team-form-helper').html()+'Team Member '+i+' is invalid<br>');
						}else{
							$('#member-'+i+'-hidden').val(id);
						}
					}
				}				
				if($('#team-form-helper').html() == ''){
					if($('#teamname').val() == ''){
						$('#teamname').val('Team ' + $('#team_number').html());
					}
				}else{
					event.preventDefault();
					$('#team-form-helper').addClass('validation_errors');
					$('#team-form-helper').removeClass('validation_success');
					$('#team-form-helper').css({display:'block'});
				}
			});
			
			$('.delete-photo-button').click(function(event){
				event.preventDefault();
				var button = $(this);
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete that photo?<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Delete",
					buttons: {
						"Delete": function () {
							$(this).dialog("close");
							console.log(button.parent('.dare-delete-photo'));
							button.parent('.dare-delete-photo').submit();
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});

			});
			
			$('.javascript-warning').remove();
			var is_safari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
			if(is_safari){
				$('#no-safari').show();
			}
			
			setTimeout(function(){
				if($('#tab_open').html() != ''){
					$('#accordion').children('.'+$('#tab_open').html()).click();
				}
			},500);
			
		}
	};
})(jQuery);