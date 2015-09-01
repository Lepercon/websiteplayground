(function ($) {
	$.admin = {
		init: function () {
			clearInterval(window.message_timeout_handle);
			if ($('.editable-move-left').length > 0) {
				$('.editable-move-left').click(function () { // move from not allowed to allowed
					var parent = $(this).parent().parent();
					parent.find('.editable-pagerights-notallowed > :selected').detach().appendTo(parent.find('.editable-pagerights-allowed'));
				});
				$('.editable-move-right').click(function () { // move from allowed to not allowed
					var parent = $(this).parent().parent();
					parent.find('.editable-pagerights-allowed > :selected').detach().appendTo(parent.find('.editable-pagerights-notallowed'));
				});
			}
			var availableTags = [];
			$('#users-list').children('p').each(function(){
				availableTags.push({value:$(this).text(),id:$(this).attr('value')});
			});
			$('#name').autocomplete({
      			source: availableTags,
      			change: function(event, ui){
      				$('#user-id').val(ui.item.id);
      			}
    		});
    		
    		$('.image-upload').submit(function(){
				$('.image-upload').after('<h2>Uploading, please wait...</h2>');
			});
    		
    		$.admin.remove_user();
    		$('.add-new-user').submit(function(event){
    			event.preventDefault();
    			form = $(this);
    			id = form.children('.user_id').val();
    			if(id != ''){
    				y = form.find('select[name=year]').val();
    				year = y + '/' + (parseInt(y) + 1);
    				name = form.find('.user-list-select').val();
    				id = form.find('.user_id').val();
    				level_id = form.find('input[name=level_id]').val();
                    c = form.find('input[name=current]').is(':checked');
    				form.children('.user-list-select').val('');
    				form.children('.user_id').val('');
    				$.ajax({
						type: "POST",
						url: script_url + 'admin/level_add_user',
						data: {
							year:y,
							u_id:id,
							level_id:level_id,
                            current:c?1:0
						},
						success: function() {
							new_row = $('<tr/>').appendTo(form.closest('table').find('.in-level-table'));
                            current_form = '<a href="#" class="current-link">'+(c?'Yes':'No')+'</a><form action="'+script_url+'/admin/levels" class="jcr-form no-jsify current" method="post" accept-charset="utf-8"><input type="hidden" name="id" value="'+id+'"><input type="hidden" name="l_id" value="'+level_id+'"><input type="hidden" name="new" value="'+(c?'0':'1')+'"></form>';
		    				new_row.html('<td><span value="'+id+'" class="user-id">'+name+'</span></td><td>'+year+'</td><td>'+current_form+'</td><td><form action="'+script_url+'/admin/levels" class="jcr-form no-jsify remove-user" method="post" accept-charset="utf-8"><input type="hidden" name="id" value="'+id+'"><input type="hidden" name="l_id" value="'+level_id+'"><input type="submit" name="remove" value="Remove"></form></td>');
		    				$.admin.remove_user();
						},
						error: function() {
							$.common.notify('The user could not be added.');
						}
					});    				
    			}
    		});
    		
    		$('.display-email').change(function(){
    			$('#email').prop('disabled', $('#radio-display').is(':checked'));
    			$('#email').focus();
    		});
			$('.admin-level-delete').click(function(e) {
				e.preventDefault();
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete this admin level?');
				var del = $(this);
				cont.dialog({
					resizable: false,
					modal: true,
					buttons: {
						"Delete": function () {
							$(this).dialog("close");
							$.ajax({
								type: "POST",
								url: script_url + 'admin/delete_level/' + del.data('delete'),
								success: function() {
									$('.level-' + del.data('delete')).remove();
								},
								error: function() {
									$.common.notify('The level could not be deleted.');
								}
							});
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					},
					close: function() {
						$(this).dialog("destroy");
					}
				});
			});
			$('.admin-levels-form').submit(function() {
				var selectList = $(this).find('.level-user').find('option');
				selectList.each(function () {
					$(this).attr('selected', true);
				});
			});
			if($('#list-of-users').length > 0){
            	var availableTags = [];
				$('#list-of-users').children('p').each(function(){
					availableTags.push({value:$(this).text(),id:$(this).attr('value')});
				});
            	$('.level-notuser').autocomplete({
			      	source: availableTags,
			      	change: function(event, user){
			      		if(user.item == null){
			    			$(this).siblings('.user_id').val('');
			    		}else{
			    			$(this).siblings('.user_id').val(user.item.id);
			    		}
			      	}
			    });
            }
						
			$('.add-child-1').click(function(){
				i = $(this).attr('id');
				ii=0
				while(true){
					if($("[name='menu-"+i+"-"+ii+"']").length == 0){
						break;
					}
					ii++;
				}
				
				var level_1_entry = $('#level-1-entry').clone();
				level_1_entry.attr('id', '');
				level_1_entry.children('.name').attr('name', 'menu-'+i+'-'+ii);
				level_1_entry.children('.link').attr('name', 'link-'+i+'-'+ii);
				new_menu = $(this).parent('div').append(level_1_entry);
			});
			
			$('.add-child-2').click(function(){
				i = $(this).attr('id');
				ii=0
				while(true){
					if($("[name='menu-"+i+"-"+ii+"']").length == 0){
						break;
					}
					ii++;
				}
				
				var level_2_entry = $('#level-2-entry').clone();
				level_2_entry.attr('id', '');
				level_2_entry.children('.name').attr('name', 'menu-'+i+'-'+ii);
				level_2_entry.children('.link').attr('name', 'link-'+i+'-'+ii);
				new_menu = $(this).parent('span').append(level_2_entry);
			});
			
			$('.remove-child-1, .remove-child-2').click(function(){
				$(this).siblings('span:last').remove();
			});
			
			$('#submit-menu-form').click(function(event){
				event.preventDefault();
				form_elements = $('#header-menu-form').find('input');
				d = {submit:'Submit'};
				form_elements.each(function(){
					name = $(this).attr('name');
					value = $(this).val();
					d[name] = value;
				});
				$.ajax({
					type: "POST",
					url: script_url + 'admin/menu',
					data:d,
					success: function() {
						location.replace(script_url + 'admin/menu?newmenu=1');
					},
					error: function() {
						$.common.notify('The form could not be submitted.');
					}
				});
			});
			
			$('#cancel-menu-form').click(function(event){
				event.preventDefault();
				location.reload();
			});
			
			$('.message-input, .event').on('keyup blur change click', function(event){
				console.log('a');
				row = $(this).closest('tr');
				new_message = row.children('td').children('.message-input').val();
				if(new_message == ''){
					$('#message').html('<i>Type your message and it will preview here...</i>');
				}else{
					$('#message').html(new_message);
				}
				title = row.children('td').children('.event').children('option:selected').text();
				if(title.indexOf(' - ') == -1){
					$('#title, #dash').hide();
				}else{
					breakup = title.split(' - ');
					title = breakup[1];
					$('#title').text(title);
					$('#title, #dash').show();
				}
			});
			
			$('#new-message').on('keyup blur change click', function(){
				new_message = $(this).val();
				if(new_message == ''){
					$('#message').html('<i>Type your message and it will preview here...</i>');
				}else{
					$('#message').html(new_message);
				}
				$('#title, #dash').hide();
			});
			
			$('.disable-enable').change(function(){
				if($(this).is(":checked")) {
					$(this).siblings("input[type='text'], select").removeAttr('disabled');
				}else{
					$(this).siblings("input[type='text'], select").attr('disabled', 'disabled');
				}
			});
			$('.row-delete-check').change(function(){
				if($(this).is(":checked")) {
					$(this).closest('tr').addClass('row-selected');
					checked = true;
					$('.row-delete-check').each(function(){
						checked = checked && ($(this).attr('checked') == 'checked');
					});
					if(checked){
						$('.all-check').attr('checked', true);
					}
				}else{
					$(this).closest('tr').removeClass('row-selected');
					$('.all-check').attr('checked', false);
					console.log('a');
				}
			});
			
			$('.delete-selected').click(function(event){
				event.preventDefault();
			});
			
			$('.all-check').change(function(){
				if($(this).is(":checked")) {
					$('.row-delete-check').each(function(){
						$(this).attr('checked', true);
						$(this).closest('tr').addClass('row-selected');
					});
				}else{
					$('.row-delete-check').each(function(){
						$(this).attr('checked', false);
						$(this).closest('tr').removeClass('row-selected');
					});
				}
			});
            
            
		},
		remove_user:function(){
			$('.remove-user').submit(function(event){
				event.preventDefault();
				form = $(this);
				$.ajax({
					type: "POST",
					url: script_url + 'admin/level_remove_user',
					data:{
						u_id:form.children('input[name=id]').val(),
						l_id:form.children('input[name=l_id]').val()
					},
					success: function() {
						form.closest('tr').remove();
					},
					error: function() {
						$.common.notify('The user level could not be removed.');
					}
				});
			});
            $('.current-link').click(function(event){
                event.preventDefault();
                link = $(this);
                form = link.siblings('.jcr-form');
                current_status = link.html() == 'Yes';
                $.ajax({
					type: "POST",
					url: script_url + 'admin/level_change_status',
					data:{
						u_id:form.children('input[name=id]').val(),
						l_id:form.children('input[name=l_id]').val(),
                        new_status:(current_status?'0':'1')
					},
					success: function() {
						link.html(current_status?'No':'Yes');
					},
					error: function() {
						$.common.notify('This status could not be changed.');
					}
				});
            });
		}
	};
})(jQuery);