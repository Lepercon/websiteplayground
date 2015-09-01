(function ($) {
	$.alumni = {
		init: function() {
			var count = 0;
			if(typeof($('#cost')[0]) != "undefined"){
				var cost = parseFloat($('#cost')[0].innerHTML);
			}
			$('.alumni-add-guest').click(function(event){
				event.preventDefault();
				count += 1;
				var newguest = $('#guest_span_0')[0].innerHTML;
				newguest = newguest.split("COUNT").join(count);			
				$(newguest).insertAfter($('#guest_span_'+(count-1)));
				$('#alumni-signup-remove-guest')[0].style.display = '';
				$('#cost')[0].innerHTML = (cost * (count + 1)).toFixed(2);
			});
			
			$('.alumni-remove-guest').click(function(event){
				event.preventDefault();
				$('#guest_span_'+count).remove()
				count -= 1;
				$('#cost')[0].innerHTML = (cost * (count + 1)).toFixed(2);
				if(count == 0){
					$('#alumni-signup-remove-guest')[0].style.display = 'none';
				}
			});
			
			$('.custom-price').click(function(event){
				$('#cost-not-free').prop('checked', 'true');
				$('#create-signup-cost').focus();
			});
			
			$('.cost-free').click(function(event){
				$('#cost-free').prop('checked', 'true');
				$('#create-signup-cost').val('');
			});
			
			$('#create-signup-cost').change(function(event){
				if($(this).val().substr(0,1) != "\u00A3"){
					var value = parseFloat($(this).val());				
				}else{
					var value = parseFloat($(this).val().substr(1))
				}
				if(isNaN(value)){
					$(this).val('');
				}else{
					$(this).val('\u00A3'+value.toFixed(2));
				}
			});
			
			$('.email-specify').click(function(event){
				var clickedOn = $(this).children('#email-to');
				clickedOn.prop('checked', !clickedOn.prop('checked'));
				
			});
			
			var options = 0;
			$('#add-dropdown').click(function(event) {
				event.preventDefault();
				options++;
				var newOption = $('#option-0')[0].innerHTML;
				$(newOption.split('COUNT').join(options)).insertAfter($('#option-'+(options-1))[0]);
				var newOption = $('#option-'+options+'-0')[0].innerHTML;
				$(newOption.split('SUB').join('1')).insertAfter($('#option-'+options+'-0')[0]);
				$(newOption.split('SUB').join('2')).insertAfter($('#option-'+options+'-1')[0]);
				$('#add-suboption-'+options).click(function(e){
					e.preventDefault();
					
					var optionNum = $(this).parent().siblings('#option-number')[0].innerHTML;
					var newOption = $('#option-'+optionNum+'-0')[0].innerHTML;
					var numOpt = parseInt($('#add-suboption-'+optionNum).parent().siblings('#suboptions')[0].innerHTML);		
					
					$(newOption.split('SUB').join(numOpt+1)).insertAfter($('#option-'+optionNum+'-'+numOpt)[0]);		
					numOpt++;
					$('#add-suboption-'+optionNum).parent().siblings('#suboptions')[0].innerHTML = numOpt;
					$(this).parent().siblings('.remove')[0].style.display = '';
					$('#option-value-'+optionNum+'-'+numOpt).focus();
				});
				$('#remove-suboption-'+options).click(function(e){
					e.preventDefault();
					var optionNum = $(this).parent().siblings('#option-number')[0].innerHTML;
					var numOpt = $('#add-suboption-'+optionNum).parent().siblings('#suboptions')[0].innerHTML;
					if(parseInt(numOpt) > 2){
						$('#option-'+optionNum+'-'+numOpt).remove();		
						numOpt--;
						$('#add-suboption-'+optionNum).parent().siblings('#suboptions')[0].innerHTML = numOpt;
					}
					if(parseInt(numOpt) == 2){
						$(this).parent()[0].style.display = 'none';
					}
					$('#option-value-'+optionNum+'-'+numOpt).focus();
				});
				$('#optionname-'+options).focus();
				$('.remove-dropdown')[0].style.display = '';
			});
			$('#remove-dropdown').click(function(event) {
				event.preventDefault();
				if(options > 0){	
					$('#option-'+options).remove();
					options--;
				}
				$('#optionname-'+options).focus()
				if(options == 0){
					$('.remove-dropdown')[0].style.display = 'none';
				}
			});
			$('#submit').click(function(event) {
				event.preventDefault();
				optiontext = '';
				failed = false;
				for(var i=1;i<=options;i++){
					optiontext += $('#optionname-'+i).val() + ',';
					if($('#optionname-'+i).val() == ''){
						failed = true;
						$('#optionname-'+i+'-helper')[0].innerHTML = '*this field is required';
					}else{
						$('#optionname-'+i+'-helper')[0].innerHTML = '';
					}
					numSubOptions = parseInt($('#option-'+i).children('#suboptions')[0].innerHTML);
					for(var j=1;j<=numSubOptions;j++){
						optiontext += $('#option-value-'+i+'-'+j).val() + ',';
						if($('#option-value-'+i+'-'+j).val() == ''){
							failed = true;
							$('#option-'+i+'-sub-'+j+'-helper')[0].innerHTML = '*this field is required';
						}else{
							$('#option-'+i+'-sub-'+j+'-helper')[0].innerHTML = '';
						}
					}
					optiontext = optiontext.slice(0,-1) + ';';
				}
				optiontext = optiontext.slice(0,-1);
				if(!failed){
					console.log('Options: '+optiontext);
				}
			});
			
			$('.claim-submit-form').submit(function(event){
											
				event.preventDefault();
				var inputs = ['name', 'alumni-email', 'subject-at-uni'];
                var helpers = ['name-helper', 'alumni-email-helper', 'alumni-subject-helper'];
                failed = false;
                
                for (i = 0; i < inputs.length; i++) {
                	if($('#'+inputs[i]).val() == ""){
                		failed = true;
                		$('#'+helpers[i])[0].innerHTML = '*This Field Is Required';                		
                	}else{
                		$('#'+helpers[i])[0].innerHTML = '';
                	}
                }
                                
				var email = $('#alumni-email').val();
			    var atpos = email.indexOf("@");
			    var dotpos = email.lastIndexOf(".");
			    if (atpos< 1 || dotpos<atpos+2 || dotpos+2>=email.length) {
			        failed = true;
			        $('#alumni-email-helper')[0].innerHTML = '*Please enter a valid email address.';
			    }

                numoptions = parseFloat($('#options')[0].innerHTML);
                for(i=0; i <= count; i++){
                	for(j=1; j<= numoptions; j++){
                		if($('#guest-'+i+'-option-'+j).val() == 0){
                			$('#guest-'+i+'-helper-'+j)[0].innerHTML = '*This Field Is Required';
                			failed = true;
                		}else{
                			$('#guest-'+i+'-helper-'+j)[0].innerHTML = '';
                		}
                	}
                	if(i >= 1){
                		if($('#guest-name'+i).val() == ''){
                			
                		}
                	}
                }                                
                
                if(!failed){
                	
                	options = '';
                	guests = '';
                	for(i=0; i <= count; i++){
                		for(j=1; j<= numoptions; j++){
                			options += $('#guest-'+i+'-option-'+j)[0].options[$('#guest-'+i+'-option-'+j).val()].innerHTML + ';';                			                			
                		}
                		if(i >= 1){
            				guests += $('#guest-name'+i).val() + ';';
            			}
                	}
                
	                $.ajax({
						type: "POST",
						url: script_url + 'alumni/submit_sign_up',
						data: {
							name: $('#name').val(),
							name_at_uni: $('#name-at-uni').val(),
							address: $('#alumni-address').val(),
							email: email,
							phone_number: $('#alumni-phone').val(),
							graduation_year: $('#year-of-grad')[0].options[$('#year-of-grad').val()].innerHTML,
							subject: $('#subject-at-uni').val(),
							guest_names: guests,
							options: options,
							requirements: $('#details').val(),
							numoptions: numoptions,
							event_id: $('#event_id')[0].innerHTML,
							num_tickets: (count + 1)
						},
						error: function(data) {
							$.common.notify('Your sign up was unsuccessful');
						},
						success: function(data) {
							JS = JSON.parse(data.html);
							if(JS.error){
								$.common.notify('Your sign up was unsuccessful');
							}else{
								window.location.href = $('#back_link')[0].href + '/view_signup/' + JS.id + '/' + JS.key;
							}
						}
					});
					//$.common.notify('Your sign up was successful');
					//
	            }
				
			});
			
			$('.email-type').click(function(event){
				$(this).children('#email-type').prop('checked', 'true');
			});
			
		}};
})(jQuery);