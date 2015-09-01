(function($) {
	$.photos = {
		init : function() {
					
			$('#file-upload').submit(function(event){
				event.preventDefault();
				num = $('#file-select').prop('files').length;
				$('#notifier').show();
				$('#file-upload').hide();
				$('#notifier-text').html('Loading your images...');
				for(i = 0; i < num; i++){
					outer = $('<div/>');
					$('<p/>').append($('<span/>').html('0')).append('<span/>').appendTo(outer);
					container = $('<div/>').prependTo(outer);
					$('<img/>').addClass('blank').appendTo(container);
					$('#image-previews').append(outer);
				}
				if(num == 0){
					//do something?
				}else{
					window.files = $('#file-select').prop('files');
					//console.log(files[0]);
					window.reader = new FileReader();
					window.reader.onload = $.photos.loaded;
					window.n = 0;
					reader.readAsDataURL(files[0]);
				}
			});
			
			$.photos.load_basket();
			$.photos.add_to_basket();
			$('.update-cart').click(function(event){
				event.preventDefault();
				$.photos.update_totals();
			});
			
			$('.status-update').change(function(e){
				v = $(this).val();
				option = $(this);
				o_id = $(this).siblings('input[name=oid]').val();
				console.log(e);
				$.ajax({
					type: "POST",
					url: script_url + 'photos/change_status',
					data: {
						order_id:o_id,
						status:v
					},
					error: function() {
						$.common.notify('Status could not be changed');
					},
					success: function() {
						option.parent().prev('h3').children('.stat').html(option.children('option[value='+v+']').html());
					}
				});
			});

			
			$('.photo-link').click(function(event){
				event.preventDefault();
				photo = $(this);
				container = $('<div/>').append('<div class="photo-preview"><div class="prev"/><div class="next"/><div class="tag"/><div class="close"/><div class="download"/><div class="open"/></div><div class="side-bar"/>').addClass('photo-preview-container').appendTo('body');
				$('.side-bar').append('<div class="uploader"><div class="user-icon"/><div class="name-date"><h3 class="name"/><p class="date"/></div></div><div class="tagged-info"/>');
				cover = $('<div/>').addClass('photo-cover').appendTo('body');
				$('.photo-preview').children('div').addClass('hover-icon');
				$('.photo-cover, .close').click(function(){
					$.photos.closeScreen();
				});
				$('.next').click(function(){
					$.photos.next_photo();
				});
				$('.prev').click(function(){
					$.photos.prev_photo();
				});
				$('.tag').click(function(){
					if($(this).hasClass('untag')){
						$.photos.end_tag();
					}else{
						$.photos.start_tag();
					}
				});
				$('.download').click(function(){
					url = $('.photo-preview').css('background-image');
					url = url.substr(4,url.length-5);
					var a = $("<a>").attr("href", url).attr("download", $('#album-name').html()+".jpg").appendTo("body");
					a[0].click();
					a.remove();
				});
				$('.open').click(function(){
					url = $('.thumb').eq(n).parent().attr('href');
					window.location.href = url;
				});
								
				$('.photo-preview').attr('value', photo.parent().index());
				$.photos.reload_photo();
				container.css('top', $(window).height()/2-container.height()/2+$(window).scrollTop());
				cover.css('height', $(document).height());
				container.css('left', $(window).width()/2-container.width()/2);
				$('.photo-preview').on({
					mouseenter:function(){
						container.addClass('hovering');
					},
					mouseleave:function(){
						container.removeClass('hovering');
					},
					mousemove:function(e){
						o = $('.photo-preview').offset();
						d_x = $.photos.screen_to_db_x(e.pageX- o.left);
						d_y = $.photos.screen_to_db_y(e.pageY- o.top);
						minD = 1000;
						$('.photo-preview-container .list-of-tagged').children('p').each(function(){
							xx = parseFloat($(this).attr('x'));
							yy = parseFloat($(this).attr('y'));
							d = Math.pow(xx - d_x, 2) + Math.pow(yy - d_y, 2);
							if(d < minD){
								minD = d;
								ind = $(this).index();
							}
							//console.log('(' + x + ', ' + y + ') - (' + xx + ', ' + yy + ') - ' + d);
						});
						if(minD < 100){
							p = $('.photo-preview-container .list-of-tagged').children('p').eq(ind);
							$.photos.show_tag(p);
						}else{
							$.photos.hide_tag();
						}
					}
				});
			});
			
			$(document).unbind('keydown');
			$(document).keydown(function(e) {
				if($('.photo-cover').length > 0){
					if(e.keyCode == 27) {
						e.preventDefault();
						$.photos.closeScreen();
					}else if(e.keyCode == 37){
						e.preventDefault();
						$.photos.prev_photo();
					}else if(e.keyCode == 39){
						e.preventDefault();
						$.photos.next_photo();
					}
				}
			});
			
			$('.rotate-photo, .edit-rotate').click(function(event){
				event.preventDefault();
				button = $(this);
				is_img = button.hasClass('rotate-photo');
				
				if(is_img){
					img = $('#image-container').children('img');
					p_id = $('#photo-id').html();
					link = img.attr('src');
					img.attr('src', $('#spinner-link').html());
				}else{
					img = button.parent('div').siblings('a').children('div');
					p_id = button.siblings('.photo-id').html();
					link = img.css('background-image');
					img.css('background-image', 'url('+$('#spinner-link').html()+')');
					if(typeof(img.attr('value')) == "undefined"){
						img.attr('value', link.substr(4).slice(0,-1))
					}
				}
				
				
				$.ajax({
					type: "POST",
					url: script_url + 'photos/rotate_photo',
					data: {
						photo_id:p_id,
						angle:button.hasClass('alt')?'90':'-90'
					},
					error: function() {
						if(is_img){
							img.attr('src', link); 
						}else{
							img.css('background-image', link);
						}	
						$.common.notify('This photo could not be rotated');
					},
					success: function() {
						d = new Date();
						link = img.attr('value')+'?t='+d.getTime();
						if(is_img){
							img.attr('src', link); 
						}else{
							img.css('background-image', 'url('+link+')');
						}
					}
				});
			});
			
			$('.delete-photo, .edit-delete').click(function(event){
				event.preventDefault();
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete this photo?<br />');
				
				button = $(this);
				is_img = button.hasClass('delete-photo');
				
				if(is_img){
					p_id = $('#photo-id').html();
				}else{
					p_id = button.siblings('.photo-id').html();
				}
				
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Delete",
					buttons: {
						"Delete": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'photos/delete_photo',
								data: {
									photo_id:p_id
								},
								error: function() {
									$.common.notify('This photo could not be removed');
								},
								success: function() {
									if(is_img){
										$('#back_link').click();
									}else{
										button.closest('.photo-container').remove();
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
			
			$('.delete-album').click(function(event){
				event.preventDefault();
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete this album, and all of the photos in it?<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Delete",
					buttons: {
						"Delete": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'photos/delete_album',
								data: {
									a_id:$('#album-id').html()
								},
								error: function() {
									$.common.notify('This album could not be removed');
								},
								success: function() {
									$('#back_link').click();
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
			
			$('input[name=watermark]').change(function(){
				if($(this).is(':checked')){
					$('.watermark-options').show();
					$('#custom-text').prop('disabled', $('input[name=watermark-options]:checked').val() !== 'custom-watermark-option');
				}else{
					$('.watermark-options').hide();
				}
			});
			
			$('input[name=watermark-options]').change(function(){
				$('#custom-text').prop('disabled', $(this).val() !== 'custom-watermark-option');
			});

			
		},
		
		loaded:function(e){
			window.n++;
			$('.blank').eq(0).removeClass('blank').addClass('loaded').attr('src', e.target.result);
			if(window.n < num){
				window.reader.readAsDataURL(window.files[window.n]);
			}else{
				$('#notifier-text').html('Uploading your images...');
				$.photos.upload();
			}
		},
		upload:function(im){
			num_active = $('.uploading').length;
			if(num_active > 2){
				return;
			}
			if($('.loaded').length == 0){
				if($('.uploading').length == 0){
					$('#notifier-text').html('Done!');
					$('#spinning').hide();
					$('#album-link').click();
				}
				return;
			}
			im = $('.loaded').eq(0);
			im.removeClass('loaded').addClass('uploading');
			im.parent().siblings('p').addClass('animating');
			src = im.attr('src').substring(23);
			console.log(src.length);
			//console.log(LZString.compressToEncodedURIComponent(src).length);
			parts = src.match(/.{1,1000000}/g);
			unique = $.photos.make_unique();
			im.attr('unique', unique);
			$.photos.upload_part(im,parts,0,unique);
			setTimeout(function(){
				$.photos.upload();
			},500);
		},
		upload_part:function(im,parts,n,unique){
			if(n < parts.length){
				$.ajax({
					type: "POST",
					url: script_url + 'photos/upload_part',
					data: {
						uid: unique,
						n:n,
						string:parts[n]
					},
					error: function() {
						im.parent().siblings('p').removeClass('animating').addClass('failed');
						im.parent().siblings('p').children('span').eq(0).html('');
					},
					success: function() {
						im.parent().siblings('p').children('span').eq(0).html(Math.round(100 * (n+1)/parts.length));
						im.parent().siblings('p').children('span').eq(1).width(200 * (n+1)/parts.length);
						$.photos.upload_part(im,parts,n+1,unique);
					}
				});
			}else{
				$.ajax({
					type: "POST",
					url: script_url + 'photos/photo_complete',
					data: {
						uid: unique,
						n:n,
						aid:$('#album-id').html(),
						watermark:$('input[name=watermark]').is(':checked')?1:0,
						watermarktype:$('input[name=watermark-options]:checked').parent().index(),
						watermarktext:$('#custom-text').val()
					},
					error: function() {
						im.parent().siblings('p').removeClass('animating').addClass('failed');
						im.parent().siblings('p').children('span').eq(0).html('');
						setTimeout(function(){
							$.photos.upload();
						},500);
					},
					success: function() {
						setTimeout(function(){
							$.photos.upload();
						},500);
						im.removeClass('uploading').addClass('done');
						im.parent().siblings('p').removeClass('animating').addClass('done');
					}
				});
			}
		},			
		make_unique:function(){
			var text = "";
		    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		    for( var i=0; i < 50; i++ )
		        text += possible.charAt(Math.floor(Math.random() * possible.length));
		    return text;
		},
		closeScreen:function(){
			$('.photo-cover, .photo-preview-container').remove()		
		},
		next_photo:function(){
			console.log('next');
			n = parseInt($('.photo-preview').attr('value'))+1;
			if(n == $('.photo-container').length){
				n = 0;
			}
			$('.photo-preview').attr('value', n);
			$.photos.reload_photo();	
		},
		prev_photo:function(){
			n = parseInt($('.photo-preview').attr('value'));
			if(n == 0){
				n = $('.photo-container').length;
			}
			$('.photo-preview').attr('value', n-1);
			$.photos.reload_photo();
		},
		reload_photo:function(){
			n = $('.photo-preview').attr('value');
			$('.photo-preview').css('background-image', 'url('+$('.thumb').eq(n).attr('image')+')');
			$('.photo-preview-container .side-bar .uploader .user-icon').css('background-image', 'url('+$('.thumb').eq(n).children('.uploader-icon').html()+')');
			$('.photo-preview-container .side-bar .uploader .name-date .name').html($('.thumb').eq(n).children('.uploader-name').html());
			$('.photo-preview-container .side-bar .uploader .name-date .date').html($('.thumb').eq(n).children('.date').html());
			$('.photo-preview-container .side-bar .tagged-info').html($('.thumb').eq(n).children('.uploader-tagged').html());
			$.photos.hover_remove();
			$.photos.end_tag();
			$.photos.hide_tag();
			$.photos.add_to_basket();
			$.photos.load_basket();
		},
		basket_animate:function(n){
			if($('.basket-num').hasClass('basket-num-flash')){
				$('.basket-num').removeClass('basket-num-flash')
			}else{
				$('.basket-num').addClass('basket-num-flash')
			}
			if(n >= 0){
				setTimeout(function(){
					$.photos.basket_animate(n-1);
				},200);
			}
		},
		add_to_basket:function(){
			$('.basket-add').click(function(){
				n = $('.photo-preview').attr('value');
				if($('.thumb').eq(n).children('.p-id').length > 0){
					p_id = $('.thumb').eq(n).children('.p-id').html();
				}else{
					p_id = $('#photo-id').html();
				}
				
				var cont = $('<div />').addClass('type-selection-box');
				cont.html($('#type-selection').html());
												
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Type Selection",
					buttons: {
						Ok: function () {
							$.ajax({
								type: "POST",
								url: script_url + 'photos/add_basket',
								data: {
									photo_id:p_id,
									type:cont.find('.type-select').val()
								},
								error: function() {
									cont.dialog("close");
									$.common.notify('This photo could not be added to your basket');
								},
								success: function() {
									cont.dialog("close");
									$.photos.closeScreen();
									$.photos.load_basket(true);
								}
							});
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
			});
		},
		load_basket:function(animate){
			$.ajax({
				type: "POST",
				url: script_url + 'photos/basket',
				data: {
					update:1
				},
				error: function() {
					$.common.notify('Something went wrong...');
				},
				success: function(e) {
					$('.basket-preview').html(JSON.parse(e).html);
					$('.basket-preview h2').click(function(){
						if($(this).parent().hasClass('basket-open')){
							$('.basket-contents').animate({height:'0px'}, function(){
								$(this).parent().removeClass('basket-open');
							});
						}else{
							$(this).parent().addClass('basket-open');
							h = $('.basket-contents').css('height', 'auto').height();
							$('.basket-contents').css('height', '0px')
							$('.basket-contents').animate({height:h+'px'});
						}
					});
					$('.update-cart').click(function(event){
						event.preventDefault();
						$.photos.update_totals();
					});
					if(animate){
						$.photos.basket_animate(4);
					}
				}
			});
		},
		update_totals:function(){
			ids = [];
			vals = [];
			$('.basket-qty').each(function(){
				ids.push($(this).siblings('.row-id').html());
				vals.push($(this).val());
			});
			$.ajax({
				type: "POST",
				url: script_url + 'photos/update_basket',
				data: {
					ids:ids,
					vals:vals
				},
				error: function() {
					$.common.notify("Sorry, we couldn't update the basket at the moment.");
				},
				success: function() {
					if($('.large-photo').length > 0){
						location.reload();
					}else{
						$.photos.load_basket();
					}
				}
			});
		},
		start_tag:function(){
			$('.tag').addClass('untag');
			$('.photo-preview-container').addClass('tagging');
			$('<a/>').html('<p>Stop Tagging</p>').attr('href', '#').addClass('stop-tagging').prependTo('<p/>').click(function(){ $.photos.end_tag(); }).appendTo('.tagged-info');
			setTimeout(function(){
				$('.photo-preview').click(function(click,n){
					$.photos.tag_image(click);
				});
			},200);
		},
		end_tag:function(){
			$('.tag').removeClass('untag');
			$('.stop-tagging, .tag-box').remove();
			$('.photo-preview-container').removeClass('tagging');
			$('.photo-preview').unbind('click');
			$('.name-input').parent().remove();
		},
		tag_image:function(click){
			$('.tag-box').remove();
			$('.name-input').parent().remove();
			/*if(!$(click.srcElement).hasClass('photo-preview')){
				click.offsetX += $(this).attr('x') - 25;
				click.offsetY += $(this).attr('y') - 25;
			}*/
			$(this).attr('x', click.offsetX);
			$(this).attr('y', click.offsetY);
			o = $('.photo-preview').offset();
			x = $.photos.screen_to_db_x(click.pageX - o.left);
			y = $.photos.screen_to_db_y(click.pageY - o.top);
			cX = $.photos.db_to_screen_x(x);
			cY = $.photos.db_to_screen_y(y);
			box = $('<div/>').addClass('tag-box').appendTo('.photo-preview');
			box.css('top', cY - box.height()/2);
			box.css('left', cX - box.width()/2);
			paragrah = $('<p/>').appendTo('.tagged-info');
			$('<input>').addClass('name-input').attr('placeholder', 'Type a Name...').appendTo(paragrah).focus();
			var availableTags = [];
			$('#list-of-users').children('p').each(function(){
				availableTags.push({value:$(this).text(),id:$(this).attr('value')});
			});
        	$('.name-input').autocomplete({
		      	source: availableTags,
		      	select: function(event, user){
		      		if(user.item == null){
		    			//??
		    		}else{
		    			n = $('.photo-preview').attr('value');
		    			$.ajax({
							type: "POST",
							url: script_url + 'photos/add_tag',
							data: {
								x:x,
								y:y,
								u_id:user.item.id,
								p_id:$('.thumb').eq(n).children('.p-id').html()
							},
							error: function() {
								$.common.notify("Sorry, something went wrong, could you reload the page?");
							},
							success: function() {
								$('<p/>').attr('value', user.item.id).attr('x', x).attr('y', y).html(user.item.label).appendTo('.side-bar .list-of-tagged');
				    			$('<p/>').attr('value', user.item.id).attr('x', x).attr('y', y).html(user.item.label).appendTo($('.thumb').eq(n).find('.list-of-tagged'));
				    			$.photos.hover_remove();
				    			$('.name-input').parent().remove();
				    			$('.tag-box').remove();
							}
						});
		    		}
		      	}
		    });
		},
		hover_remove:function(){
			$('.list-of-tagged').children('p').mouseover(function(){
				$.photos.show_tag($(this));
				//console.log($.photos.db_to_screen_x(parseInt($(this).attr('x'))) + ', ' + $('.tag-name').css('top'));
			}).mouseout(function(){
				$.photos.hide_tag();
			});
		},
		show_tag:function(p){
			if($('.tag-name').attr('value') == p.attr('value')){
				return;
			}
			if($('.tag-name').length == 0){
				$('<div/>').addClass('tag-name').appendTo('.photo-preview');
				$('<div/>').addClass('tag-name-arrow').prependTo('.tag-name');
				$('<div/>').addClass('tag-name-text').appendTo('.tag-name');
			}
			$('.tag-name-text').html(p.html());
			$('.tag-name').show();
			$('.tag-name').css('left', $.photos.db_to_screen_x(parseFloat(p.attr('x'))) - $('.tag-name').width()/2);
			$('.tag-name').css('top', $.photos.db_to_screen_y(parseFloat(p.attr('y'))));
			$('.tag-name').attr('value', p.attr('value'));
		},
		hide_tag:function(){
			$('.tag-name').hide();
			$('.tag-name').attr('value', '');
		},
		db_to_screen_x:function(x){
			n = $('.photo-preview').attr('value');
			w = $('.thumb').eq(n).children('.width').html();
			h = $('.thumb').eq(n).children('.height').html();
			r = w/h;
			if(r > 1){
				return x*5;
			}else{
				return 5*r*x + 250 - 250*r;
			}
		},
		db_to_screen_y:function(y){
			n = $('.photo-preview').attr('value');
			w = $('.thumb').eq(n).children('.width').html();
			h = $('.thumb').eq(n).children('.height').html();
			r = w/h;
			if(r > 1){
				return 5*y/r + 250 - 250/r;
			}else{
				return y*5;
			}
		},
		screen_to_db_x:function(cX){
			n = $('.photo-preview').attr('value');
			w = $('.thumb').eq(n).children('.width').html();
			h = $('.thumb').eq(n).children('.height').html();
			r = w/h;
			if(r > 1){
				return Math.min(Math.max(cX/5,0),100);
			}else{
				return Math.min(Math.max(cX/(5*r) - 50/r + 50,0),100);
			}
		},
		screen_to_db_y:function(cY){
			n = $('.photo-preview').attr('value');
			w = $('.thumb').eq(n).children('.width').html();
			h = $('.thumb').eq(n).children('.height').html();
			r = w/h;
			if(r > 1){
				return Math.min(Math.max(cY*r/5 - 50*r + 50,0),100);
			}else{
				return Math.min(Math.max(cY/5,0),100);
			}
		}
	};
})(jQuery);