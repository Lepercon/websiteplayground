(function ($) {
	$.finance = {
		init: function() {		
			$('#removefromlist').click(function() {
				$('#namelist :selected').remove();
			});	
			
			$("#tabs").tabs({
		      collapsible: true,
		      hide: true
		    });
			
			$('.authorise-link').click(function(event){
				event.preventDefault();
				url = $(this).attr('href');
				var button = $(this);
				var cont = $('<div />').addClass('delete-conf-box');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Authorise",
					width:500,
					buttons: {
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
				cont.html('Loading...');
				$.ajax({
					type: "POST",
					url: url,
					error: function() {
						cont.dialog('close');
						$.common.notify('Error...');
					},
					success: function(e) {
						cont.html(JSON.parse(e).html);
						cont.find('.jcr-form').submit(function(event){
							event.preventDefault();
							url = $(this).attr('action');
							$.ajax({
								type: "POST",
								url: url,
								data:{
									code:$(this).find('input').val(),
									authorise:'Authorise'
								},
								error: function() {
									cont.dialog('close');
									$.common.notify('Error...');
								},
								success: function(e) {
									console.log(e);
									location.reload();
								}
							});
						});
					}
				});

				return false;
			});	
			
			$('input, textarea, select').blur(function(){
				$(this).addClass('sel');
			});
			
			$('#account-number, #sort-code').on('focus blur keyup', function(){
				$(this).get(0).setCustomValidity('');
				if(!$(this).get(0).checkValidity()){
					$(this).get(0).setCustomValidity('Please enter a valid '+$(this).attr('placeholder')+'.');
				}
			});
			
			$('.claim-edit-form').submit(function(a){
				
				if($('#claim-method').val() == 2 && ($('#account-number').val() != 'Hidden' || $('#sort-code').val() != 'Hidden')){
					if(typeof(a.isTrigger) == 'undefined'){
						event.preventDefault();
						
						var button = $(this);
						var cont = $('<div />').html($('#account-details-confirmation').html());
						cont.find('.acc-num').html($('#account-number').val());
						cont.find('.sort-code').html($('#sort-code').val());
						cont.dialog({
							resizable: false,
							draggable: false,
							modal: true,
							title: "Check Details",
							width:300,
							buttons: {
								Yes: function(){
									$(this).dialog("close");
									$('.claim-edit-form').submit();
								},
								Cancel: function () {
									$(this).dialog("close");
								}
							}
						});
					}
				}
			});
			/*
			*/

					
			$('#print-file').click(function(event){
				event.preventDefault();
				window.print();
			});
			$('.edit-budgets-accordion').accordion({active: parseInt($('#index_select').html())});
			$('.budgetholder').change(function(event){
				holder = $(this).val();
				
				var names = $('#fullnameslist').children();
				var id = -1;
				names.each(function(){
					if($(this).html() == holder){
						id = $(this).attr('id');
					}
				});
				$(this).siblings('#budgetholder_id').val(id);
			});
			
			$('.remove-file').click(function(event){
				event.preventDefault();
				file = $(this).siblings('#file-name').html();
				claim_id = $('#claim-id').html();
				
				$.ajax({
					type: "POST",
					url: script_url + 'finance/claims/remove_file',
					data: {
						file: file,
						claim_id:claim_id 
					},
					error: function() {
						$.common.notify('This file could not be removed');
					},
					success: function() {
						location.reload();
					}
				});
			});
			
			$('#claim-method').change(function(){
				if($('#claim-method').val() == 2){
					$('#transfer-details').show();
					$('#account-number').focus();
					$('#account-number').attr('required', 'required');
					$('#sort-code').attr('required', 'required');
				}else{
					$('#transfer-details').hide();
					$('#account-number').removeAttr('required');
					$('#sort-code').removeAttr('required');
				}
			});
			
			console.log('a');
			if($('.finance-feedback').length > 0){
				console.log('a');
				d = $('<div/>').html('<p>This page is still being worked on, if you have any feedback please click here</p>');
				$('#content').prepend(d);
			}
			
			$('#sort-code').keyup(function(e){
				if(e.keyCode >= 37 && e.keyCode <= 40){
					return;
				}
				if($(this).get(0).selectionStart != $(this).get(0).selectionEnd){
					return;
				}
				pos = $(this).get(0).selectionStart;
				text = $(this).val();
				pos -= text.length;
				text = text.split('-').join('');
				if(text.length >= 3){
					text = text.substr(0,2) + '-' + text.substr(2);
				}
				if(text.length >= 6){
					text = text.substr(0,5) + '-' + text.substr(5);
				}
				pos += text.length;
				if(pos == 2 || pos == 5){
					pos++;
				}
				$(this).val(text);
				$(this).get(0).setSelectionRange(pos,pos);
				/*text = $(this).val().split('-').join('');
				if(text.length == 6){
					$('#sort-code').removeClass('bad-code');
					$('#sort-code').removeClass('good-code');
					$('#sort-code').addClass('load-code');
					$.ajax({
						type: "POST",
						url: script_url + 'finance/sortcode',
						data: {
							sortcode: text
						},
						error: function() {
							$.common.notify('The sort code could not be validated');
						},
						success: function(d) {
							data = JSON.parse(JSON.parse(d).html);
							console.log(data);
							$('#sort-code').removeClass('load-code');
							if(data.resultCode == '01'){
								$('#sort-code').addClass('good-code');
							}else{
								$('#sort-code').addClass('bad-code');
							}
						}
					});
				}else{
					$('#sort-code').removeClass('good-code');
					$('#sort-code').addClass('bad-code');
				}*/
			});
			
			
			$('.button-mark-paid').click(function(event) {
				event.preventDefault();
				button = $(this);
				claimid = button.siblings('#claim-id').text();
				$.ajax({
					type: "POST",
					url: script_url + 'finance/claims/change_claim_status',
					data: {
						claimid: claimid,
						newstatus: (button[0].innerHTML == 'Unmark As Paid'?1:2)
					},
					error: function() {
						$.common.notify('The status could not be updated');
					},
					success: function() {
						button[0].innerHTML = (button[0].innerHTML == 'Unmark As Paid'?'Mark Paid':'Unmark As Paid');
					}
				});	
			});
			
			$('.button-approve-claim').click(function(event) {
				event.preventDefault();
				button = $(this);
				claimid = button.siblings('#claim-id').text();
				$.ajax({
					type: "POST",
					url: script_url + 'finance/claims/change_claim_status',
					data: {
						claimid: claimid,
						newstatus: 1 
					},
					error: function() {
						$.common.notify('The status could not be updated');
					},
					success: function() {
						button.siblings('#claim-id')[0].innerHTML = 'Approved';
						button.siblings('#claim-id')[0].style.display = ''
						button.parent().siblings('.claim-status').text('Waiting For Payment');
						button[0].remove();
					}
				});
			});
			
			$('#claim-add-budget').submit(function(event) {				
				event.preventDefault();
				
				var id = $('#nameentry-id').val();
				var budgetname = $('#newname').val();
				if(id == ''){
					$('#error-message').html('*Invalid Input');
				}else{
					$('#error-message').html('');			
					$.ajax({
						type: "POST",
						url: script_url + 'finance/claims/add_budget',
						data: {
							newname: budgetname,
							holdername: id
						},
						error: function() {
							$.common.notify('The budget could not be added');
						},
						success: function() {
							$('#budgets-list').append('<li>'+budgetname + ' - ' + $('#nameentry').val()+'</li>');
							$('#nameentry-id, #newname, #nameentry').val('');
							$('#newname').focus();
						}
					});				
				}

			});
			
			

			$('#claim-amount, #invoice-amount').blur(function(event) {
                if($(this).val().substring(0,1) == '\u00A3'){
                    var val = parseFloat($(this).val().substring(1));
                }else{
                    var val = parseFloat($(this).val());
                }
                if(!isNaN(val)){
                    $(this).val('\u00A3'+val.toFixed(2))
                }else{
                    $(this).val('');
                }
			});

			$('.invoice-paid').click(function(event) {
				event.preventDefault();
				var button = $(this);
				var status = button.siblings('.invoice-status').text();
				$.ajax({
					type:"POST",
					url: script_url + 'finance/claims/admin_mark_paid',
					data: {
						'payment_id': button.siblings('.invoice-id').text(),
						'status': (status == '1' ? '0' : '1')
					},
					success: function(){
						button.text('Mark as ' + (status == '1' ? 'P':'Unp') + 'aid');
						button.attr('title', 'Mark This Entry As ' + (status=='1' ? 'P':'Unp') + 'aid')
						button.parent().siblings('.invoice-paid').text(status=='1'?'NO':'YES');
						button.siblings('.invoice-status').text(status=='1'?'0':'1')
					},
					error: function(){
						$.common.notify('The payment status could not be updated');
					}
				});
			});
            $('#claim-submit-form').submit(function(event){
				
                var inputs = ['claim-name', 'invoice-amount', 'claim-item', 'claim-budget'];
                var helpers = ['claim-name-helper', 'invoice-amount-helper', 'claim-item-helper', 'claim-budget-helper'];
                failed = false;
                
                for (i = 0; i < inputs.length; i++) {
                	if($('#'+inputs[i]).val() == ""){
                		failed = true;
                		$('#'+helpers[i])[0].innerHTML = '*This Field Is Required';
                	}else{
                		$('#'+helpers[i])[0].innerHTML = '';
                	}
                }
                if(failed){
	                event.preventDefault();
	            }
	            
            });
            
			$('.invoice-remove').click(function(event) {
				event.preventDefault();
				var button = $(this);
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete that invoice?<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Delete",
					buttons: {
						"Delete": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'finance/claims/remove_invoice/' + button.siblings('.invoice-id').text(),
								error: function() {
									$.common.notify('The invoice could not be deleted');
								},
								success: function() {
									button.closest('tr').remove();
								}
							});
							$(this).dialog("close");
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
				return false;
			});
			$('.invoice-remove-member').click(function(event) {
				event.preventDefault();
				var button = $(this);
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to remove this member?<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Remove",
					buttons: {
						"Delete": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'finance/claims/remove_member/' + button.siblings('.invoice-id').text(),
								error: function() {
									$.common.notify('The member could not be removed');
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
				return false;
			});
			
		}
	};
})(jQuery);