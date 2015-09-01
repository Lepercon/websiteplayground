(function ($) {
	family = {
		init: function() {
			family.start();
			answer = new Audio('buzzer.mp3');
			wrong = new Audio('wrong.mp3');
			$(document).bind('keydown', function (event) {
				if(event.which == 32 || event.which == 13) {
					event.preventDefault();
					wrong.play();
				}
			});
		},
		start: function() {
			$("#result").html('<img src="crest.png"/>');
			dataTimer = setInterval(family.getData, 1000);
		},
		getData: function() {
			$.ajax({
				type: "GET",
				url: "check.php",
				data: { check : "true"}
			}).done(function(msg) {
				console.log(msg);
				if(msg.substring(0,1) == 'c'){
					$("#result").html('Team '+msg.substr(-1,1));
					answer.play();
					clearInterval(dataTimer);
					setTimeout(function() {
						family.start();
					}, 15000);
				}
			});
		}
	}
	$(document).ready(family.init);
})(jQuery);