(function($) {
	$.brick = {
		canvas: {},
		ctx: {},
		w: 0,
		h: 0,
		brickWidth: 0,
		brickHeight: 12,
		nRows: 5,
		nCols: 5,
		rightDown: false,
		leftDown: false,
		paused: false,
		radius: 6,
		
		level: 1,
		score: 0,
		speed: 20,
		paddleX: 0,
		x: 25,
		y: 250,
		dX: 1.5,
		dY: -4,
		bricks: [],
		init: function() {
			$.brick.canvas = document.getElementById("brick-canvas");
			$.brick.ctx = $.brick.canvas.getContext("2d");
			$.brick.h = $.brick.canvas.height;
			$.brick.w = $.brick.canvas.width;
			$.brick.ctx.font="10px sans-serif";
			$.brick.brickWidth = $.brick.w / $.brick.nCols;
			
			$(document).keydown(function (e) {
				var key = e.keyCode;
				if($("#brick-canvas").length > 0 && (key == 39 || key == 37 || key == 32)) {
					e.preventDefault();
					if (key == 39) $.brick.rightDown = true;
					else if (key == 37) $.brick.leftDown = true;
					else if (key == 32) $.brick.paused = !$.brick.paused;
				}
			});
			$(document).keyup(function (e) {
				var key = e.keyCode;
				if($("#brick-canvas").length > 0 && (key == 39 || key == 37)) {
					e.preventDefault();
					if (key == 39) $.brick.rightDown = false;
					else if (key == 37) $.brick.leftDown = false;
				}
			});
			$.brick.initGame();
		},
		initGame: function() {
			if (typeof $.brick.game_loop != "undefined") clearInterval($.brick.game_loop);
			$.brick.paddleX = ($.brick.w - 75) / 2;
			$.brick.x = 25;
			$.brick.y = 250;
			$.brick.dX = 1.5;
			$.brick.dY = -4;
			$.brick.initBricks();
			$.brick.game_loop = setInterval($.brick.draw, $.brick.speed);
		},
		initBricks: function() {
			$.brick.bricks = new Array($.brick.nRows);
			for (i=0; i < $.brick.nRows; i++) {
				$.brick.bricks[i] = new Array($.brick.nCols);
				for (j=0; j < $.brick.nCols; j++) {
					$.brick.bricks[i][j] = 1;
				}
			}
		},
		draw: function() {
			if(!$.brick.paused) {
				$.game.clearCanvas('brick', '#fff');
				
				// Draw circle
				$.brick.ctx.fillStyle = "#800f25";
				$.brick.ctx.beginPath();
				$.brick.ctx.arc($.brick.x, $.brick.y, $.brick.radius, 0, Math.PI*2, true);
				$.brick.ctx.closePath();
				$.brick.ctx.fill();
				
				// Draw paddle
				if ($.brick.rightDown) $.brick.paddleX += 5;
				else if ($.brick.leftDown) $.brick.paddleX -= 5;
				$.brick.ctx.fillStyle = "#eeb300";
				$.brick.rect($.brick.paddleX, $.brick.h-10, 75, 10);
	
				$.brick.drawBricks();
	
				var rowheight = $.brick.brickHeight + 1;
				var colwidth = $.brick.brickWidth + 1;
				var row = Math.floor($.brick.y/rowheight);
				var col = Math.floor($.brick.x/colwidth);
				
				//reverse the ball and mark the brick as broken
				if ($.brick.y < $.brick.nRows * rowheight && row >= 0 && col >= 0 && $.brick.bricks[row][col] == 1) {
					$.brick.dY = -1 * $.brick.dY;
					$.brick.bricks[row][col] = 0;
					$.brick.score++;
				}
	
				if ($.brick.x + $.brick.dX + $.brick.radius > $.brick.w || $.brick.x + $.brick.dX - $.brick.radius < 0) {
					$.brick.dX = -1 * $.brick.dX;
				}
	
				if ($.brick.y + $.brick.dY - $.brick.radius < 0) {
					$.brick.dY = -1 * $.brick.dY;
				} else if ($.brick.y + $.brick.dY + $.brick.radius > $.brick.h - 10) {
					if ($.brick.x > $.brick.paddleX && $.brick.x < $.brick.paddleX + 75) {
						//move the ball differently based on where it hit the paddle
						$.brick.dX = 8 * (($.brick.x-($.brick.paddleX+75/2))/75);
						$.brick.dY = -1 * $.brick.dY;
					} else if ($.brick.y + $.brick.dY + $.brick.radius > $.brick.h) {
						if (typeof $.brick.game_loop != "undefined") clearInterval($.brick.game_loop);
						if($.brick.score > 2) {
							$.game.scoreAdd('brick');
						} else {
							$.brick.scoreDone();
						}
					}
				}
	
				$.brick.x += $.brick.dX;
				$.brick.y += $.brick.dY;
				
				$.brick.ctx.fillStyle = "#800f25";
				$.brick.ctx.fillText("Level: " + $.brick.level + " Score: " + $.brick.score, 5, $.brick.h - 5); //Paint the score
			}
		},
		scoreDone: function() {
			$.brick.score = 0;
			$.brick.level = 1;
			$.brick.speed = 20;
			$.brick.nRows = 5;
			$.brick.initGame();
		},
		drawBricks: function() {
			var levelUp = true;
			var colorArray = ["#EEB300", "#E99F00", "#E58B00", "#E17700", "#DD6300", "D84F00", "D43B00", "D02700", "CC1300", "C80000"];
			for (i=0; i < $.brick.nRows; i++) {
				$.brick.ctx.fillStyle = colorArray[i];
				for (j=0; j < $.brick.nCols; j++) {
					if ($.brick.bricks[i][j] == 1) {
						$.brick.rect((j * ($.brick.brickWidth + 1)) + 1, (i * ($.brick.brickHeight + 1)) + 1, $.brick.brickWidth, $.brick.brickHeight);
						levelUp = false;
					}
				}
			}
			if(levelUp) {
				$.brick.level++;
				if($.brick.speed > 6) {
					$.brick.speed = $.brick.speed - 2;
				}
				if($.brick.nRows < 10) {
					$.brick.nRows++;
				}
				$.brick.initGame();
			}
		},
		rect: function(x,y,w,h) {
			$.brick.ctx.beginPath();
			$.brick.ctx.rect(x,y,w,h);
			$.brick.ctx.closePath();
			$.brick.ctx.fill();
		}
	};
})(jQuery);
