(function($) {
	$.snake = {
		canvas: {},
		ctx: {},
		w: 0,
		h: 0,

		cw: 10, // cell width
		d: "right", // travel direction
		set: false,
		food: {},
		score: 0,
		paused: false,
		snake_array: [], // an array of cells to make up the snake

		initGame: function() {
			$.snake.d = "right"; // default direction
			$.snake.snake_array = []; // Empty array to start with
			for (var i = 4; i >= 0; i--) {
				// This will create a horizontal snake length 5 starting from the top left
				$.snake.snake_array.push({ x: i, y: 0 });
			}
			$.snake.createFood(); //Now we can see the food particle
			$.snake.score = 0;

			// Move the snake using a timer to trigger the paint function
			if (typeof $.snake.game_loop != "undefined") clearInterval($.snake.game_loop);
			$.snake.game_loop = setInterval($.snake.paint, 180);
		},
		
		clickHandle: function(direction) {
			$.snake.d = direction;
			$.snake.set = true;
		},

		createFood: function() {
			$.snake.food = {
				x: Math.round(Math.random() * ($.snake.w - $.snake.cw) / $.snake.cw),
				y: Math.round(Math.random() * ($.snake.h - $.snake.cw) / $.snake.cw)
			};
		},

		paint: function() {
			if (!($.snake.paused)) {
				$.snake.set = false;
				//Pop out the tail cell and place it infront of the head cell
				var nx = $.snake.snake_array[0].x;
				var ny = $.snake.snake_array[0].y;
				//These were the position of the head cell, increment it to get the new head position
				if ($.snake.d == "right") nx++;
				else if ($.snake.d == "left") nx--;
				else if ($.snake.d == "up") ny--;
				else if ($.snake.d == "down") ny++;

				//Restart if the snake hits the wall or if the head of the snake bumps into its body
				if (nx == -1 || nx == $.snake.w / $.snake.cw || ny == -1 || ny == $.snake.h / $.snake.cw || $.snake.checkCollision(nx, ny, $.snake.snake_array)) {
					if ($.snake.score > 2) {
						if (typeof $.snake.game_loop != "undefined") clearInterval($.snake.game_loop);
						$.game.scoreAdd('snake');
					} else {
						$.snake.initGame();
					}
					return;
				}

				$.game.clearCanvas('snake', '#fff');

				//If the new head position matches with that of the food, create a new head instead of moving the tail
				if (nx == $.snake.food.x && ny == $.snake.food.y) {
					var tail = { x: nx, y: ny };
					$.snake.score++;
					$.snake.createFood();
					if (typeof $.snake.game_loop != "undefined") clearInterval($.snake.game_loop);
					var speed = 180 - $.snake.score - $.snake.score;
					if (speed < 80) {
						speed = 80;
					}
					$.snake.game_loop = setInterval($.snake.paint, speed);
				} else {
					var tail = $.snake.snake_array.pop(); //pops out the last cell
					tail.x = nx; tail.y = ny;
				}

				$.snake.snake_array.unshift(tail); //puts back the tail as the first cell

				for (var i = 0; i < $.snake.snake_array.length; i++) {
					var c = $.snake.snake_array[i];
					$.snake.paintCell(c.x, c.y, false);
				}

				$.snake.paintCell($.snake.food.x, $.snake.food.y, true); //Paint the food
				$.snake.ctx.fillText("Score: " + $.snake.score, 5, $.snake.h - 5); //Paint the score
			}
		},

		scoreDone: function () {
			$.snake.initGame();
		},

		paintCell: function (x, y, paintfood) {
			if (paintfood == false) {
				$.snake.ctx.fillStyle = "#c80000";
			} else {
				$.snake.ctx.fillStyle = "#eeb300";
			}
			$.snake.ctx.fillRect(x * $.snake.cw, y * $.snake.cw, $.snake.cw, $.snake.cw);
			$.snake.ctx.strokeStyle = "#fff";
			$.snake.ctx.strokeRect(x * $.snake.cw, y * $.snake.cw, $.snake.cw, $.snake.cw);
			$.snake.ctx.fillStyle = "#c80000";
		},

		checkCollision: function(x, y, array) {
			// Check if the provided x,y coordinates exist in array
			for (var i = 0; i < array.length; i++) {
				if (array[i].x == x && array[i].y == y)
					return true;
			}
			return false;
		},
			
		init: function() {
			$.snake.canvas = document.getElementById("snake-canvas");
			$.snake.ctx = $.snake.canvas.getContext("2d");
			$.snake.h = $.snake.canvas.height;
			$.snake.w = $.snake.canvas.width;
			$.snake.ctx.font="10px sans-serif";
			// Keyboard Control
			$(document).keydown(function (e) {
				var key = e.which;
				if ((key == "37" || key == "38" || key == "39" || key == "40" || key == "32") && $("#snake-canvas").length > 0) {
					e.preventDefault();
					if ($.snake.paused == false && $.snake.set == false) {
						if (key == "37" && $.snake.d != "right") {
							$.snake.clickHandle("left");
						}
						else if (key == "38" && $.snake.d != "down") {
							$.snake.clickHandle("up");
						}
						else if (key == "39" && $.snake.d != "left") {
							$.snake.clickHandle("right");
						}
						else if (key == "40" && $.snake.d != "up") {
							$.snake.clickHandle("down");
						}
					}
					if (key == "32") $.snake.paused = !$.snake.paused;
				}
			});
			$.snake.initGame();
		}
	};
})(jQuery);
