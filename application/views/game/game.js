(function($) {
	$.game = {
		init: function() {
			// Game inspiration - Mike Hudson
			// Programming - Courtney Edgar
			$.each(['snake', 'brick', 'missile', 'defend'], function(i,v) {
				if($('#'+v+'-canvas').length > 0) {
					var canvas = document.getElementById(v+"-canvas");
					var ctx = canvas.getContext("2d");
					ctx.font="20px sans-serif";
					ctx.fillText("Loading",(canvas.width)/2 - 40, (canvas.height)/2 - 10);
					$.game.getScript(v);
				}
			});
		},
		getScript: function(name) {
			var url = view_url + 'game/' + name + '.js?v=' + scriptVersion;
			if($.inArray(url, $.common.scripts) == -1) {
				// get the script
				$.common.get_script(url, function() {
					// push the script onto the loaded scripts array
					$.common.scripts.push(url);
					// initialise the script
					$[name].init();
				});
			} else {
				// initialise the script
				$[name].init();
			}
		},
		clearCanvas: function (name, fillStyle) {
			//Paint the background with fillStyle with black border
			$[name].ctx.fillStyle = fillStyle;
			$[name].ctx.fillRect(0, 0, $[name].w, $[name].h);
			$[name].ctx.strokeStyle = "#000";
			$[name].ctx.strokeRect(0, 0, $[name].w, $[name].h);
		},
		getMousePos: function(name, event) {
			var rect = $[name].canvas.getBoundingClientRect();
			return {
				x: event.clientX - rect.left,
				y: event.clientY - rect.top
			};
		},
		scoreAdd: function(name) {
			$.ajax({
				url: script_url + 'game/submit_score',
				cache: false,
				type: 'POST',
				data: {
					score: $[name].score,
					game: name
				},
				dataType: 'json',
				error: function(xhr) {
					$.common.notify('Your score could not be submitted. If it is a highscore, take a screenshot.');
				},
				success: function() {
					$("#leaderboard").prepend("<li>" + $("#gameusername").html() + ": " + $[name].score + "</li>");
					$[name].scoreDone()
				}
			});
		}
	};
})(jQuery);