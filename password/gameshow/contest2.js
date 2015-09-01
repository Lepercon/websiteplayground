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
				$("#result").html("Team 2 - You Buzzed");
				$.ajax({
					type: "POST",
					url: "update.php",
					data: {id:"2"}
				});
				setTimeout(function() {
					$("#result").html("Team 2 - Click to Buzz");
					allow = true;
				}, 5000);
			}
		}
	}
	$(document).ready(family.init);
})(jQuery);