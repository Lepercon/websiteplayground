(function ($) {
	$.faults = {
		init: function () {
			$('.faults-delete').click(function(event) {
				event.preventDefault();
				var button = $(this);
				var href = button.attr('href');
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete that report?<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Delete",
					buttons: {
						"Delete": function () {
							$.ajax({
								type: 'POST',
								url: href,
								success: function() {
									button.closest('tr').remove();
								},
								error: function() {
									$.common.notify('The report could not be deleted');
								}
							});
							$(this).dialog("close");
						},
						Cancel: function () {
							$(this).dialog("close");
						}
					}
				});
				return false;
			});
		}
	}
})(jQuery);