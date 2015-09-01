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
				$("#result").html("Judge "+$('#team-no').html()+" - You Buzzed");
				$.ajax({
					type: "POST",
					url: "update.php",
					data: {id:$('#team-no').html()}
				});
				setTimeout(function() {
					$("#result").html("Judge "+$('#team-no').html()+" - Click to Buzz");
					allow = true;
				}, 5000);
			}
		}
	}
	$(document).ready(family.init);
})(jQuery);