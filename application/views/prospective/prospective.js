(function ($) {
	$.prospective = {
		section: '',
		page: '',
		init: function () {
			var new_spinner = $('#spinner').clone().attr('id', 'prospective-spinner').hide();
			$('#prospective-content-area').append(new_spinner);
			$('#prospective-left a').click(function (event) {
				event.preventDefault();
				$.prospective.load_section.call(this);
				return false;
			});
			timer = null;
			$(window).scroll(function(){
				if(timer !== null) {
			        clearTimeout(timer);        
			    }
			    timer = setTimeout(function() {
			    	max_height = $('.content-right').height() - $('.content-left').height() + $('#column-spacer').height();
			    	height = $(window).scrollTop() - $('#column-spacer').offset().top;
			    	if(height > max_height){
			    		height = max_height;
			    	}
			        $('#column-spacer').animate({height:height});
			    }, 200);
			});
		},

		load_section: function () {
			$('.anchor-selected').removeClass('anchor-selected');
			$(this).addClass('anchor-selected');
			$('#prospective-content').hide();
			$('#prospective-spinner').show();
			var url_parts = this.href.slice(this.href.indexOf('index/') + 6).split('/');
			$.prospective.page = url_parts[0];
			$.prospective.section = url_parts[1];
			$('.section-name').html($(this).children('p').html());
			$.post(this.href, [{ 'name': 'ajax', 'value': 'none'}], $.prospective.load, 'json');
		},

		load: function (data) {
			if (data.redirect) {
				window.location = data.redirect;
				return;
			}
			$('#prospective-content').html(data.html).jsify();
			$.common.preload_images($('#prospective-content-area img'), $.prospective.finish_loading);
		},

		finish_loading: function () {
			$('#prospective-spinner').hide();
			$('#prospective-content').show();
			$.common.interface();
		}
	};
})(jQuery);