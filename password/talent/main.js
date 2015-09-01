(function ($) {
	family = {
		init: function() {
			family.start();
			buzz = new Audio('wrong.mp3');
			all = new Audio('buzzer.mp3');
			$(document).bind('keydown', function (event) {
				if(event.which == 32 || event.which == 13) {
					event.preventDefault();
					family.start();
				}
			});
		},
		start: function() {
			$(".cross-image").attr('src', 'blank.png');
			dataTimer = setInterval(family.getData, 500);
		},
		getData: function() {
			$.ajax({
				type: "GET",
				url: "check.php",
				data: { check : "true"}
			}).done(function(msg) {
				if(msg.substring(0,7) == 'contest'){
					if($("#image-"+msg.substr(-1,1)).attr('src') == 'blank.png'){
						$("#image-"+msg.substr(-1,1)).attr('src', 'cross.png');
						if($('img[src="blank.png"]').length == 1){
							buzz.play();
						}else{
							all.play();
						}
					}
				}
			});
		}
	}
	$(document).ready(family.init);
})(jQuery);