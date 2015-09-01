(function ($) {
	family = {
		init: function() {
			$("body").on('click touchstart', function() {
				family.setData();
			});
			allow = true;
		},
		setData: function() {
			if(allow=true) {
				allow = false;
				$("#result").html("Team 1 - You Buzzed");
				$.ajax({
					type: "POST",
					url: "update.php",
					data: {id:"1"}
				});
				setTimeout(function() {
					$("#result").html("Team 1 - Click to Buzz");
					allow = true;
				}, 5000);
			}
		}
	}
	$(document).ready(family.init);
})(jQuery);