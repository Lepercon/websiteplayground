(function ($) {
	$.heading = {
		init: function() {
			var start_height = 30;
			var hover_main = false;
			var hover = false;
						
			$('.heading').on({
                mouseenter:function(event){
                    $('.heading').stop();		
					var maxHeight = 0;
                    hover_main = true;
					$('.heading').each(function(){
						maxHeight = Math.max($(this).height(), maxHeight);
					});
					$('.heading').removeClass('using');
					if(maxHeight != start_height){
						$('.heading').animate({
							height:start_height+"px"
						},{
							speed:50,
							easing:"swing",
							queue:false,
							specialEasing:"easeOutExpo"
						});
					}
					startheight = $(this).height();
					$(this).height('auto');
					endheight = $(this).height();
					$(this).height(startheight);
					$(this).addClass('using');              
					$(this).animate({
						height:endheight+"px"
					},{
						speed:400,
						easing:"swing",
						queue:true,
						specialEasing:"easeOutExpo"                        
					});
                    setTimeout(function(){
                        hover_main = false;
                    },50);
				},
                mouseleave:function(event){
					setTimeout(function(){
						if(!hover && !hover_main){
							$('.heading').removeClass('using');
							$('.heading').animate({
								height:start_height+"px"
							},{
								speed:200,
								easing:"swing",
								queue:false,
								specialEasing:"easeOutExpo"				
							});
						}
					},50);
				}
				
			});
			hover = false;           
			$('.heading tr').on({
				mouseenter:function(){					
					if($(this).find('.ui-icon').length == 1){
						$('#sub-menu').html('');
						$(this).find('table').clone().appendTo('#sub-menu');
						box = $(this)[0].getBoundingClientRect();
						$('#sub-menu').css({left:box.right, top:box.top+$(window).scrollTop()});
                        $('#sub-menu').removeClass('hidden-menu');
                        hover = true;
						$('#sub-menu').on({
                            mouseenter: function () {
                                hover = true;
                            },
                            mouseleave:function () {
                                hover = false;
                                $('#sub-menu').addClass('hidden-menu');
                                $('.sub-sub-menu').remove();
                                $('.heading').removeClass('using');
                                $('.heading').animate({
                                    height:start_height+"px"
                                },{
                                    speed:200,
                                    easing:"swing",
                                    queue:false,
                                    specialEasing:"easeOutExpo"
                                });
                            }
						},'table');
                        $('#sub-menu tr').unbind('click').on({
                            mouseenter: function(){
                                $(this).addClass('hovering');
                                $(this).children('td').addClass('hovering');
                            },
                            mouseleave: function(){
                                $(this).removeClass('hovering');
                                $(this).children('td').removeClass('hovering');
                            },
                            click: function(oEvent){
                            	oEvent.preventDefault();
                                master_class_list = $(this).attr('class');
                                $(this).attr('class', '');
                                classes = master_class_list.split(' ');
                                class_name = '';
                                for(i=0; i < classes.length; i++){
                                    if(classes[i].indexOf('sub-menu-link-') == 0){
                                        class_name = classes[i];
                                    }
                                }
                                link = $('.'+class_name).find('a');
                                if(oEvent.ctrlKey){
                                	window.open(link.attr('href'),'_blank');
                                }else{
	                                if(link.hasClass('no-jsify')){
										window.location.href = link.attr('href');
									}else{
										link.click();
									}
								}
                                $(this).attr('class', master_class_list);
                                
                            }
                        });
                       
					}
                    $(this).siblings('tr').children('td').removeClass('hovering');
                    $(this).children('td').addClass('hovering');
                },
                mouseleave:function(){
					div = $('.sub-sub-menu');
					td = $(this).children('td');
                    hover = false;
					setTimeout(function(){
                        if(!hover){
							td.removeClass('hovering');
                            td.children('a').removeClass('hovering');
                            $('#sub-menu').addClass('hidden-menu');
							div.remove();
						}					
					}, 50);
				}
			});
			
			$('.link-row').unbind('click').click(function(oEvent){
				oEvent.preventDefault();
				link = $(this).find('.level-1').find('a');
				if(oEvent.ctrlKey){
                	window.open(link.attr('href'),'_blank');
                }else{
                    if(link.hasClass('no-jsify')){
						window.location.href = link.attr('href');
					}else{
						link.click();
					}
				}
			});
            $('.headings h2').unbind('click').on({
            	mouseenter:function(){
            		$('.hovering').removeClass('hovering');
            		$(this).addClass('hovering');
            	},
            	mouseleave:function(){
            		$(this).removeClass('hovering');
            	},
            	click:function(oEvent){
            		oEvent.preventDefault();
            		link = $(this).find('a');
            		if(oEvent.ctrlKey){
	                	window.open(link.attr('href'),'_blank');
	                }else{
		                if(link.hasClass('no-jsify')){
							window.location.href = link.attr('href');
						}else{
							link.click();
						}
					}
	                
            	},
            });
		}		
	};
})(jQuery);