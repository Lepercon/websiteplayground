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
                        
                        $('#whoswho').children().each(function(){
                            $.whoswho.getDataUri($(this));
                        });
                        
                        $('.users-select').change(function(){
                            if($(this).is(':checked')){
                                $(this).parent().parent().addClass('selected-user');
                            }else{
                                $(this).parent().parent().removeClass('selected-user');
                            }
                        });
                        
                        $('.select-all').click(function(event){
                            event.preventDefault();
                            $('.users-select').prop('checked', true);
                            $('.user-icon').addClass('selected-user');
                        });
                        
                        $('.select-none').click(function(event){
                            event.preventDefault();
                            $('.users-select').prop('checked', false);
                            $('.selected-user').removeClass('selected-user');
                        });
                        
                        $('#whoswho-print').click(function(event){
                            event.preventDefault();
                            var imgData = '';
                            var doc = new jsPDF();
                            
                            var specialElementHandlers = {
                                '#editor': function(element, renderer){
                                    return true;
                                }
                            };
                            
                            i = 1;
                            $('#whoswho .selected-user').parent().each(function(){
                                var name = $(this).find('p').eq(0).children().html();
                                var role = $(this).find('p').eq(1).html();
                                var email = $(this).find('p').eq(2).html();
                                imgData = $(this).children('span').attr('data-uri');

                                doc.setFontType("bold");
                                doc.setFontSize(10);
                                x = 40 + 65 * ((i+1) % 3);
                                y = 70 + Math.ceil(i/3) * 80 - 70;
                                doc.centerText(x, y, name);
                                
                                doc.setFontType("normal");
                                doc.setFontSize(8);
                                doc.centerText(x, y+4, role);
                                doc.addImage(imgData, 'JPEG', x-25, y - 55, 50, 50);
                                doc.setDrawColor(200, 0, 0);
                                doc.rect(x - 30, y - 60, 60, 70);
                                
                                doc.setFontType("italic");
                                doc.centerText(x, y+8, email);
                                doc.addImage(imgData, 'JPEG', x-25, y - 55, 50, 50);
                                doc.setDrawColor(200, 0, 0);
                                doc.rect(x - 30, y - 60, 60, 70);
                                i++;
                                if(i % 10 === 0){
                                    doc.addPage('a4','l');
                                    i = 1;
                                }
                            });
                            
                            d = new Date();
                            var name = 'Whoswho - ' + $('.page').html() + ' - ' +d.toDateString();
                            doc.save(name+'.pdf');
                            //doc.addImage(imgData, 'JPEG', 15, 40, 180, 160);
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
		},
                
                getDataUri: function(obj) {
                    var url = obj.find('div').css('background-image').substr(4);
                    url = url.substring(0, url.length-1);
                    var image = new Image();

                    image.onload = function () {
                        var canvas = document.createElement('canvas');
                        canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
                        canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

                        canvas.getContext('2d').drawImage(this, 0, 0);

                        // ... or get as Data URI
                        obj.children('span').attr('data-uri', canvas.toDataURL('image/jpeg'));
                    };

                    image.src = url;
                }

	};
})(jQuery);