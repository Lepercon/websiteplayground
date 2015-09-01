(function ($) {
	$.whoswho = {
		cache: [],
		mem_num: null,
		sorting: false,

		init: function () {
			$.whoswho.cache = []; // clear cache on tab load
			$('.whoswho-jsify').click(function () {
				if (!$.whoswho.sorting) {
					$.whoswho.load_profile.call(this);
				}
				return false;
			});
			var new_spinner = $('#spinner').clone().attr('id', 'whoswho-spinner').hide();
			$('#whoswho-details').append(new_spinner);
			$.whoswho.sorting_init();
			
			$('.list-of-roles a').click(function(event){
				console.log('a');
				event.preventDefault();
				$('.role-description').html('<div class="spacer"></div>');
				$('<h2/>').html($(this).children().html()).appendTo('.role-description');
				$(this).next().clone().appendTo('.role-description');
				$('.role-description h3').each(function(){
					$('<img/>').attr('src', $(this).attr('value')).insertAfter(this);
				});
				$('html, body').stop().animate({
			        'scrollTop': $('.content-right').offset().top
			    });
			});
			
			$('.top-link').click(function(){
				event.preventDefault();
				$('html, body').stop().animate({
			        'scrollTop': 0
			    });

			});
			
			$('.report-link').click(function(event){
				event.preventDefault();
				$('<div></div>').dialog({
					modal: true,
					resizable: false,
					open: function() {
						$(this).html($('#report-form').html());
					},
					buttons: {
						Submit: function() {
							text = $(this).children('.text-area').val();
							$.ajax({
								type: 'POST',
								url: script_url + 'whoswho/feedback',
								data:{
									text:text,
									page:$('.role-description').children('h2').first().html()
								}
							});
							$(this).html('Thank you for your help...');
							d = $(this);
							setTimeout(function(){
								d.parent().animate({'opacity':0},{
									complete:function(){ d.dialog('close'); console.log('a'); }
								});
							},500);
						}
					},
					width:'500px',
					title:'Anonymous Feedback'
				});
			});
			
		},

		sorting_init: function () {
			var whoswho = $('#whoswho');
			var whoswho_order = $('#whoswho-order');
			var whoswho_order_icon = '<span class="ui-icon ui-icon-gear inline-block"></span>';
			var whoswho_cursors = whoswho.find('li, li > a');
			// setup
			whoswho.sortable({
				disabled: true,
				placeholder: 'whoswho-placeholder inline-block'
			}).disableSelection();
			// click
			whoswho_order.click(function () {
				if ($.whoswho.sorting) {
					whoswho_cursors.css('cursor', 'auto');
					whoswho.sortable('disable');
					whoswho_order.html(whoswho_order_icon + 'Saving...');
					$.whoswho.save_order(function () {
						whoswho_order.html(whoswho_order_icon + 'Saved.');
						setTimeout(function () {
							whoswho_order.html(whoswho_order_icon + 'Change Order');
						}, 2000);
					});
				}
				else {
					whoswho_cursors.css('cursor', 'move');
					whoswho.sortable('enable')
					whoswho_order.html(whoswho_order_icon + 'Save order');
				}
				$.whoswho.sorting = !$.whoswho.sorting;
				return false;
			});
		},

		save_order: function (callback) {
			var whoswho = $('#whoswho');
			var order = [];
			whoswho.find('li').each(function (i, e) {
				order.push({ 'name': 'order[' + i + ']', 'value': $(e).find('div.whoswho-id').text() });
			});
			var page = $('#ww-nav').attr('value');
			$.post(script_url + 'whoswho/save_order/' + page, order, callback);
		},

		load_profile: function () {
			// get member num from href
			$.whoswho.mem_num = parseInt(this.href.substr(this.href.lastIndexOf('/') + 1, this.href.length));
			// is there a cache for this member?
			if ($.whoswho.cache[$.whoswho.mem_num]) {
				$('#whoswho-mem-content').html($.whoswho.cache[$.whoswho.mem_num]).jsify();
				$.whoswho.scroll();
				return;
			}
			$('#whoswho-spinner').show();
			$('#whoswho-mem-content').hide();
			$.ajax({
				type:'POST',
				url: this.href,
				data: {profile:1},
				success: $.whoswho.load,
				dataType: 'json'
			});
		},

		load: function (data) {
			if (data.redirect) {
				window.location = data.redirect;
				return;
			}

			$.whoswho.cache[$.whoswho.mem_num] = data.html;

			$('#whoswho-mem-content').html(data.html).jsify();

			$.common.preload_images($('#whoswho-mem-content img'), $.whoswho.finish_loading);
		},

		scroll: function () {
			$('html,body').animate({ scrollTop: $('#whoswho-icons').offset().top + $('#whoswho-icons').height() }, { duration: 'slow' });
		},

		finish_loading: function () {
			$.whoswho.scroll();
			$('#whoswho-spinner').hide();
			$('#whoswho-mem-content').show();
		}
	};
})(jQuery);