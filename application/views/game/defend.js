(function($) {
	$.defend = {
		canvas: {},
		ctx: {},
		w: 0,
		h: 0,
		brickWidth: 0,
		brickHeight: 12,
		nRows: 5,
		nCols: 8,
		rightDown: false,
		leftDown: false,
		paused: false,
		radius: 6,
		
		level: 1,
		score: 0,
		speed: 20,
		paddleX: 0,
		x: 25,
		y: 500,
		dX: 1.5,
		dY: -4,
		bricks: [],
		team: -1,
		teams: [
			["Brazil", "#FCE501", "#00208D"],
			["Croatia", "#860A24", "#FFFFFF"],
			["Mexico", "#049A6A", "#FB4224"],
			["Cameroon", "#117458", "#D0BB46"],
			["Spain", "#9F0124", "#B59974"],
			["Netherlands", "#FC6401", "#1D50A9"],
			["Chile", "#BE2225", "#2C3367"],
			["Australia", "#FCE501", "#012E05"],
			["Colombia", "#AB0B23", "#091223"],
			["Greece", "#FFFFFF", "#03479A"],
			["Ivory Coast", "#D49B34", "#309681"],
			["Japan", "#BFE446", "#172661"],
			["Uruguay", "#7293BE", "#FFFFFF"],
			["Costa Rica", "#C90C20", "#223C86"],
			["England", "#FFFFFF", "#C80000"],
			["Italy", "#325AB9", "#FFFFFF"],
			["Switzerland", "#B2161A", "#FFFFFF"],
			["Ecuador", "#00208D", "#FCE501"],
			["France", "#FFFFFF", "#3F4775"],
			["Honduras", "#FFFFFF", "#197BBC"],
			["Argentina", "#87BBE0", "#FFFFFF"],
			["Bosnia-Herzegovina", "#153999", "#FFFFFF"],
			["Iran", "#FFFFFF", "#B61914"],
			["Nigeria", "#76BE5B", "#FFFFFF"],
			["Germany", "#FFFFFF", "#000000"],
			["Portugal", "#FFFFFF", "#E86256"],
			["Ghana", "#FFFFFF", "#9B1222"],
			["United States", "#AE172A", "#233B81"],
			["Belgium", "#000000", "#CF3C46"],
			["Algeria", "#FFFFFF", "#8CB684"],
			["Russia", "#9A1D2B", "#032C64"],
			["South Korea", "#D82E2F", "#2C3A77"],
		],
		init: function() {
			$.defend.canvas = document.getElementById("defend-canvas");
			$.defend.ctx = $.defend.canvas.getContext("2d");
			$.defend.h = $.defend.canvas.height;
			$.defend.w = $.defend.canvas.width;
			$.defend.brickWidth = $.defend.w / $.defend.nCols;
			$.defend.speed = 20;
			if (typeof $.defend.game_loop != "undefined") clearInterval($.defend.game_loop);
			$.defend.paused = false;
			
			if($.defend.team < 0) {
				$.defend.startMessage("Choose your opponent");
				$.defend.canvas.addEventListener('click', function(event) {
					if($.defend.team < 0) {
						var mousePos = $.game.getMousePos('defend', event);
						if(mousePos.y > 29) {
							$.defend.team = 0;
							if(mousePos.x < $.defend.w / 2) {
								$.defend.team = Math.floor((mousePos.y - 29)/29);
							} else {
								$.defend.team = 16 + Math.floor((mousePos.y - 29)/29);
							}
							$.defend.initKeyboard();
						}
					}
				}, false);
			} else {
				$.defend.initKeyboard();
			}
		},
		initKeyboard: function() {
			$(document).keydown(function (e) {
				var key = e.keyCode;
				if($("#defend-canvas").length > 0 && (key == 39 || key == 37 || key == 32)) {
					e.preventDefault();
					if (key == 39) $.defend.rightDown = true;
					else if (key == 37) $.defend.leftDown = true;
					else if (key == 32) $.defend.paused = !$.defend.paused;
				}
			});
			$(document).keyup(function (e) {
				var key = e.keyCode;
				if($("#defend-canvas").length > 0 && (key == 39 || key == 37)) {
					e.preventDefault();
					if (key == 39) $.defend.rightDown = false;
					else if (key == 37) $.defend.leftDown = false;
				}
			});
			$.defend.initGame();
		},
		initGame: function() {
			if (typeof $.defend.game_loop != "undefined") clearInterval($.defend.game_loop);
			$.defend.paddleX = ($.defend.w - 75) / 2;
			$.defend.x = 25;
			$.defend.y = 500;
			$.defend.dX = 1.5;
			$.defend.dY = -4;
			$.defend.initBricks();
			$.defend.paused = false;
			$.defend.game_loop = setInterval($.defend.draw, $.defend.speed);
		},
		initBricks: function() {
			var drawAlternate = true
			$.defend.bricks = new Array($.defend.nRows);
			for (var i=0; i < $.defend.nRows; i++) {
				$.defend.bricks[i] = new Array($.defend.nCols);
				for (var j=0; j < $.defend.nCols; j++) {
					if(drawAlternate) {
						$.defend.bricks[i][j] = 1;
					} else {
						$.defend.bricks[i][j] = 0;
					}
					drawAlternate = !drawAlternate;
				}
				drawAlternate = !drawAlternate;
			}
		},
		startMessage: function(title) {
			// Draw the pitch
			$.defend.drawPitch();
			// Add a text-safe overlay
			$.defend.ctx.fillStyle="rgba(100,100,100,0.9)";
			$.defend.ctx.rect(0,0,$.defend.w,$.defend.h);
			$.defend.ctx.fill();
			
			$.defend.ctx.font="18px sans-serif";
			$.defend.ctx.textAlign = "center";
			$.defend.ctx.fillStyle="#ffffff";
			$.defend.ctx.fillText(title, $.defend.w / 2, 29);
			$.defend.ctx.font="14px sans-serif";
			for(var i = 0; i < $.defend.teams.length / 2; i++) {
				$.defend.ctx.fillText($.defend.teams[i][0], $.defend.w / 4, (i+2) * 29);
			}
			for(var i = 0; i < $.defend.teams.length / 2; i++) {
				$.defend.ctx.fillText($.defend.teams[i + $.defend.teams.length / 2][0], 3 * $.defend.w / 4, (i+2) * 29);
			}
		},
		draw: function() {
			if(!$.defend.paused) {
				$.defend.drawPitch();
				
				// Draw paddle
				if ($.defend.rightDown) $.defend.paddleX += 5;
				else if ($.defend.leftDown) $.defend.paddleX -= 5;
				$.defend.ctx.fillStyle = "#C80000";
				$.defend.rect($.defend.paddleX, $.defend.h-10, 75, 10);
				
				$.defend.drawBricks();
				
				var rowheight = $.defend.brickHeight + 1;
				var colwidth = $.defend.brickWidth + 1;
				var row = Math.floor(($.defend.y - 20)/(rowheight * 2.5));
				var col = Math.floor($.defend.x/colwidth);
				
				if (row >= 0 && col >= 0 && row < $.defend.nRows && $.defend.y < (rowheight + row * rowheight * 2.5 + 20) && $.defend.bricks[row][col] == 1) {
					// Hit brick
					$.defend.bricks[row][col] = 0;
					$.defend.score++;
					$.defend.dY = -1 * $.defend.dY;
				}
				
				// Hit side edge
				if ($.defend.x + $.defend.dX + $.defend.radius > $.defend.w || $.defend.x + $.defend.dX - $.defend.radius < 0) {
					$.defend.dX = -1 * $.defend.dX;
				}
				
				if ($.defend.y + $.defend.dY - $.defend.radius < 0) {
					if($.defend.x > ($.defend.w / 2) - 25 && $.defend.x < ($.defend.w / 2) + 25) {
						$.defend.score += 5;
						$.defend.levelUp();
					} else {
						$.defend.dY = -1 * $.defend.dY;
					}
					// Hit top edge
				} else if ($.defend.y + $.defend.dY + $.defend.radius > $.defend.h - 10) {
					if ($.defend.x > $.defend.paddleX && $.defend.x < $.defend.paddleX + 75) {
						// Hit paddle, move the ball differently based on where it hit the paddle
						$.defend.dX = 8 * (($.defend.x-($.defend.paddleX + 75/2))/75);
						$.defend.dY = -1 * $.defend.dY;
					} else if ($.defend.y + $.defend.dY + $.defend.radius > $.defend.h) {
						// hit bottom of screen
						if (typeof $.defend.game_loop != "undefined") clearInterval($.defend.game_loop);
						if($.defend.score > 3) {
							$.game.scoreAdd('defend');
						} else {
							$.defend.scoreDone();
						}
					}
				}
				
				$.defend.x += $.defend.dX;
				$.defend.y += $.defend.dY;
				
				$.defend.ctx.fillStyle = "#fff";
				$.defend.ctx.font="10px sans-serif";
				$.defend.ctx.textAlign = "left";
				$.defend.ctx.fillText("Level: " + $.defend.level + " Score: " + $.defend.score, 20, 15); //Paint the score
				$.defend.ctx.textAlign = "right";
				$.defend.ctx.fillText($.defend.teams[$.defend.team][0], $.defend.w - 20, 15);
			}
		},
		drawBricks: function() {
			$.defend.ctx.lineWidth = 1;
			var noBricks = true
			for (var i=0; i < $.defend.nRows; i++) {
				for (var j=0; j < $.defend.nCols; j++) {
					if ($.defend.bricks[i][j] == 1) {
						for(var k = 0; k < $.defend.brickWidth/4; k++) {
							if(Math.pow(-1, k) > 0) {
								$.defend.ctx.fillStyle = $.defend.teams[$.defend.team][1];
							} else {
								$.defend.ctx.fillStyle = $.defend.teams[$.defend.team][2];
							}
							$.defend.rect((j * ($.defend.brickWidth + 1)) + 1 + k*4, (2.5 * i * ($.defend.brickHeight + 1)) + 21, 4, $.defend.brickHeight);
							noBricks = false;
						}
					}
				}
			}
			if(noBricks) {
				$.defend.needScore();
			}
		},
		needScore: function() {
			$.defend.ctx.fillStyle = "#fff";
			$.defend.ctx.font="14px sans-serif";
			$.defend.ctx.textAlign = "center";
			$.defend.ctx.fillText("Score a goal", $.defend.w / 2, 70);
			
			// arrow
			$.defend.ctx.strokeStyle = "#fff";
			$.defend.ctx.lineWidth = 3;
			$.defend.ctx.beginPath();
			$.defend.ctx.moveTo($.defend.w / 2 - 10, 40);
			$.defend.ctx.lineTo($.defend.w / 2, 30);
			$.defend.ctx.lineTo($.defend.w / 2 + 10, 40);
			$.defend.ctx.stroke();
			$.defend.ctx.beginPath();
			$.defend.ctx.moveTo($.defend.w / 2, 30);
			$.defend.ctx.lineTo($.defend.w / 2, 50);
			$.defend.ctx.stroke();
		},
		levelUp: function() {
			$.defend.level++;
			if($.defend.speed > 6) {
				$.defend.speed = $.defend.speed - 2;
			}
			if($.defend.nRows < 10) {
				$.defend.nRows++;
			}
			$.defend.initGame();
		},
		scoreDone: function() {
			$.defend.score = 0;
			$.defend.level = 1;
			$.defend.speed = 20;
			$.defend.nRows = 5;
			$.defend.initGame();
		},
		rect: function(x,y,w,h) {
			$.defend.ctx.beginPath();
			$.defend.ctx.rect(x,y,w,h);
			$.defend.ctx.closePath();
			$.defend.ctx.fill();
		},
		drawPitch: function() {
			// Draw pitch background
			$.defend.ctx.fillStyle = "#006600";
			$.defend.ctx.fillRect(0, 0, $.defend.w, $.defend.h);
			
			// Draw sidelines
			$.defend.ctx.strokeStyle = "#fff";
			$.defend.ctx.lineWidth = 5;
			$.defend.ctx.beginPath();
			$.defend.ctx.moveTo(20, $.defend.h);
			$.defend.ctx.lineTo(20, 20);
			$.defend.ctx.lineTo($.defend.w - 20, 20);
			$.defend.ctx.lineTo($.defend.w - 20, $.defend.h);
			$.defend.ctx.stroke();
			
			// Draw centre (semi) circle
			$.defend.ctx.beginPath();
			$.defend.ctx.arc($.defend.w / 2, $.defend.h, $.defend.w/6, 0, Math.PI, true);
			$.defend.ctx.stroke();
			
			// Draw penalty box
			$.defend.ctx.rect(($.defend.w / 2) - 80, 20, 160, 60);
			$.defend.ctx.stroke();
			
			// Draw outer area
			$.defend.ctx.rect(($.defend.w / 2) - 160, 20, 320, 120);
			$.defend.ctx.stroke();
			
			//Draw corner flags
			$.defend.ctx.beginPath();
			$.defend.ctx.arc(20, 20, 20, 0, Math.PI / 2, false);
			$.defend.ctx.stroke();
			
			$.defend.ctx.beginPath();
			$.defend.ctx.arc($.defend.w - 20, 20, 20, Math.PI / 2, Math.PI, false);
			$.defend.ctx.stroke();
			
			// Draw goalmouth
			$.defend.ctx.beginPath();
			$.defend.ctx.moveTo(($.defend.w / 2) - 25, 20);
			$.defend.ctx.lineTo(($.defend.w / 2) - 25, 0);
			$.defend.ctx.stroke();
			$.defend.ctx.beginPath();
			$.defend.ctx.moveTo(($.defend.w / 2) + 25, 20);
			$.defend.ctx.lineTo(($.defend.w / 2) + 25, 0);
			$.defend.ctx.stroke();
			
			// Draw football
			$.defend.ctx.fillStyle = "#000";
			$.defend.ctx.beginPath();
			$.defend.ctx.arc($.defend.x, $.defend.y, $.defend.radius, 0, Math.PI / 2, false);
			$.defend.ctx.lineTo($.defend.x, $.defend.y);
			$.defend.ctx.closePath();
			$.defend.ctx.fill();
			$.defend.ctx.beginPath();
			$.defend.ctx.arc($.defend.x, $.defend.y, $.defend.radius, Math.PI, 3 * Math.PI / 2, false);
			$.defend.ctx.lineTo($.defend.x, $.defend.y);
			$.defend.ctx.closePath();
			$.defend.ctx.fill();
			
			$.defend.ctx.fillStyle = "#FFF";
			$.defend.ctx.beginPath();
			$.defend.ctx.arc($.defend.x, $.defend.y, $.defend.radius, Math.PI / 2, Math.PI, false);
			$.defend.ctx.lineTo($.defend.x, $.defend.y);
			$.defend.ctx.closePath();
			$.defend.ctx.fill();
			$.defend.ctx.beginPath();
			$.defend.ctx.arc($.defend.x, $.defend.y, $.defend.radius, 3 * Math.PI / 2, 2 * Math.PI, false);
			$.defend.ctx.lineTo($.defend.x, $.defend.y);
			$.defend.ctx.closePath();
			$.defend.ctx.fill();
		}
	};
})(jQuery);
