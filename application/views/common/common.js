var loc = window.location;
var full_url = loc.protocol + '//' + loc.host + loc.pathname;
if(full_url.indexOf('/butler.jcr') == -1){
    var script_url = loc.protocol + '//' + full_url.substring(full_url.indexOf('://') + 3, full_url.indexOf('.co.uk')) + '.co.uk/';
}else{
    var script_url = loc.protocol + '//' + full_url.substring(full_url.indexOf('://') + 3, full_url.indexOf('/butler.jcr')) + '/butler.jcr/';
}
view_url = script_url + 'application/views/';
var ext = '.php';
var scriptVersion = '28052014'

if(script_url.indexOf('butler.jcr') !== -1) var ga_account = 'UA-750282-1';

// perform jsify actions.  call via $(el).jsify()
jQuery.fn.jsify = function jsify() {
	this.find('a[href^="'+script_url+'"]').not('a[href*="/application/views/"]').not('.no-jsify').click(function(oEvent) {
        if(this.href != window.loc.href){
            $('link[rel=stylesheet]').not('[href*="common.min.css"]').remove();
        }
		if(oEvent.ctrlKey){
			window.open(this.href,'_blank');
		}else{
			$.common.goTo(this.href);
		}
		return false;

	});
	// get forms on page excluding https and no jsify
	var forms = this.find('form').not('[action^="https"]').not('.no-jsify');
	forms.each(function() {
		// get submits
		var submits = $(this).find('input[type=submit]');
		// check if there are any submits with a name.  If so then the form submit action must come from the button not the form
		if(submits.not('[name=""]').size() > 0) {
			submits.click($.common.default_submit_handler);
		}
		else {
			$(this).submit($.common.default_submit_handler);
		}
	});
	return this;
};

