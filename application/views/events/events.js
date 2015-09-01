(function($) {
	$.events = {
		init : function() {
			$('.delete-poster').click(function(event){
				event.preventDefault();
				var button = $(this);
				$.ajax({
					type: "POST",
					url: script_url + 'events/delete_poster',
					data: {
						'event_id' : button.siblings('.event-id').text()
					},
					error: function() {
						$.common.notify('The poster could not be removed.')
					},
					success: function() {
						$('.event-poster-box').html('<div class="validation_success"><span class="ui-icon ui-icon-check inline-block green-icon"></span>Poster Removed.</div>');
					}
				});
			});
		}
	};
})(jQuery);