(function ($) {
	$.involved = {
		section: '',
		page: '',
		init: function () {
			var new_spinner = $('#spinner').clone().attr('id', 'involved-spinner').hide();
			$('#involved-right').append(new_spinner);
			$('#involved-left li a').click(function () {
				$.involved.load_section.call(this);
				return false;
			});
		},

		load_section: function () {
			$(this).addClass('anchor-selected');
			$(this).parent().siblings().find('.anchor-selected').removeClass('anchor-selected');
			$('#involved-content-area').hide();
			$('#involved-spinner').show();
			var url_parts = this.href.slice(this.href.indexOf('index/') + 6).split('/');
			$.involved.page = url_parts[0];
			$.involved.section = url_parts[1];
			$.post(this.href, [{ 'name': 'ajax', 'value': 'none'}], $.involved.load, 'json');
		},

		load: function (data) {
			if (data.redirect) {
				window.location = data.redirect;
				return;
			}
			$('#involved-content-area').html(data.html).jsify();
			$.common.preload_images($('#involved-content-area img'), $.involved.finish_loading);
		},

		finish_loading: function () {
			$('#involved-spinner').hide();
			$('#involved-content-area').show();
			$.common.interface();
			//$('html,body').animate({ scrollTop: $('#involved-right').offset().top }, { duration: 'slow' });
		}
	};
})(jQuery);