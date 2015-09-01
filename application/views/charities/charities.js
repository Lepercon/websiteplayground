(function ($) {
	$.charities = {
		init: function () {
			// Delete from basket button
			
			if($('#column-spacer').length > 0){
				timer = null;
				$(window).scroll(function(){
					if(timer !== null) {
				        clearTimeout(timer);        
				    }
				    timer = setTimeout(function() {
				    	height = $(window).scrollTop() - $('#column-spacer').offset().top;
				    	max_height = $('.content-left').height() - $('.content-right').height() + $('#column-spacer').height();			    	
				    	if(height > max_height){
				    		height = max_height;
				    	}
				        $('#column-spacer').animate({height:height});
				    }, 200);
				});		
			}		
			
			$('.charity-delete-basket').click(function(e) {
				e.preventDefault();
				var deleteButton = $(this);
				deleteButton.hide();
				var href = $(this).attr('href');
				$.ajax({
					url: href,
					type: "GET",
					success: function() {
						deleteButton.closest('tr').remove();
						$.charities.totalPrice();
					},
					error: function() {
						$.common.notify('The item could not be removed from the basket.');
					}
				});
			});
			$('#charity-create-album').hide();
			$('#charity-create-album-button').click(function(e) {
				e.preventDefault();
				$('#charity-create-album').slideToggle("slow", function() {});
			});
			
			// Calculate total price of items in basket
			$.charities.totalPrice();
			
			// Photo uploading handlers
			$('#charity-upload-errors').hide();
			$('#charity-upload-success').hide();
			$('#charity-submit').click(function(e) {
				e.preventDefault();
				var markText = '';
				
				var fileList = document.getElementById('charity-upload').files;
				var fileSize = fileList.length;
				if(fileSize > 0) {
					$('#charity-submit').before('<div id="progress-bar"></div>').remove();
					$("#progress-bar" ).progressbar({
						max: fileSize,
						value: 0,
						complete: function(event, ui) {
							$("#progress-bar").after('<h2>Upload Complete</h2>').remove();
						}
					});
					for (var i=0; i<fileSize; i++) {
						var file = fileList[i];
						if (!file.type.match(/image.*/)) {
							// this file is not an image
							$('#charity-upload-errors').append('<p>The file ' + file.name + ' is not an image</p>');
							$('#charity-upload-errors').show();
						} else {
							// this file is an image, do upload
							$.charities.prepFile(file, i+1);
						}
					}
				} else {
					$.common.notify('Please select at least one file to upload');
				}
			});
			$('.watermark-image').click(function(e) {
				$(this).prev('input').prop('checked', true);
			});
			$('#charity-watermark-text').on('focus', function(e) {
				$(this).prev('input').prop('checked', true);
			});
			$('#charity-watermark-upload').on('click', function(e) {
				$(this).prev('input').prop('checked', true);
			});
			
			// Order handling
			$('.charity-update-printed').click(function() {
				var button = $(this);
				$.ajax({
					type: "POST",
					url: script_url + 'charities/update_status',
					data: {
						'order_id' : button.parent('div').find('.charity-order-id').text(),
						'status' : 'printed'
					},
					error: function() {
						$.common.notify('The order status could not be updated')
					},
					success: function() {
						button.parent('div').prev('h3').find('.charity-order-status').html('Printed');
					}
				});
			});
			$('.charity-update-paid').click(function() {
				var button = $(this);
				$.ajax({
					type: "POST",
					url: script_url + 'charities/update_status',
					data: {
						'order_id' : button.parent('div').find('.charity-order-id').text(),
						'status' : 'paid'
					},
					error: function() {
						$.common.notify('The order status could not be updated')
					},
					success: function() {
						button.parent('div').prev('h3').find('.charity-order-status').html('Paid');
					}
				});
			});
			$('.charity-update-delete').click(function() {
				var button = $(this);
				var cont = $('<div />').addClass('delete-conf-box');
				cont.html('Are you sure you want to delete that order?<br />');
				cont.dialog({
					resizable: false,
					draggable: false,
					modal: true,
					title: "Confirm Delete",
					buttons: {
						"Delete": function () {
							$.ajax({
								type: "POST",
								url: script_url + 'charities/delete_order/' + button.parent('div').find('.charity-order-id').text(),
								error: function() {
									$.common.notify('The order could not be deleted')
								},
								success: function() {
									button.parent('div').prev('h3').remove();
									button.parent('div').remove();
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
		},
		totalPrice: function() {
			if($('.charity-item-price').length > 0) {
				var total = 0;
				$('.charity-item-price').each(function(i,v) {
					var str = $(this).html();
					str = str.replace(/\u00A3/g,''); // unicode for pound symbol
					total += parseFloat(str);
				});
				$('#charity-total-price').html('Total: &pound;' + total.toFixed(2));
				$('#charity-basket-options').show();
			} else {
				$('#charity-total-price').html('Your shopping basket is empty');
				$('#charity-basket-options').hide();
			}
			
		},
		wrapText: function(context, text, x, y, maxWidth, lineHeight) {
			var words = text.split(' ');
			var line = '';
			var lines = [];

			for(var n = 0; n < words.length; n++) {
				var testLine = line + words[n] + ' ';
				var metrics = context.measureText(testLine);
				var testWidth = metrics.width;
				if (testWidth > maxWidth && n > 0) {
					lines.push(line);
					line = words[n] + ' ';
				} else {
					line = testLine;
				}
			}
			lines.push(line);
			y = y - (lines.length / 2) * lineHeight;
			if(y < lineHeight) {
				y = lineHeight;
			}
			for (var n = 0; n < lines.length; n++) {
				context.fillText(lines[n], x, y);
				y += lineHeight;
			}
		},
		prepFile: function(file, i) {
			var img = document.createElement("img");
			img.src = window.URL.createObjectURL(file);
			setTimeout(function() {
				var width = img.width;
				var height = img.height;
				
				if (width > height) {
					if (width > 500) {
						height *= 500 / width;
						width = 500;
					}
				} else {
					if (height > 500) {
						width *= 500 / height;
						height = 500;
					}
				}
				var canvas = document.createElement("canvas");
				canvas.width = width;
				canvas.height = height;
				var ctx = canvas.getContext("2d");
				ctx.drawImage(img, 0, 0, width, height);
				switch($('input[name=watermark-radio]:checked').val()) {
					case 'text':
						var markText = $('#charity-watermark-text').val();
						if(markText != '') {
							ctx.fillStyle = "#CCC";
							ctx.textAlign = 'center';
							ctx.font = "40px Georgia";
							$.charities.wrapText(ctx, markText, width/2, height/2, width - 50, 50);
						}
						$.charities.sendFile(canvas, file, i);
						break;
					case 'image' :
						var fileList = document.getElementById('charity-watermark-upload').files;
						var fileSize = fileList.length;
						if(fileSize == 1) {
							var waterMarkfile = fileList[0];
							if (!waterMarkfile.type.match(/image.*/)) {
								// this file is not an image
								$('#charity-upload-errors').append('<p>The watermark file is not an image</p>');
								$('#charity-upload-errors').show();
							} else {
								var waterMarkimg = document.createElement("img");
								waterMarkimg.src = window.URL.createObjectURL(waterMarkfile);
								setTimeout(function() {
									var w = waterMarkimg.width;
									var h = waterMarkimg.height;
									if (w > h) {
										if (w > 250) {
											h *= 250 / w;
											w = 250;
										}
									} else {
										if (h > 250) {
											w *= 250 / h;
											h = 250;
										}
									}
									ctx.drawImage(waterMarkimg, width/2 - w/2, height/2 - h/2, w, h);
									$.charities.sendFile(canvas, file, i);
								}, 1000);
							}
						}
						break;
					case 'butler-jcr':
						var waterMarkimg = document.getElementById("charity-watermark-butler-jcr-logo");
						ctx.drawImage(waterMarkimg, width/2 - 106, height/2 - 125);
						$.charities.sendFile(canvas, file, i);
						break;
					case 'grace-house':
						var waterMarkimg = document.getElementById("charity-watermark-grace-house-logo");
						ctx.drawImage(waterMarkimg, width/2 - 125, height/2 - 31);
						$.charities.sendFile(canvas, file, i);
						break;
				}
			}, 1000);
		},
		
		sendFile: function(canvas, file, i) {
			$.ajax({
				type: "POST",
				async: false,
				cache: false,
				url: script_url + 'charities/upload_image',
				data: {
					img: canvas.toDataURL("image/png"),
					album: $('#album-id').val()
				},
				error: function() {
					$('#charity-upload-errors').append('<p>Error uploading ' + file.name + '</p>');
					$('#charity-upload-errors').show();
					$("#progress-bar").progressbar( "option", "value", i);
				},
				success: function() {
					$('#charity-upload-success').append('<p>' + file.name + ' uploaded successfully</p>');
					$('#charity-upload-success').show();
					$("#progress-bar").progressbar( "option", "value", i);
				}
			});
		}
	};
})(jQuery);