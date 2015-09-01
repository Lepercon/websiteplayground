(function($) {
	$.details = {
		init : function() {
			// set the upload submit handler
			$('input[value=Upload]').click($.details.upload);
			// set the crop submit handler
			$('input[value=Crop]').click($.details.crop);
			// attach jcrop to the large image if it exists
			var crop_img = $('#de-photo-large');
			if(crop_img.size() > 0) {
				$.details.jcrop_large = $.Jcrop(crop_img, {
					bgColor: 'black',
					minSize: [200, 200],
					setSelect: [50, 50, 200, 200]
				});
			}
			// attach jcrop to the small image if it exists
			crop_img = $('#de-photo-small');
			if(crop_img.size() > 0) {
				$.details.jcrop_small = $.Jcrop(crop_img, {
					aspectRatio: 1, // square
					bgColor: 'black',
					minSize: [200, 200],
					setSelect: [50, 50, 100, 100]
				});
			}
			
			newline = String.fromCharCode(10);
			$('textarea').each(function(){
				$(this).html($(this).html().split('\\n').join(newline));
			});
			
			$('.level-desc').focus(function(){
				if($(this).val() == ''){
					$(this).val($(this).attr('placeholder').substr(0,$(this).attr('placeholder').length-3)+' ');
				}
			}).blur(function(){
				if($(this).val() == ($(this).attr('placeholder').substr(0,$(this).attr('placeholder').length-3)+' ')){
					$(this).val('');
				}
			});
			
		},
		
		upload : function() {
			this.disabled = true;
			if($(this.form).find('h1').size() == 0) $(this.form).prepend('<h1>Uploading, Please wait...</h1>');
			this.form.submit();
		},
		
		crop : function() {
			// get large dims
			var dims = $.details.jcrop_large.tellSelect();
			var inputs = '<input type="hidden" name="x-large" value="'+dims.x+'" />';
			inputs += '<input type="hidden" name="y-large" value="'+dims.y+'" />';
			inputs += '<input type="hidden" name="h-large" value="'+dims.h+'" />';
			inputs += '<input type="hidden" name="w-large" value="'+dims.w+'" />';
			// get small dims
			dims = $.details.jcrop_small.tellSelect();
			inputs += '<input type="hidden" name="x-small" value="'+dims.x+'" />';
			inputs += '<input type="hidden" name="y-small" value="'+dims.y+'" />';
			inputs += '<input type="hidden" name="h-small" value="'+dims.h+'" />';
			inputs += '<input type="hidden" name="w-small" value="'+dims.w+'" />';
			// append inputs to form
			$('#de-crop').append(inputs);
			// call the standard ajax submitter
			$.common.default_submit_handler.call(this);
			return false;
		}
	};
})(jQuery);