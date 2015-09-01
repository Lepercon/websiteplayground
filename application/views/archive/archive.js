(function($) {
	$.archive = {
		section : '',
		page : '',
		init : function() {
			$('#archive-upload-form').submit(function() {
				$(this).find('input[type=submit]').attr('disabled', 'disabled').val('Please Wait...');
				return true;
			});
			var doc_lists = $('#archive-docs ul li');
			var doc_list;
			doc_lists.find('h3').click(function() {
				doc_list = $(this).siblings('ul');
				if(doc_list.is(':visible')) {
					doc_list.hide();
					$(this).find('.archive-arrow').removeClass('archive-rotate');
				}
				else {
					doc_list.show();
					$(this).find('.archive-arrow').addClass('archive-rotate');
				}
			}).css('cursor', 'pointer');
			doc_lists.find('ul').not(':first').hide();
			doc_lists.find('h3 .archive-arrow').first().addClass('archive-rotate');
		}
	};
})(jQuery);