(function ($) {
	$.notifications = {
		init: function() {
			$('.n-cell').click(function(event){
				id = $(this).siblings('.n-checkbox').children('.n-id').html();
				$.ajax({
					type: "POST",
					url: script_url + 'finance/notifications/status_change',
					data:{
						ids:id,
						new_status:'1'
					}
				});
				$(this).siblings('.hidden-info').children('.click-link').click();
				$(this).parent().addClass('notification-row-read');
				$(this).parent().removeClass('notification-row-unread');
			});
			
			$('.n-check').change(function(event){
				if($(this).prop('checked')){
					$(this).parent().parent().addClass('row-checked');
					all = true;
					$('.n-check').each(function(){
						all = all && $(this).prop('checked');
					});
					if(all){
						$('.all-check').prop('checked',true);
					}
				}else{
					$(this).parent().parent().removeClass('row-checked');
					$('.all-check').prop('checked',false);
				}
			});
			
			$('.all-check').change(function(event){
				if($(this).prop('checked')){
					$('.n-r').addClass('row-checked');
				}else{
					$('.n-r').removeClass('row-checked');
				}
				$('.n-check').prop('checked',$(this).prop('checked'));
			});
			
			$('.delete-selected').click(function(event){
				event.preventDefault();
				ids = [];
				i = 0;
				$('.n-check').each(function(){
					if($(this).prop('checked')){
						ids[i++] = $(this).siblings('.n-id').html();
						$(this).parent().parent().remove();						
					}
				});
				$.ajax({
					type: "POST",
					url: script_url + 'finance/notifications/delete',
					data:{
						ids:ids.join(',')
					}
				});
			});

			$('.read-selected').click(function(event){
				event.preventDefault();
				ids = [];
				i = 0;
				$('.n-check').each(function(){
					if($(this).prop('checked')){
						ids[i++] = $(this).siblings('.n-id').html();
						$(this).parent().parent().addClass('notification-row-read');
						$(this).parent().parent().removeClass('notification-row-unread');
					}
				});
				$.ajax({
					type: "POST",
					url: script_url + 'finance/notifications/status_change',
					data:{
						ids:ids.join(','),
						new_status:'1'
					}
				});
			});

			$('.unread-selected').click(function(event){
				event.preventDefault();
				ids = [];
				i = 0;
				$('.n-check').each(function(){
					if($(this).prop('checked')){
						ids[i++] = $(this).siblings('.n-id').html();
						$(this).parent().parent().addClass('notification-row-unread');
						$(this).parent().parent().removeClass('notification-row-read');
					}
				});
				$.ajax({
					type: "POST",
					url: script_url + 'finance/notifications/status_change',
					data:{
						ids:ids.join(','),
						new_status:'0'
					}
				});
			});
		}
	};
})(jQuery);