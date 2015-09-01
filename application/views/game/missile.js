(function($) {
	$.missile = {
		canvas: {},
		ctx: {},
		w: 0,
		h: 0,
		cities: [],
		missiles: [],
		bombs: [],
		planes: [],
		bombCount: 0,
		score: 0,
		level: 1,
		bubble: 0,
		paused: false,
		init: function() {
			$.missile.canvas = document.getElementById("missile-canvas");
			$.missile.ctx = $.missile.canvas.getContext("2d");
			$.missile.h = $.missile.canvas.height;
			$.missile.w = $.missile.canvas.width;
			$.missile.ctx.font="10px sans-serif";
			$.missile.initGame();
			$.missile.canvas.addEventListener('click', function(event) {
				if(!$.missile.paused) {
					var mousePos = $.game.getMousePos('missile', event);
					var distances = [];
					var indices = [];
					for(var i = 0; i < $.missile.cities.length; i++) {
						// If is an existing launcher with capacity
						if($.missile.cities[i][4] && $.missile.cities[i][3] && $.missile.cities[i][5] > 0) {
							distances.push(Math.pow(mousePos.x - $.missile.cities[i][0], 2) + Math.pow(mousePos.y - $.missile.cities[i][1], 2));
							indices.push(i);
						}
					}
					if(distances.length > 0) {
						var origin = distances.indexOf(Math.min.apply(Math, distances));
						if(mousePos.y < $.missile.cities[indices[origin]][1] - 20) {
							$.missile.launchMissile($.missile.cities[indices[origin]][0], $.missile.cities[indices[origin]][1], mousePos.x, mousePos.y);
							$.missile.cities[indices[origin]][5] = $.missile.cities[indices[origin]][5] - 1;
						}
					}
				}
			}, false);
			$(document).keydown(function (e) {
				var key = e.keyCode;
				if($("#missile-canvas").length > 0 && key == 32) {
					e.preventDefault();
					$.missile.paused = !$.missile.paused;
				}
			});
		},
		initGame: function() {
			if (typeof $.missile.game_loop != "undefined") clearInterval($.missile.game_loop);
			$.missile.cities = [
				[50, 455, "The Bar", true, true, 10],
				[100, 455, "Kirknewton", false, true],
				[150, 455, "Dilston", false, true],
				[200, 455, "Milfield", false, true],
				[250, 455, "The Mound", true, true, 10],
				[300, 455, "Klute", false, true],
				[350, 455, "Loveshack", false, true],
				[400, 455, "Loft", false, true],
				[450, 455, "Howlands", true, true, 10]
			];
			$.missile.game_loop = setInterval($.missile.draw, 20); // 50 fps
		},
		launchMissile: function(launchX, launchY, targetX, targetY) {
			var distance = Math.sqrt(Math.pow(targetX - launchX, 2) + Math.pow(targetY - launchY, 2));
			var angle = Math.atan2((targetY - launchY),(targetX - launchX));
			var dX = 4 * Math.cos(angle);
			var dY = 4 * Math.sin(angle);
			$.missile.missiles.push([launchX, launchY, targetX, targetY, dX, dY]);
		},
		dropBomb: function(dropX, dropY) {
			var indices = [];
			for(var i = 0; i < $.missile.cities.length; i++) {
				if($.missile.cities[i][4]) {
					indices.push(i);
				}
			}
			if(indices.length > 0) {
				var random = indices[Math.floor(Math.random() * indices.length)];
				var angle = Math.atan2(($.missile.cities[random][1] - dropY), ($.missile.cities[random][0] - dropX));
				var dX = Math.cos(angle);
				var dY = Math.sin(angle);
				$.missile.bombs.push([dropX, dropY, $.missile.cities[random][0], $.missile.cities[random][1], dX, dY]);
				$.missile.bombCount++;
			}
		},
		draw: function() {
			if(!$.missile.paused) {
				if($.missile.bombs.length == 0 && $.missile.bombCount == 20) {
					$.missile.level++;
					$.missile.bombCount = 0;
					for(var i = 0; i < $.missile.cities.length; i++) {
						if($.missile.cities[i][3]) {
							$.missile.cities[i][5] = 10;
						}
					}
				}
				$.game.clearCanvas('missile', "#fff");
				if($.missile.bubble > 0) {
					$.missile.ctx.fillStyle = "#CCFFFF";
					$.missile.ctx.beginPath();
					$.missile.ctx.arc($.missile.w / 2, $.missile.h - 40, $.missile.w / 2, 0, Math.PI, true);
					$.missile.ctx.fill();
					$.missile.bubble = $.missile.bubble - 1;
				}
				$.missile.drawGround();
				for(var i = 0; i < $.missile.cities.length; i++) {
					if($.missile.cities[i][4]) {
						if($.missile.cities[i][3]) {
							$.missile.drawLauncher(i);
						} else {
							$.missile.drawCity(i);
						}
					}
				}
				
				var toSpliceI = [];
				var toSpliceJ = [];
				var toSpliceK = [];
				for(var i=0; i < $.missile.missiles.length; i++) {
					if($.missile.missiles[i][1] > $.missile.missiles[i][3]) {
						$.missile.ctx.fillStyle = "#C80000";
						$.missile.ctx.beginPath();
						$.missile.ctx.arc($.missile.missiles[i][0], $.missile.missiles[i][1], 2, 0, 2*Math.PI, true);
						$.missile.ctx.fill();
						// increment x and y location by dx and dy
						$.missile.missiles[i][0] = $.missile.missiles[i][0] + $.missile.missiles[i][4];
						$.missile.missiles[i][1] = $.missile.missiles[i][1] + $.missile.missiles[i][5];
					} else {
						for(var j = 0; j < $.missile.bombs.length; j++) {
							if(Math.abs($.missile.bombs[j][0] - $.missile.missiles[i][0]) < 32 - (2 * $.missile.level) && Math.abs($.missile.bombs[j][1] - $.missile.missiles[i][1]) < 32 - (2 * $.missile.level)) {
								toSpliceJ.push(j);
							}
						}
						for(var j = 0; j < $.missile.planes.length; j++) {
							if(Math.abs($.missile.planes[j][1] - $.missile.missiles[i][0]) < 42 - (2 * $.missile.level) && Math.abs($.missile.planes[j][0] - $.missile.missiles[i][1]) < 42 - (2 * $.missile.level)) {
								toSpliceK.push(j);
								$.missile.bubble = 200;
							}
						}
						$.missile.ctx.fillStyle = "#EEB300";
						$.missile.ctx.beginPath();
						$.missile.ctx.arc($.missile.missiles[i][0], $.missile.missiles[i][1], 32 - (2 * $.missile.level), 0, 2*Math.PI, true);
						$.missile.ctx.fill();
						toSpliceI.push(i);
					}
				}
				if(toSpliceJ.length > 0) {
					$.missile.bombs = $.grep($.missile.bombs, function(n, i) {
						return ($.inArray(i, toSpliceJ) == -1);
					});
					$.missile.score++;
				}
				if(toSpliceI.length > 0) {
					$.missile.missiles = $.grep($.missile.missiles, function(n, i) {
						return ($.inArray(i, toSpliceI) == -1);
					});
				}
				if(toSpliceK.length > 0) {
					$.missile.planes = $.grep($.missile.planes, function(n, i) {
						return ($.inArray(i, toSpliceK) == -1);
					});
					$.missile.score++;
				}
				
				// conventional bomb drop
				if(Math.random() > 0.992 && $.missile.bombCount < 20) {
					$.missile.dropBomb(Math.random() * $.missile.w, 0);
				}
				
				var toSplice = [];
				for(var i = 0; i < $.missile.bombs.length; i++) {
					if($.missile.bombs[i][1] < $.missile.bombs[i][3]) {
						$.missile.ctx.strokeStyle = "#EEB300";
						$.missile.ctx.beginPath();
						$.missile.ctx.moveTo($.missile.bombs[i][0], $.missile.bombs[i][1]);
						$.missile.ctx.lineTo($.missile.bombs[i][0] - 20 * $.missile.bombs[i][4], $.missile.bombs[i][1] - 20 * $.missile.bombs[i][5]);
						$.missile.ctx.lineWidth = 2;
						$.missile.ctx.stroke();
						// increment x and y location by dx and dy
						$.missile.bombs[i][0] = $.missile.bombs[i][0] + $.missile.bombs[i][4];
						$.missile.bombs[i][1] = $.missile.bombs[i][1] + $.missile.bombs[i][5];
					} else {
						// detonate bomb and destroy city
						for(var j = 0; j < $.missile.cities.length; j++) {
							if($.missile.bombs[i][2] == $.missile.cities[j][0] && $.missile.bombs[i][3] == $.missile.cities[j][1]) {
								if($.missile.bubble == 0) {
									$.missile.cities[j][4] = false;
								}
								toSplice.push(i);
							}
						}
					}
				}
				if(toSplice.length > 0) {
					$.missile.bombs = $.grep($.missile.bombs, function(n, i) {
						return ($.inArray(i, toSplice) == -1);
					});
				}
				
				// introduce planes to drop bombs
				if(Math.random() > 0.998 && $.missile.bombCount < 20) {
					$.missile.addPlane(Math.random() * ($.missile.h)/2);
				}
				
				var toSplice = [];
				for(var i = 0; i < $.missile.planes.length; i++) {
					// planes: Y, X, Direction, Drop location
					if($.missile.planes[i][1] >= 0 && $.missile.planes[i][1] <= $.missile.w) {
						$.missile.ctx.fillStyle="#C80000";
						$.missile.ctx.beginPath();
						$.missile.ctx.moveTo($.missile.planes[i][1] + $.missile.planes[i][2] * 20, $.missile.planes[i][0] + 4);
						$.missile.ctx.lineTo($.missile.planes[i][1] + $.missile.planes[i][2] * 10, $.missile.planes[i][0] - 4);
						$.missile.ctx.lineTo($.missile.planes[i][1] - $.missile.planes[i][2] * 10, $.missile.planes[i][0] - 4);
						$.missile.ctx.lineTo($.missile.planes[i][1] - $.missile.planes[i][2] * 20, $.missile.planes[i][0] - 12);
						$.missile.ctx.lineTo($.missile.planes[i][1] - $.missile.planes[i][2] * 10, $.missile.planes[i][0] + 4);
						$.missile.ctx.closePath();
						$.missile.ctx.fill();
						$.missile.planes[i][1] = $.missile.planes[i][1] + $.missile.planes[i][2];
						if($.missile.planes[i][1] == $.missile.planes[i][3] && $.missile.bombCount < 20) {
							$.missile.dropBomb($.missile.planes[i][1], $.missile.planes[i][0]);
						}
					} else {
						toSplice.push(i);
					}
				}
				if(toSplice.length > 0) {
					$.missile.planes = $.grep($.missile.planes, function(n, i) {
						return ($.inArray(i, toSplice) == -1);
					});
				}
				
				$.missile.ctx.textAlign = "left";
				$.missile.ctx.fillStyle = "#C80000";
				$.missile.ctx.fillText("Level: " + $.missile.level + "  Score: " + $.missile.score + "  Remaining this wave: " + (20 - $.missile.bombCount), 5, $.missile.h - 5);
				
				var launchersLeft = 0;
				for(var i = 0; i < $.missile.cities.length; i++) {
					if($.missile.cities[i][4] && $.missile.cities[i][3]) {
						launchersLeft++;
					}
				}
				if(launchersLeft == 0) {
					if (typeof $.missile.game_loop != "undefined") clearInterval($.missile.game_loop);
					if($.missile.score > 2) {
						$.game.scoreAdd('missile');
					} else {
						$.missile.scoreDone();
					}
				}
			}
		},
		addPlane: function(levelY) {
			if(Math.random() > 0.5) {
				var startX = 0;
				var direction = 1;
			} else {
				var startX = $.missile.w;
				var direction = -1;
			}
			var dropLocation = Math.round(Math.random() * $.missile.w);
			$.missile.planes.push([levelY, startX, direction, dropLocation]);
		},
		scoreDone: function() {
			$.missile.level = 1;
			$.missile.score = 0;
			$.missile.bombCount = 0;
			$.missile.bombs = [];
			$.missile.missiles = [];
			$.missile.planes = [];
			$.missile.initGame();
		},
		drawCity: function(city) {
			var centreX = $.missile.cities[city][0];
			var centreY = $.missile.cities[city][1];
			// Draw building with triangular roof
			$.missile.ctx.fillStyle = "#C80000";
			$.missile.ctx.beginPath();
			$.missile.ctx.moveTo(centreX - 10, centreY - 10);
			$.missile.ctx.lineTo(centreX, centreY - 16);
			$.missile.ctx.lineTo(centreX + 10, centreY - 10);
			$.missile.ctx.lineTo(centreX + 10, centreY + 10);
			$.missile.ctx.lineTo(centreX - 10, centreY + 10);
			$.missile.ctx.closePath();
			$.missile.ctx.fill();
			
			// Write building name underneath
			$.missile.ctx.font="10px sans-serif";
			$.missile.ctx.textAlign = "center";
			$.missile.ctx.fillText($.missile.cities[city][2], centreX, centreY + 20);
			
			// Draw windows
			$.missile.ctx.fillStyle = "#EEB300";
			$.missile.ctx.fillRect(centreX - 7, centreY - 8, 4, 4);
			$.missile.ctx.fillRect(centreX - 7, centreY + 2, 4, 4);
			$.missile.ctx.fillRect(centreX + 3, centreY - 8, 4, 4);
			$.missile.ctx.fillRect(centreX + 3, centreY + 2, 4, 4);
		},
		drawLauncher: function(city) {
			var centreX = $.missile.cities[city][0];
			var centreY = $.missile.cities[city][1];
			$.missile.ctx.fillStyle = "#C80000";
			$.missile.ctx.beginPath();
			$.missile.ctx.arc(centreX, centreY + 10, 20, 0, Math.PI, true);
			$.missile.ctx.closePath();
			$.missile.ctx.fill();
			
			// Write building name underneath
			$.missile.ctx.font="10px sans-serif";
			$.missile.ctx.textAlign = "center";
			$.missile.ctx.fillText($.missile.cities[city][2], centreX, centreY + 20);
			$.missile.ctx.fillText($.missile.cities[city][5], centreX, centreY + 30);
		},
		drawGround: function() {
			$.missile.ctx.fillStyle = "#EEB300";
			$.missile.ctx.fillRect(0, $.missile.h - 40, $.missile.w, 40);
		}
	};
})(jQuery);
