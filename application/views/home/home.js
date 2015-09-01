(function($) {
	$.home = {
		init : function() {
			$('#home-screen').html('<p><a id="home-screen-link" href="#">Add the JCR website to your phone home screen</a></p>');
			$('#home-screen-link').on('click touch', function(e) {
				e.preventDefault();
				$('<div><h2>iOS</h2><div><ul><li>Tap the share icon</li><li>Tap Add to Home Screen</li></ul></div><h2>Android</h2><div><ul><li>In the menu, add to bookmarks</li><li>In bookmarks, hold the new icon and tap Add to home screen</li></ul></div><h2>Windows Phone</h2><div><ul><li>Tap More...</li><li>Tap Pin to Start</li></ul></div></div>').accordion({
					heightStyle: "content"
				}).dialog({
					resizable: false,
					modal: true,
					buttons: {
						"Close": function () {
							$(this).dialog("close");
						}
					},
					close: function(event, ui) {
						$(this).dialog('destroy').remove();
					}
				});
			});
			
			if($('.slideshow-wrapper').length > 0){
				$('.slideshow:first').addClass('front-image');
				$.home.slideshow();				
			}
			
			h = parseInt($('#image-height').text());
			if($('.banner-page').length > 0){
				
				$('#nav, #login-details, #footer').remove();
				
				$('#big-title').html('');
				$('#header-title').css({'background-image':'none'});
				$('#header-title').addClass('header-text');
				$('.poster-banner').addClass('poster-banner-tall');
                $('#content-area').css('width', '90%');
                $('#header-width').css('width', '1500px');
								
				$('.poster-outer-box').css({'height':(h+2)});
				$('.poster-box').css({'height':(h+2)});
				$('.event-scroll-image').css({'height':(h)});
				
				$('body').addClass('hide-overflow');
				$('#green-week-close, .green-image, .banner-page').remove();
				
				setTimeout(function() {
					window.location.reload();
				}, 0.5 * 60 * 60 * 1000);//
			}
			
			if($('.clock').length > 0){
				new_clock = $('.clock').clone().appendTo($('#wrapper'));
				new_clock.css('display', 'block');
				left = $(window).width() - new_clock.outerWidth(true) - 15;
				new_clock.css('left', left);
				new_clock.css('top', 15);
				$.home.clock()
				
				clearInterval(window.blink_interval);
				window.blink_interval = setInterval(function(){
					if($('.colon').css('color') == 'rgb(128, 128, 128)'){
						$('.colon').css('color', 'white')
					}else{
						$('.colon').css('color', 'gray')
					}
				}, 1000);
				//
			}
			
			clearInterval(window.poster_timeout_handle);//such a long name to ensure it isn't overwritten by another process.
			window.poster_timeout_handle = setInterval(function(){
				if($('.poster-box').children('.poster-image').length == 0){
					clearInterval(window.poster_timeout_handle);
				}else{
					$('.poster-box').append($('.poster-box').children('.poster-image')[0].outerHTML);
					//im_width = (h*parseFloat($('#image-width').html())).toFixed(0);
					$('.poster-box').animate({'left':(-$('.poster-image img').first().width() - 7)+'px'});
					setTimeout(function(){
						del = false;
						$('.poster-box').children('.poster-image').each(function(){
							if(!del){
								$(this).remove();
								del = true;
							}
						});
						$('.poster-box').css('left','0px');
					}, 1000);
				}
			}, 4000);
			if($('#redirect-link').length > 0){
				location.replace($('#redirect-link').html());
			}
			
			if($('.touch-device-test').length > 0){
				if(!!('ontouchstart' in window)){
					$('.touch-device-test').html('You are using a touchscreen device<br>Your window width: '+$(window).width()+'px');
				}else{
					$('.touch-device-test').html('You are not using a touchscreen device<br>Your window width: '+$(window).width()+'px');
				}
			}
			
			clearInterval(window.message_timeout_handle);
			$('.message-scroll').children('span').last().css({'display':'block'});
			window.message_timeout_handle = setInterval(function(){
				if($('.message-scroll').children('span').length == 0){
					clearInterval(window.message_timeout_handle);
				}else{
					$('#content').css({'overflow':'hidden'});
					$('.message-scroll').children('span').last().animate({
						'left':'-1100px'
					},{
						duration: 300,
						complete: function() {
							$('.message-scroll').children('span').css({'display':'none'});
							$('.message-scroll').append($('.message-scroll').children('span')[0].outerHTML);
							$('.message-scroll').children('span').first().remove();
							$('.message-scroll').children('span').last().css({
								'display':'block',
								'left':'1100px'
							});
							$('.message-scroll').children('span').last().animate({'left':'5px'},{duration: 300});
						}
					});					
				}
			}, 5000);
			
			if($('#redirect-link').length > 0){
				location.replace($('#redirect-link').html());
				hours = currentdate.getHours();
				if(hours.length == 1){
					$('.hour').text('0'+hours);
				}else{
					$('.hour').text(hours );
				}
				minutes = currentdate.getMinutes();
				if(minutes.length == 1){
					$('.minute').text('0'+minutes);
				}else{
					$('.minute').text(minutes);
				}
			}
			
			$('.new-status').focus(function(){
				$(this).css('min-height', 40);
				$('.submit-line').show();
			}).blur(function(){
				if($(this).val() == ''){
					window.closing_status_box = true;
					setTimeout(function(){
						if(window.closing_status_box){
							$('.new-status').css('min-height', 24);
							$('.new-status').css('height', 24);
							$('.submit-line').hide();
						}
					},150);
				}
			});
			
			$('.post-button').click(function(){
				new_status = $('.new-status').val();
				if(new_status == ''){
					return;
				}
				$.ajax({
					type: "POST",
					url: script_url + 'home/new_status',
					data: {
						content: new_status,
						title:''
					},
					error: function() {
						$.common.notify('This status could not be posted');
					},
					success: function(response) {
						J = JSON.parse(response);
						$('.new-status').val('');
						$('.new-status').css('min-height', 24);
						$('.submit-line').hide();
						$(J.html).insertAfter('.new-status-box');
					}
				});
				
			});
			
			$('.new-status').on('keyup', function(e){
				textarea = $('.new-status');
				textarea.css('height', 0);
				textarea.css('min-height', 0);
				textarea.css('height', textarea.get(0).scrollHeight);
				textarea.css('min-height', 40);
			});
			
			$('.smilies-selection img').click(function(){
				window.closing_status_box = false;
				cursorPos = $('.new-status').prop('selectionStart');
				v = $('.new-status').val(),
				textBefore = v.substring(0, cursorPos),
				textAfter  = v.substring(cursorPos, v.length);
				$('.new-status').val(textBefore + $(this).attr('value') + textAfter);
				$('.new-status').focus;
			});
			
		},
		
		slideshow:function(){
			var $active = $('.front-image');
			var $next = ($active.next().length > 0) ? $active.next() : $('.slideshow:first');
			$next.addClass('next-image');
			$active.fadeOut(1500,function(){
				$active.show().removeClass('front-image');
				$next.removeClass('next-image').addClass('front-image');
			});
			setTimeout(function(){
				$.home.slideshow();
			},5000);
		},
        
        clock:function(){
            if($('.clock').length > 0){
                $.post(script_url + 'home/get_time', [{ 'name': 'ajax','value': 'none'}], $.home.time, 'json').fail(function() {
                    var currentdate = new Date();
                    hours = currentdate.getHours();
                    if(hours.toString().length == 1){
                        $('.hour').text('0'+hours);
                    }else{
                        $('.hour').text(hours);
                    }
                    minutes = currentdate.getMinutes();
                    if(minutes.toString().length == 1){
                        $('.minute').text('0'+minutes);
                    }else{
                        $('.minute').text(minutes);
                    }
                    setTimeout(function(){
                        $.home.clock();
                    },5000);
                });
            }
        },
		
		time: function(data){
			new_time = data.html;
            if(new_time.indexOf(':') == 2){
                time = new_time.split(':');
                $('.hour').text(time[0]);
                $('.minute').text(time[1]);
                setTimeout(function(){
                    $.home.clock();
                },parseInt(time[2]));
            }else{
                var currentdate = new Date();
                hours = currentdate.getHours();
                if(hours.toString().length == 1){
                    $('.hour').text('0'+hours);
                }else{
                    $('.hour').text(hours);
                }
                minutes = currentdate.getMinutes();
                if(minutes.toString().length == 1){
                    $('.minute').text('0'+minutes);
                }else{
                    $('.minute').text(minutes);
                }
                setTimeout(function(){
                    $.home.clock();
                },5000);
            }
		}
	};
})(jQuery);