(function($,t,n) {
	
	// SlickNav Responsive Mobile Menu
	var r={label:"Josephine Butler JCR",duplicate:false,duration:500,easingOpen:"swing",easingClose:"swing",closedSymbol:"&#9658;",openedSymbol:"&#9660;",prependTo:"body",parentTag:"a",closeOnClick:false,allowParentLinks:false},i="slicknav",s="slicknav";$.fn[i]=function(n){return this.each(function(){function h($){var t=$.data("menu");if(!t){t={};t.arrow=$.children("."+s+"_arrow");t.ul=$.next("ul");t.parent=$.parent();$.data("menu",t)}if($.parent().hasClass(s+"_collapsed")){t.arrow.html(o.openedSymbol);t.parent.removeClass(s+"_collapsed");p(t.ul,true)}else{t.arrow.html(o.closedSymbol);t.parent.addClass(s+"_collapsed");p(t.ul,true)}}function p($,t){var n=v($);var r=0;if(t)r=o.duration;if($.hasClass(s+"_hidden")){$.removeClass(s+"_hidden");$.slideDown(r,o.easingOpen);$.attr("aria-hidden","false");n.attr("tabindex","0");d($,false)}else{$.addClass(s+"_hidden");$.slideUp(r,o.easingClose,function(){$.attr("aria-hidden","true");n.attr("tabindex","-1");d($,true);$.hide()})}}function d(t,n){var r=t.children("li").children("ul").not("."+s+"_hidden");if(!n){r.each(function(){var t=$(this);t.attr("aria-hidden","false");var r=v(t);r.attr("tabindex","0");d(t,n)})}else{r.each(function(){var t=$(this);t.attr("aria-hidden","true");var r=v(t);r.attr("tabindex","-1");d(t,n)})}}function v($){var t=$.data("menu");if(!t){t={};var n=$.children("li");var r=n.children("a");t.links=r.add(n.children("."+s+"_item"));$.data("menu",t)}return t.links}function m(t){if(!t){$("."+s+"_item, ."+s+"_btn").css("outline","none")}else{$("."+s+"_item, ."+s+"_btn").css("outline","")}}var i=$(this);var o=$.extend({},r,n);if(o.duplicate){var u=i.clone();u.removeAttr("id");u.find("*").each(function(t,n){$(n).removeAttr("id")})}else var u=i;var a=s+"_icon";if(o.label==""){a+=" "+s+"_no-text"}if(o.parentTag=="a"){o.parentTag='a href="#"'}u.attr("class",s+"_nav");var f=$('<div class="'+s+'_menu"></div>');var l=$("<"+o.parentTag+' aria-haspopup="true" tabindex="0" class="'+s+'_btn"><span class="'+s+'_menutxt">'+o.label+'</span><span class="'+a+'"><span class="'+s+'_icon-bar"></span><span class="'+s+'_icon-bar"></span><span class="'+s+'_icon-bar"></span></span></a>');$(f).append(l);$(o.prependTo).prepend(f);f.append(u);var c=u.find("li");$(c).each(function(){var t=$(this);data={};data.children=t.children("ul").attr("role","menu");t.data("menu",data);if(data.children.length>0){var n=t.contents();var r=[];$(n).each(function(){if(!$(this).is("ul")){r.push(this)}else{return false}});var i=$(r).wrapAll("<"+o.parentTag+' role="menuitem" aria-haspopup="true" tabindex="-1" class="'+s+'_item"/>').parent();t.addClass(s+"_collapsed");t.addClass(s+"_parent");$(r).last().after('<span class="'+s+'_arrow">'+o.closedSymbol+"</span>")}else if(t.children().length==0){t.addClass(s+"_txtnode")}t.children("a").attr("role","menuitem").click(function(){if(o.closeOnClick)$(l).click()})});$(c).each(function(){var t=$(this).data("menu");p(t.children,false)});p(u,false);u.attr("role","menu");$(t).mousedown(function(){m(false)});$(t).keyup(function(){m(true)});$(l).click(function($){$.preventDefault();p(u,true)});u.on("click","."+s+"_item",function(t){t.preventDefault();h($(this))});$(l).keydown(function($){var t=$||event;if(t.keyCode==13){$.preventDefault();p(u,true)}});u.on("keydown","."+s+"_item",function(t){var n=t||event;if(n.keyCode==13){t.preventDefault();h($(t.target))}});if(o.allowParentLinks){$("."+s+"_item a").click(function($){$.stopImmediatePropagation()})}})}
	
	// Common code
	$.common = {
		big_title: '<span class="big-text-medium">BUTLER JCR</span>',
		short: '',
		cur_url : '',
		old_url : '',
		cache_urls : [],
		cache : [],
		scripts : [],
		inits : [],
		to_init : [],
		js_loading_count : 0,
		addrchange : true,
		currently_loading : false,
		
		init : function() {
			if($('.slicknav_menu').length < 1) {
				$('#navmob').slicknav();
			}
			// jsify everything!
			$(document).jsify();
			$('#content').on('click', '.admin-delete-button', $.common.conf_delete);
			if(typeof(history_loaded) == 'undefined') {
				History.Adapter.bind(window, 'statechange', function() {
					if(script_url.indexOf('dur.ac.uk') !== -1) {
						_gaq.push(['_trackPageview', History.getState().hash]);
					}
					if($.common.addrchange) {
						$.common.loader(History.getState().url);
					}
				});
				history_loaded = true;
			}

			$("head").find('script').each(function() {
				// push existing scripts onto array of loaded scripts
				$.common.scripts.push(this.src);
				$.common.add_init(this.src);
			});
            
            newline = String.fromCharCode(10); 
			$('textarea').each(function(){
				$(this).html($(this).html().split('\\n').join(newline));
			});
			
			$('.add-bg').click(function(event){
				event.preventDefault();
				$('html').addClass('bg');
				$('.add-bg-link').remove();
			});
			
			if($('.show-survey').length > 0){
				box_content = '<img src="http://ohdoylerules.com/content/images/googleforms.png" width="100px" style="float:left;padding-right:10px;">';
				box_content += "<p>Would you like to fill in an anonymous survey about this site?</p>";
				box_content += "<p>It'll help us keep the content on this site useful and relivent to you and it'll only take a couple of minutes of your time.</p>";
				$('<div/>').html(box_content).dialog({
					buttons: [ 
						{ 
							text: "Ok", 
							click: function() { 
								window.open("https://docs.google.com/forms/d/1uVEn0JS9VV6uL3IdQ8yMiiUBdv0yo7br2o9HPTjJfvQ/viewform?usp=send_form");
								$( this ).dialog( "close" ); 
								$.ajax({
									type: "POST",
									url: location.href,
									data: { 
										surveycomplete : 'done',
									}
								});
							} 
						},
						{ 
							text: "No Thank You",  
							click: function() { 
								$( this ).dialog( "close" );
								$.ajax({
									type: "POST",
									url: location.href,
									data: { 
										surveycomplete: 'skip',
									}
								});
							} 
						}
					],
					title:'Butler JCR Website Survey',
					width:'400px',
					modal:true,
					draggable:false,
					resizable:false,
					show: 'drop',
					hide: true
				});
			}
			            
            $('.print-page').click(function(event){
            	event.preventDefault();
            	window.print();
            });
            
            if($('#cookie-popup').length > 0){
            	message = $('#cookie-popup').html();
            	$('<div></div>').dialog({
					modal: true,
					resizable: false,
					open: function() {
						$(this).html(message);
					},
					buttons: {
						Ok: function() {
							$(this).dialog('close');
							$.ajax({
								type: 'POST',
								url: script_url + 'home/cookie_prompt'
							});
							if($(window).width() > 900){
								//$.common.tutorial();
							}
						}
					},
                    close:function() {
                        $.ajax({
                            type: 'POST',
                            url: script_url + 'home/cookie_prompt'
                        });
                    }
				});
            }
            
            if(($('.no-profile-image').length > 0) && ($(window).width() > 900)){
            	window.scrollTo(0, 0);
            	layer = $('<div/>').addClass('spesificPropertiesDiv upper-layer').appendTo($('body'));
            	arrow = $('<div/>').addClass('arrow upper-layer').appendTo($('body'));
            	$('<div/>').html('Did you know you can edit your profile photo by clicking here?').addClass('prompt-text').appendTo(arrow);
            	link = $('<a/>').appendTo($('body')).addClass('profile-link upper-layer');
            	link.attr('href', script_url+'details/profile');
            	profile = $('<div/>').addClass('upper-profile-photo upper-layer').appendTo(link);
            	existing = $('#header-profile');
            	profile.css('background-image', existing.css('background-image'));
            	profile.css('margin', existing.css('margin'));
            	profile.css('padding', existing.css('padding'));
            	profile.css('height', existing.height());
            	profile.css('width', existing.width());
            	profile.css('top', existing.position().top);
            	profile.css('left', existing.position().left);
            	arrow.css('left', $('#login-details').position().left-arrow.width());
            	arrow.css('top', 10);
            	$('.spesificPropertiesDiv, .upper-layer').click(function(){
            		$('.upper-layer').remove();
            		$.ajax({
						type: "POST",
						url: script_url + 'details/profile'
					});

            	}); 
            	
            }
            
            
            if($('#nameentry').length > 0){
            	var availableTags = [];
				$('#users-list').children('p').each(function(){
					availableTags.push({value:$(this).text(),id:$(this).attr('value')});
				});
            	$('#nameentry').autocomplete({
			      	source: availableTags,
			      	change: function(event, user){
			      		if(user.item == null){
			    			$('#nameentry-id').val('');
			    		}else{
			    			$('#nameentry-id').val(user.item.id);
			    		}
			      	}
			    });
            }
            
            /* file upload */
            var doc_lists = $('#uploads .year-title');
			var doc_list;
			doc_lists.click(function() {
				doc_list = $(this).siblings('table');
				if(doc_list.is(':visible')) {
					doc_list.hide();					
					$(this).find('.file-arrow').removeClass('file-rotate');
				}
				else {
					doc_list.show();
					$(this).find('.file-arrow').addClass('file-rotate');
				}
			});
			doc_lists.siblings('.file-table').not(':first').hide();
			//doc_lists.find('h3 .file-arrow').first().addClass('archive-rotate');

			
			$.common.js_init();
			$.common.interface();
		},
		
		move_v_mouse: function(object, func) {
			$('#vmouse').animate({
				top:object.offset().top+object.height()/2,
				left:object.offset().left+object.width()/2
			},{
				complete:func
			});
		},
		
		tutorial: function(){
			setTimeout(function(){
	            window.scrollTo(0, 0);
	            endlink = window.location.href;
	            layer = $('<div/>').addClass('spesificPropertiesDiv upper-layer tutorial').appendTo($('body'));
	            textprompt = $('<div/>').appendTo($('body')).addClass('text-display');
	            textprompt.html('Let me show you around...');
	            $('.link-head').eq(0).click();
	            numclicks = 0;
	            layer.click(function(){
	            	numclicks += 1;
	            	if(numclicks == 1){
	            		layer.animate({'opacity':0.2});
	            		mouse = $('<div/>').attr('id', 'vmouse').appendTo($('body'));
	            		textprompt.html('Do you want to know who we are?');
		            	setTimeout(function(){
		            		$('#sub-menu').find('tr').mouseleave();
		            		$.common.move_v_mouse($('.heading').eq(1), function(){
		            			$('.link-head').eq(1).mouseenter();
		            			$('.link-head').eq(1).click();
		            		});
	            		},200);
	            	}else if(numclicks == 2){
	            		textprompt.html('Or do you want to know what we have on?');
	            		setTimeout(function(){
		            		//$.common.move_v_mouse($('.link-head').eq(1), function(){
		            			$('.link-head').eq(1).mouseenter();
		            			setTimeout(function(){
		            				$.common.move_v_mouse($('.heading').eq(1).find('tr').eq(0), function(){
		            					$('.link-head').eq(1).mouseleave();
				            			$('.heading').eq(1).find('tr').eq(0).mouseenter();
				            			setTimeout(function(){
				            				$.common.move_v_mouse($('#sub-menu').find('tr').eq(0), function(){
				            					$('#sub-menu').find('tr').eq(0).mouseenter();
				            					$.common.move_v_mouse($('#sub-menu').find('tr').eq(1), function(){
				            						$('#sub-menu').find('tr').eq(0).mouseleave();
				            						$('.heading').eq(1).find('tr').eq(0).mouseenter();
				            						setTimeout(function(){
				            							$('#sub-menu').find('tr').eq(1).mouseenter();
					            						$('#sub-menu').find('tr').eq(1).click();
					            					},30);
				            					});
				            				});
					            		},200);
				            		});
			            		},200);
		            		//});
	            		},200);
	            	}else if(numclicks == 3){
	            		textprompt.html('And what we are up to?');
	            		setTimeout(function(){
		            		$.common.move_v_mouse($('.heading').eq(1).find('tbody').eq(0).children('tr').eq(5), function(){
		            			$('.heading').eq(1).find('tbody').eq(0).children('tr').eq(5).mouseenter();
		            			setTimeout(function(){
		            				$.common.move_v_mouse($('#sub-menu').find('tr').eq(0), function(){
	            						$('#sub-menu').find('tr').eq(0).mouseenter();
		            					$('#sub-menu').find('tr').eq(0).click();
	            					});
	            				},30);
		            		});
		            	},200);
		            }else if(numclicks == 4){
		            	textprompt.html('Do you fancy coming back?');
		            	setTimeout(function(){
		            		$('#sub-menu').find('tr').mouseleave();
		            		$.common.move_v_mouse($('.heading').eq(5), function(){
		            			$('.link-head').eq(3).mouseenter();
		            			$('.link-head').eq(3).click();
		            		});
	            		},200);
	            	}else if(numclicks == 5){
		            	textprompt.html('Or have you never been here before?');
		            	setTimeout(function(){
		            		$('#sub-menu').find('tr').mouseleave();
		            		$.common.move_v_mouse($('.heading').eq(6), function(){
		            			$('.link-head').eq(4).mouseenter();
		            			setTimeout(function(){
		            				$.common.move_v_mouse($('.heading').eq(6).find('tr').eq(2), function(){
		            					$('.link-head').eq(4).mouseleave();
				            			$('.heading').eq(6).find('tr').eq(2).mouseenter();
				            			$('.heading').eq(6).find('tr').eq(2).click();
				            		});
			            		},200);
		            		});
	            		},200);
	            	}else if(numclicks == 6){
	            		layer.animate({'opacity':0.2});
	            		textprompt.html('Enjoy...');
	            		setTimeout(function(){
		            		$('#sub-menu').find('tr').mouseleave();
		            		$.common.move_v_mouse($('.heading').eq(0), function(){
		            			$('.link-head').eq(0).mouseenter();
		            			$('.link-head').eq(0).click();
		            			setTimeout(function(){
		            				mouse.remove();
		            				$('.link-head').eq(0).mouseleave();
	            					$('#sub-menu').find('tr').mouseleave();	
		            			},300);
		            		});
	            		},200);
	            	}else if(numclicks == 7){
	            		layer.remove();
	            		mouse.remove();
	            		textprompt.remove();
	            		window.location.href = endlink;
	            	}
	            });
            },200);
		},
		
		interface: function() {
			// attach tooltip to form inputs
			$('.apt, .input-help, .jcr-button').tooltip({
				open: function(event, ui) {
					if($('.ui-tooltip').length > 1) {
						$('.ui-tooltip').not(':last').remove();
					}
				}
			});
			// attach datepicker to form inputs
			$('.datepicker').datepicker({
				'dateFormat'	: "dd/mm/yy",
				'duration'		: "fast",
				'firstDay'		: 1, // make Monday first day of week
				'numberOfMonths': 2, // display 1 month of calendar in the drop-down
				'beforeShow'	: function() {
					$('.ui-tooltip').remove();
				}
			});
			$('#accordion').accordion({
				heightStyle: "content",
				collapsible: true
			});
			
            $('.page-content-area').each(function(){
                if($(this).siblings('input.page-rights').val() > 0){
                    $.common.editorButton($(this));
                }
            });
			
			if($('a.photo-thumb').length > 0) {
				$('a.photo-thumb').click(function(event){
					// which was clicked?
					$.common.sbClick($(this).index('a.photo-thumb'));
					$(document).on("keydown.g", function(event) {
						if (event.which == "27") {
							//ESC key
							event.preventDefault();
							$.common.sbCancel();
						} else if(event.which == "37") {
							// Left arrow
							event.preventDefault();
							$.common.sbPrev();
						} else if(event.which == "39") {
							// Left arrow
							event.preventDefault();
							$.common.sbNext();
						}
					});
					event.preventDefault();
				});
				
				$(document).on('click', ".sb-cancel", function(event){
					$.common.sbCancel();
					event.preventDefault();
				});
				
				$(document).on('click', ".sb-next-on", function(event){
					$.common.sbNext();
					event.preventDefault();
				});
				
				$(document).on('click', ".sb-prev-on", function(event){
					$.common.sbPrev();
					event.preventDefault();
				});
			}
		},

		goTo: function(url) {
			if(script_url.indexOf('butler.jcr') !== -1) History.pushState(null, null, '/butler.jcr/'+url.substring(script_url.length));
			else History.pushState(null, null, url.substring(script_url.length-1));
		},
		
		loader : function(url, data) {
			if(url === null) return;
			
			// check if page is cached
			var cache_index = $.inArray(url, $.common.cache_urls);
			if(cache_index != -1 && !data) {
				$.common.load_page($.common.cache[cache_index]);
				return;
			}
			
			$.common.big_title = $('#big-title').html();
			$.common.short = $('#nav').attr('class');
			$.common.old_url = $.common.cur_url;
			$.common.cur_url = url;
			if($('.slicknav_nav').is(':visible')) {
				$('.slicknav_btn').click();
			}
			if($('.mce-tinymce').length > 0) {
				tinymce.remove("#page-content-area");
			}
			$('#content-area').hide();

			$('#spinner').show();
			$.common.set_page_attributes('<span class="big-text-medium">BUTLER JCR</span>', '');

			$.ajax({
				url: url,
				type: (data == null ? 'GET' : 'POST'),
				cache: false,
				dataType: 'json',
				data: data,
				error: function(XHR){
					$.common.cur_url = $.common.old_url;
					$.common.old_url = '';
					$('#spinner').hide();
					$('#content-area').show();
					$.common.set_page_attributes($.common.big_title, $.common.short, 'Error');
					$.common.notify('The requested page could not be loaded. Please check your internet connection and try again.');
				},
				success: $.common.load_page
			});
		},

		notify : function(message) {
			$('<div></div>').dialog({
				modal: true,
				resizable: false,
				open: function() {
					$(this).html(message);
				},
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		},
		
		load_page : function(data) {

			// unload old js
			for(var i in $.common.inits) {
				if($[$.common.inits[i]]['unload']) $[$.common.inits[i]]['unload']();
			}

			if(typeof data != 'object') { // error
				try {
					var tmp = $.parseJSON(data);
				}
				catch(e) {
					$('#content').html(data);
					$('#spinner').hide();
					$('#content-area').show();
					return;
				}
				data = tmp;
			}
			// redirect if told to do so (logout etc)
			if(data.redirect) {
				window.location = data.redirect;
				return;
			}
			
			$.common.currently_loading = true;
			
			// save to cache
			if(data.keep_cache && !data.from_cache) {
				if($.isArray(data.keep_cache)) {
					for(var i in data.keep_cache) {
						if((script_url +data.keep_cache[i]) == $.common.cur_url) {
							data.keep_cache = true;
							break;
						}
					}
				}
				if(data.keep_cache === true) {
					var index = $.common.cache_urls.push($.common.cur_url) - 1;
					$.common.cache[index] = $.extend(true, {}, data);
					$.common.cache[index].from_cache = true;
				}
			}
			
			// load page css
			if(data.css && !data.from_cache) {
				$(data.css).each(function(i, css) {
					// is this css already loaded?
					if($("head").find('link[href="'+css+'"]').size() == 0) {
						// append it
						$("head").append($('<link />', {
							rel:  "stylesheet",
							href: css
						}));
					}
				});
			}
			
			// reset inits array
			$.common.inits = [];
			// load page js
			if(data.js) {
				$.common.js_loading_count = 0;
				$.each(data.js, function(i, url) {
					// has this file already been loaded?
					if($.inArray(url, $.common.scripts) == -1) {
						// increase the loading scripts count
						$.common.js_loading_count++;
						// get the script
						$.common.get_script(url, function() {
							//push the script onto the loaded scripts array
							$.common.scripts.push(url);
							// add the init to the array
							$.common.add_init(url);
							// decrease the loading scripts count
							$.common.js_loading_count--;
							// call the initialiser function
							$.common.js_init();
						});
					}
					else $.common.add_init(url);
				});
				// if no scripts to load go ahead and init all the others
				if($.common.js_loading_count == 0) $.common.js_init();
				// force the script to initialise other scripts after 5 seconds of waiting
				else $.common.to = setTimeout(function() { $.common.js_init(true); }, 5000);
			}
			
			// jsify page
			$('#content').html(data.html).jsify();

			// preload images
			if(!data.from_cache) $.common.preload_images($('#content-area img'), function() { $.common.finish_loading(data); });
			else $.common.finish_loading(data);
		},
		
		get_script : function(url, callback){
			var head = $("head")[0];
			var script = document.createElement("script");
			script.src = url;
			{
				var done = false;
				script.onload = script.onreadystatechange = function(){
					if(!done && (!this.readyState||this.readyState == "loaded"||this.readyState == "complete")){
						done = true;
						if(callback)callback();
						script.onload = script.onreadystatechange = null;
					}
				};
			}
			head.appendChild(script);
			return undefined;
		},
		
		add_init : function(url) {
			// determine init name from file name
			var init_name = url.substring(url.lastIndexOf('/') + 1, url.lastIndexOf('.'));
			// does class exist?
			if(init_name.indexOf('-') == -1 && init_name != 'common' && $[init_name] && $[init_name]['init']) {
				// does init function exist?  if so, add it to array of inits to call when everything is loaded
				$.common.inits.push(String(init_name));
			}
		},
		
		finish_loading : function(data) {
			$.common.set_page_attributes(data.big_title, data.short, data.title);
			// on finish loading hide the spinner and show the content
			$('#spinner').hide();
			$('#content-area').show();
			$.common.currently_loading = false;
			// force reload of twitter widget
			$.common.interface();
			if(typeof(twttr) !== 'undefined') {
				twttr.widgets.load();
			}
			$.common.js_init();
		},
		
		set_page_attributes : function(big_title, short, title) {
			$('#big-title').html(big_title);
			// change nav state
			$('#nav').attr('class', short);
			// set page title
			if(title) document.title = 'Butler College JCR - '+title;
		},
		
		// this function is called every time a js file is loaded, but only does something when all js files are loaded or 5 seconds has expired
		js_init: function(force) {
			if(force === true) $.common.js_loading_count = 0;
			if($.common.js_loading_count == 0 && $.common.currently_loading === false) {
				clearTimeout($.common.to);
				delete $.common.to;
				for(var i in $.common.inits) {
					$[$.common.inits[i]]['init']();
				}
				// js init is the final word on initialisation
				$(document).trigger('common_loading_finished');
			}
		},
		
		preload_images : function(el, callback) {
			// array of loaded images' urls
			var images = [];
			// initially no images
			var hasimages = false;
			var finished_counting = false;
			var callback_called = false;
			el.each(function(i) {
				// if we're here then this content has images..
				hasimages = true;
				// set this image's src to a var
				var src = this.src;
				// add this image to our images array
				images.push(src);
				// create a new image
				var img = new Image();
				// callback when image has finished loading
				$(img).load(function(){
					var index = $.inArray(src, images);
					images.splice(index,1);
					if(finished_counting && images.length <= 0) {
						callback.call();
						clearTimeout($.common.fin);
						callback_called = true;
					}
				});
				if(i == el.length - 1) finished_counting = true;
				// set our new image's src
				img.src = src;
			});
			if(!callback_called) {
				// a timeout needs setting to load the page anyway even if the images aren't loaded
				if(hasimages) $.common.fin = setTimeout(callback, 5000);
				// load page if no images.  Small timer to allow html to be processed
				else setTimeout(callback, 10);
			}
		},
		
		default_submit_handler : function() {
			// get form
			var form = $(this).closest('form');
			// get target address
			var addr = form.attr('action');
			// check for https and return true, form will just submit.
			if(addr.indexOf('https') !== -1) return true;
			// get form data
			var data = form.serializeArray();
			// if submit button has a name attribute we need to send this too
			if(this.name) data.push({ name: this.name, value: this.value });
			// load page
			$.common.post_data_change_url(addr, data);
			// return false to the submit request
			return false;
		},
		
		post_data_change_url : function(url, data) {
			// don't submit using address change callback
			$.common.addrchange = false;
			// change url
			$.common.goTo(url);
			// post it!
			$.common.loader(url, data);
			// revert address change
			$.common.addrchange = true;
		},
		
		print : function() {
			window.print();
			return false;
		},

		conf_delete: function() {
			var cont = $('<div />').addClass('delete-conf-box');
			cont.html('Are you sure you want to delete that?<br />');
			var del = this;
			cont.dialog({
				resizable: false,
				draggable: false,
				modal: true,
				title: "Confirm Delete",
				buttons: {
					"Delete": function () {
						$.common.goTo(del.href);
						$(this).dialog("close");
					},
					Cancel: function () {
						$(this).dialog("close");
					}
				}
			});
			return false;
		},
		
		sbClick: function(clicked) {
			// create smoothbox
			$('body').append('<div class="smoothbox"><div class="smoothbox-table"><div class="smoothbox-centering"><div class="smoothbox-sizing"><div class="sb-nav"><a href="#" class="sb-prev sb-prev-on" alt="Previous">&larr;</a><a href="#" class="sb-cancel" alt="Close">&times;</a><a href="#" class="sb-next sb-next-on" alt="Next">&rarr;</a></div><ul class="sb-items"></ul></div></div></div></div>');
			
			$.fn.reverse = [].reverse;
			// get each picture, put them in the box
			$('a.photo-thumb').reverse().each(function() {
				var href = $(this).attr('href');
				if ($(this).attr('title')) {
					var caption = $(this).attr('title');
					$('.sb-items').append('<div class="sb-item"><div class="sb-caption">'+ caption +'</div><img src="'+ href + '"/></div>');
				} else {
					$('.sb-items').append('<div class="sb-item"><img src="'+ href + '"/></div>');
				}
			});
			
			$('.sb-item').slice(0,-(clicked)).appendTo('.sb-items');
			$('.sb-item').not(':last').hide();
			$('.sb-item img:last').load(function() { 
				$('.smoothbox-sizing').fadeIn('slow', function() {
					$('.sb-nav').fadeIn();
				});
			});
		},
		
		sbCancel: function() {
			$(document).unbind("keydown.g");
			$('.smoothbox').fadeOut('slow', function() {
				$('.smoothbox').remove();
			});
		},
		
		sbNext: function() {
			$('.sb-next').removeClass('sb-next-on');
			$('.sb-item:last').addClass('sb-item-ani');
			// after animation, move order & remove class
			$(".sb-item:last").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
				$('.sb-item').eq(-2).addClass('no-trans').fadeIn('fast');
				$(this).removeClass('sb-item-ani').prependTo('.sb-items').hide();
				$('.sb-item:last').removeClass('no-trans');
				$('.sb-next').addClass('sb-next-on');
				$('.sb-item').unbind();
			});
		},
		
		sbPrev: function() {
			$('.sb-prev').removeClass('sb-prev-on');
			$('.sb-item:last').hide();
			$(".sb-item:first").addClass('sb-item-ani2 no-trans').appendTo('.sb-items');
			$('.sb-item:last').show().removeClass('no-trans').delay(1).queue(function(next){
				$('.sb-item:last').removeClass('sb-item-ani2');
				next();
			});
			$('.sb-prev').addClass('sb-prev-on');
		},
		
		editorButton: function(content) {
			c = $('<button />', {'class' : 'jcr-button editable-launch', 'title' : 'Edit this content'}).click(function() {
				if(typeof(tinymce) === 'undefined') {
					// Get tinymce script
					$.ajax({
						url: view_url + 'common/tinymce/tinymce.min.js?v=' + scriptVersion,
						dataType: "script",
						cache: true,
						success: function() {
							$.common.launchEditor(content);
						},
						error: function() {
							$.common.notify('The page editor could not be loaded.');
						}
					});
					// Get editor stylesheet
					$("head link[rel='stylesheet']").last().after("<link rel='stylesheet' href='"+view_url + "common/editable/editable.css?v=" + scriptVersion+"' type='text/css'>");
				} else {
					$.common.launchEditor(content);
				}
			}).hover(
				function() {
					content.addClass('editable-border');
				}, function() {
					content.removeClass('editable-border');
				}
			).append($('<span />', {'class' : 'ui-icon ui-icon-pencil'})).tooltip().insertBefore(content);
		},
		
		launchEditor: function(content) {
			content.siblings('.editable-launch').remove();
			content.removeClass('editable-border');
			var editableOriginal = content.html();
			tinymce.baseURL = view_url + 'common/tinymce';
			tinymce.suffix = ".min";
            content.attr('id', 'page-content-area');
			tinymce.init({
				selector: "#page-content-area",
				theme: "modern",
				content_css: view_url+'common/common.min.css?v=' + scriptVersion,
				relative_urls: false,
				convert_urls: false,
				plugins: [
					"save paste link youtube"
				],
				menubar: false,
				toolbar: "save cancel | undo redo | cut copy paste | styleselect | bold italic underline | bullist numlist | link insertimage insertdoc insertcontact",
				style_formats: [
					{title: "Heading 1", format: "h1"},
					{title: "Heading 2", format: "h2"},
					{title: "Heading 3", format: "h3"},
					{title: "Paragraph", format: "p"},
					{title: "Inline", items: [
						{title: "Bold", icon: "bold", format: "bold"},
						{title: "Italic", icon: "italic", format: "italic"},
						{title: "Underline", icon: "underline", format: "underline"},
						{title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
						{title: "Superscript", icon: "superscript", format: "superscript"},
						{title: "Subscript", icon: "subscript", format: "subscript"},
					]},
					{title: "Alignment", items: [
						{title: "Left", icon: "alignleft", format: "alignleft"},
						{title: "Center", icon: "aligncenter", format: "aligncenter"},
						{title: "Right", icon: "alignright", format: "alignright"},
						{title: "Justify", icon: "alignjustify", format: "alignjustify"}
					]}
				],
				statusbar: true,
				save_enablewhendirty: false,
				save_onsavecallback: function() {
					$.ajax({
						type: "POST",
						url: script_url + 'editable_c/save_page/' + $('#page-content-area').siblings('.page-name').val(),
						dataType: 'json',
						data: {
							'content' : tinymce.get('page-content-area').getContent(),
							'file_path' : $('#page-content-area').siblings('.page-path').val(),
							'auth' : $('#page-content-area').siblings('.page-auth').val()
						},
						success: function(data) {
                            data = JSON.parse(data.html);
							if(typeof data != 'object') {
								console.log(data);
								return;
							}
							if(data.success) {
								tinymce.remove("#page-content-area");
								if(data.replaceWith) {
									$("#page-content-area").html(data.replaceWith);
								}
								$.common.editorButton($('#page-content-area'));
                                $('#page-content-area').attr('id', '');
							}else{
                                console.log('sOmething went wr0ng');
                            }
						},
						error: function() {
							$.common.notify('Your changes could not be saved.');
						}
					});
				},
				save_oncancelcallback: function() {
					tinymce.remove("#page-content-area");
					$("#page-content-area").html(editableOriginal);
					$.common.editorButton($('#page-content-area'));
                    $('#page-content-area').attr('id', '');
				},
				setup: function(editor) {
					editor.addButton('insertimage', {
						icon: "image",
						tooltip: "Insert image",
						onclick : function() {
							$.common.editorImage(editor, false);
						}
					});
					editor.addButton('insertdoc', {
						icon: "newdocument",
						tooltip: "Insert document",
						onclick : function() {
							$.common.editorDoc(editor);
						}
					});
					editor.addButton('insertcontact', {
						text: "Contact",
						tooltip: "Insert contact link",
						onclick : function() {
							$.common.editorContact(editor);
						}
					});
				}
			});
		},
		
		editorImage: function(editor) {
			var url;
			$('<div />', {
				'class' : 'editable-image-create'
			}).dialog({
				title: 'Insert Image',
				resizable: false,
				modal: true,
				width: 550,
				open: function(event, ui) {
					var dialog = $(this);
					$('.editable-image-create').append('<h3>Enter URL</h3><div class="editable-image-create-inserturl"></div><h3>Upload Image</h3><div><iframe id="editable-image-iframe"></iframe></div><h3>Insert Existing Image</h3><div class="editable-image-create-content"></div>').accordion({
						collapsible : true,
						heightStyle: "content",
						create: function(event, ui) {
							var accordion = $(this);
							// Insert from URL
							$('.editable-image-create-inserturl').append(
								$('<input />', { 'type' : 'text', 'value' : 'http://' }).focus()
								.add(
									$('<input />', { 'type' : 'button', 'value' : 'Insert' })
									.click(function() {
										var input = this;
										url = $(this).siblings('input').val();
										var img = new Image();
										img.onerror = function() {
											$(input).siblings('p').addClass('red').text('Invalid Image');
										};
										img.onload = function() {
											editor.insertContent('<img src="'+url+'"/>');
											dialog.dialog("close");
										};
										if($(this).siblings('p').size() == 0) $(this).parent().prepend($('<p>Checking...</p>'));
										else $(this).siblings('p').text('Checking...').removeClass('red');
										img.src = url;
									})
								)
							);
							
							var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
							$('.editable-image-create-content').html(new_spinner);
							
							$.common.getExistingImages(dialog, editor);
							
							var imageIframe = $('<form />', {
								'action' : script_url + 'editable_c/save_image/' + $('#page-content-area').siblings('.page-name').val(),
								'method' : 'post',
								'accept-charset' : 'utf-8',
								'enctype' : 'multipart/form-data'
							}).append('<input type="file" name="image"><input type="submit" value="Upload"><input type="hidden" name="auth" value="'+$('#page-content-area').siblings('.page-auth').val()+'">');
							
							$('#editable-image-iframe').contents().find('body').html(imageIframe);
							$('#editable-image-iframe').load(function() {
								$(this).contents().find('body').append(imageIframe);
								var editableError = $('#editable-image-iframe').contents().find('.editable-error');
								if(editableError.length > 0) {
									editableError.remove();
									$('#editable-image-iframe').before(editableError);
									accordion.accordion({active : 1});
								} else {
									accordion.accordion({active : 2});
									$.common.getExistingImages(dialog, editor);
								}
							});
						}
					});
				},
				close: function(event, ui) {
					$(this).dialog('destroy').remove();
				}
			});
		},
		
		getExistingImages: function(dialog, editor) {
			$.ajax({
				dataType: "html",
				url: script_url + 'editable_c/load_page_images/' + $('#page-content-area').siblings('.page-name').val(),
				data: { 'auth' : $('#page-content-area').siblings('.page-auth').val()},
				success : function(data) {
					$('.editable-image-create-content').html(JSON.parse(data).html);
					$('.editable-image-create-addexisting li').click(function(){
						var imgSrc = $(this).find('img').attr('src').replace('_thumb', '');
						editor.insertContent('<img src="'+imgSrc+'"/>');
						dialog.dialog("close");
					});
					// image delete click
					$('.editable-image-create-remove').click(function(e){
						e.stopPropagation();
						var thumbSrc = $(this).siblings('img').attr('src');
						var src = thumbSrc.replace('_thumb', '');
						
						var cont = $('<div />').addClass('delete-conf-box');
						cont.html('Are you sure you want to delete that image?<br />');
						cont.dialog({
							resizable: false,
							draggable: false,
							modal: true,
							title: "Confirm Delete",
							buttons: {
								"Delete": function () {
									$.post(script_url + 'editable_c/delete_image/' + $('#page-content-area').siblings('.page-name').val(), {'image':src.substring(src.lastIndexOf('/')+1)}, function(){
										$('.editable-image-create-content').find('img[src="'+thumbSrc+'"]').closest('li').remove();
										if($('.editable-image-create-content').find('ul').html() == '') {
											$('.editable-image-create-content').prepend('<h3>No images exist</h3>');
										}
									});
									$(this).dialog("close");
								},
								Cancel: function () {
									$(this).dialog("close");
								}
							},
							close: function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						});
					});
				}
			});
		},
		
		editorDoc: function(editor) {
			$('<div />', {
				'class' : 'editable-doc-create'
			}).dialog({
				title: 'Insert Document',
				resizable: false,
				modal: true,
				width: 450,
				open: function(event, ui) {
					var dialog = $(this);
					$('.editable-doc-create').append('<h3>Upload Document</h3><div><iframe id="editable-doc-iframe"></iframe></div><h3>Insert Existing Document</h3><div class="editable-doc-add"></div>').accordion({
						collapsible : true,
						heightStyle: "content",
						create: function(event, ui) {
							var accordion = $(this);
							var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
							$('.editable-doc-add').html(new_spinner);
							$.common.getExistingDocs(dialog, editor);
							
							var docIframe = $('<form />', {
								'action' : script_url + 'editable_c/save_doc/' + $('#page-content-area').siblings('.page-name').val(),
								'method' : 'post',
								'accept-charset' : 'utf-8',
								'enctype' : 'multipart/form-data'
							}).append('<input type="file" name="doc"><input type="submit" value="Upload">');
							
							$('#editable-doc-iframe').contents().find('body').html(docIframe);
							$('#editable-doc-iframe').load(function() {
								$(this).contents().find('body').append(docIframe);
								var editableError = $('#editable-doc-iframe').contents().find('.editable-error');
								if(editableError.length > 0) {
									editableError.remove();
									$('#editable-doc-iframe').before(editableError);
									accordion.accordion({active : 0});
								} else {
									accordion.accordion({active : 1});
									$.common.getExistingDocs(dialog, editor);
								}
							});
						}
					});
					
					
					var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
					$('.editable-doc-add').html(new_spinner);
					$.common.getExistingDocs(dialog, editor);
				},
				close: function(event, ui) {
					$(this).dialog('destroy').remove();
				}
			})
		},
		
		getExistingDocs: function(dialog, editor) {
			$.ajax({
				dataType: "html",
				url: script_url + 'editable_c/load_page_docs/' + $('#page-content-area').siblings('.page-name').val(),
				data: {
					'auth' : $('#page-content-area').siblings('.page-auth').val()
				},
				cache: false,
				success : function(data) {
					$('.editable-doc-add').html(data);
					// Doc select click
					$('.editable-doc-create li').click(function(){
						dialog.dialog("close");
						url = $(this).find('.editable-doc-create-url').text();
						file = $(this).find('.editable-doc-create-full-filename').text();
						editor.insertContent('<a class="no-jsify" href="'+url+'">'+file+'</a>');
					});
					// Doc delete click
					$('.editable-doc-create-remove').click(function(e){
						e.stopPropagation();
						var url = $(this).siblings('.editable-doc-create-details').children('.editable-doc-create-url').text();
						var docName = $(this).siblings('.editable-doc-create-details').children('.editable-doc-create-full-filename').text();
						var cont = $('<div />').addClass('delete-conf-box');
						cont.html('Are you sure you want to delete '+docName+'?<br />');
						cont.dialog({
							resizable: false,
							draggable: false,
							modal: true,
							title: "Confirm Delete",
							buttons: {
								"Delete": function () {
									$.post(script_url + 'editable_c/delete_doc/' + $('#page-content-area').siblings('.page-name').val(), {'doc':docName}, function(){
										$('.editable-doc-add').find('div.editable-doc-create-url:contains('+url+')').closest('li').remove();
										if($('.editable-doc-add').find('ul').html() == '') {
											$('.editable-doc-add').prepend('<h3>No documents exist</h3>');
										}
									});
									$(this).dialog("close");
								},
								Cancel: function () {
									$(this).dialog("close");
								}
							},
							close: function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						});
					});
				}
			});
		},
		
		editorContact: function(editor) {
			var result;
			
			$('<ul />', {
				'class' : 'editable-function-insert-params'
			}).dialog({
				title: 'Insert Contact Link',
				resizable: false,
				modal: true,
				width: 400,
				buttons: {
					"Insert" : function() {
						var params = [];
						$('.editable-function-insert-params li').each(function() {
							var input = $(this).find('input, select');
							var param = result['get_exec_contact'].params[input.attr('name')];
							var val = (param.type == 'bool' ? (input.attr('checked') ? 1 : 0) : input.val());
							params.push('\''+val+'\'');
						});
						
						editor.insertContent('{get_exec_contact: '+params.join(', ')+'}');
						$(this).dialog("close");
					}
				},
				open : function(event, ui) {
					var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
					$('.editable-function-insert-params').before(new_spinner);
					$.getJSON(script_url + 'editable_c/get_page_functions/' + $('#page-content-area').siblings('.page-name').val(), function(data) {
						result = data;
						var get_ip = function(param, type, options) {
							if(type == 'bool') return $('<input />', { 'id' : 'editable-contact-link-' + param, 'type' : 'checkbox', 'name' : param, 'value' : '1' });
							if(type == 'select') return $('<select />', { 'id' : 'editable-contact-link-' + param, 'name' : param }).append(options);
						};
						var paramsList = $();
						$.each(result['get_exec_contact'].params, function(param) {
							if(typeof this.options == 'undefined') this.options = null;
							paramsList = paramsList.add(
								$('<li />').append($('<label />', {'for' : 'editable-contact-link-' + param}).html(this.desc)).append(get_ip(param, this.type, this.options))
							);
						});
						$('#dialog-spinner').remove();
						$('.editable-function-insert-params').html(paramsList);
					});
				},
				close: function(event, ui) {
					$(this).dialog('destroy').remove();
				}
			});
		}
		

	};

	// initialise on load
	$(document).ready($.common.init);
	
}(jQuery, document, window));