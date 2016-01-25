(function ($) {
	$.invoices = {
		init: function() {
			$('#invoice-add-members').click(function(event) {
				event.preventDefault();
				ids = '';
			    $('#namelist').children().each(function(){
			    	id = $(this).attr('id');
			    	ids += id + ',';
			    });
			    $('#ids').val(ids);
			    $('#invoice-add-members-form').submit();
			});
			$('#invoice-add-member').click(function(){
				$.invoices.addToList();
			});
			$('#nameentry').keydown(function(e){
				if(e.keyCode == 13)
				 	$.invoices.addToList();
			});
			$('.invoice-mark-paid').click(function(event) {
				event.preventDefault();
				var button = $(this);
				var old_status = button.siblings('.invoice-marked-status').text()=='1';
				$.ajax({
					type: "POST",
					url: script_url + 'finance/invoices/mark_paid/',
					data:{
						id:button.siblings('.invoice-id').text(),
						new_status: (old_status?'0':'1')
					},
					error: function() {
						$.common.notify('The status of this invoice could not be changed.');
					},
					success: function() {						
						b = button
						var new_status = old_status?'0':'1';
						button.siblings('.invoice-marked-status').text(new_status);
						var new_text_status = old_status?'Mark As Paid':'Unmark As Paid';
						button.html(new_text_status);
						$('#astrix').text(old_status?'':'*');
					}
				});
			});
			$('.remove-member').click(function(event){
				event.preventDefault();
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to remove this member?<br />');
				button = $(this);
				console.log(button.closest('div'));
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Removal",
					buttons: {
						"Remove": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'finance/invoices/remove_member/',
								data: {
									user: button.siblings('.member-id').html(),
									group: $('#group-id').html()
								},
								error: function() {
									$.common.notify('The member could not be removed')
								},
								success: function() {
									button.closest('div').remove();
								}
							});
							$(this).dialog("close");
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
			});
			$('.set-privileges').click(function(event){
				event.preventDefault();
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to change this users permissions?<br />');
				button = $(this);
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Permissions",
					buttons: {
						"Change": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'finance/invoices/change_permissions/',
								data: {
									user: button.siblings('.member-id').html(),
									group: $('#group-id').html(),
									new_status: button.siblings('.admin-check').hasClass('is-admin')==true?0:1
								},
								error: function() {
									$.common.notify('Unknown Error')
								},
								success: function() {
									if(button.siblings('.admin-check').hasClass('is-admin')){
										button.siblings('.admin-check').removeClass('is-admin');
										button.html('Make Admin');
									}else{
										button.siblings('.admin-check').addClass('is-admin');
										button.html('Remove Admin');
									}
								}
							});
							$(this).dialog("close");
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
			});
			$('#new-group-owner').blur(function(){
				var newname = $(this).val();
				id = -1;
				$('#fullnameslist').children().each(function(){
					if($(this).html() == newname){
						id = $(this).attr('id');
					}
				});
				$('#new-group-owner-id').val(id);
			});
			
			$('.remind-invoice').click(function(event){
				link = $(this).attr('href');
				event.preventDefault();
				var cont = $('<div />').html('Are you sure you wish to remind all members? They get an email once a week anyway.<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Reminder",
					buttons: {
						"Send": function () {
							$(this).dialog("close");
							window.location.href = link;
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
			});
		},
		addToList: function(){
			var newname = $('#nameentry').val();
			$('#nameentry').val('');			
			var id = $('#nameentry-id').val();
			$('#nameentry-id').val('');		
			$('#namelist').append('<option id="'+id+'">'+newname+'</option>');
			$('#nameentry').focus();
		}
		
	};
})(jQuery);