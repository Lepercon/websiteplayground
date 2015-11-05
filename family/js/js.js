$(function () {
	$("#js-warning").remove();
	$("#helpContent").hide();
	if ($('html').is('.ie6, .ie7, .ie8, .ie9')) {
		$("#ms-warning").append('To view a larger, interactive version of this page, try using Google Chrome or Mozilla Firefox.');
	} else {
		$("#ms-warning").remove();
	}
	//activate search form
	$("input#submit").click(function () {
		search($("#surname").val());
		return false;
	});
	$("#help").click(function () {
		$("#helpContent").toggle(800);
		return false;
	});
	$("#random").click(function () {
		getDot(Math.floor(Math.random() * 1709) + 1);
		return false;
	});
});

// search database for surname
function search(surname) {
	$(document).ready(function () {
		$("#surname").val('');
		$("#tree").html('<div id="spinner"><img src="spinner.gif" alt="Loading Josephine Butler College Family Tree"/></div>');
		var request = $.ajax({
			type: "POST",
			url: './getDot.php',
			data: { surname: surname }
		});
		request.done(function (dot) {
			if (dot == 'nobody') {
				$("#tree").html("No college members found with surname \"" + surname + "\"");
				document.title = "Josephine Butler College Family Tree - Error";
			} else {
				showDot(dot);
				document.title = "Josephine Butler College Family Tree - " + surname;
			}
		});
		request.fail(function() {
			showFail();
		});
	});
}

// request tree information in dot format based on id
function  getDot(id) {
	$(document).ready(function () {
		$("#surname").val('');
		$("#tree").html('<div id="spinner"><img src="spinner.gif" alt="Loading Josephine Butler College Family Tree"/></div>');
		var request = $.ajax({
			type: "POST",
			url: './getDot.php',
			data: { id: id }
		});
		request.done(function (dot) {
			if (dot == 'nobody') {
				$("#tree").html("Nobody found with id: \"" + id + "\"");
			} else {
				showDot(dot);
				var request = $.ajax({
					type: "POST",
					url: './getName.php',
					data: { id: id }
				});
				request.done(function (name) {
					document.title = "Josephine Butler College Family Tree - " + name;
				});
				request.fail(function () {
					showFail();
				});
			}
		});
		request.fail(function () {
			showFail();
		});
	});
}

function showFail() {
	document.title = "Josephine Butler College Family Tree - Error";
	$("#tree").html("Request failed, please try again");
}

function showDot(dot) {
	if ($('html').is('.ie6, .ie7, .ie8, .ie9')) {
		$('#tree').html("<img src='https://chart.googleapis.com/chart?cht=gv&amp;chl="+dot+";chs=150x150'>");
	} else {
		$('#tree').html(dot);
		$('#tree').html(Viz(dot, 'svg'));
		fixSVG();
	}
}

function fixSVG() {	
	$('.graph').children('title').remove();							//remove graph title

	$('.node').attr('title', function(i, title) {
		$(this).data('title', $(this).children('title').text());	// save node title as some data
		$(this).children('title').remove();							// remove node title
	});

	$('.node').click(function(e){									// enable click on nodes
		var myarr = $(this).data('title').split('n');
		var num = myarr.length - 1;

		var posY = Math.floor($(this).position().top);				// click position
		var height = Math.floor($(this)[0].getBBox().height) + (myarr.length - 1)*7 + 2;
		var pos = (e.pageY - posY)/ height;

		getDot(myarr[Math.floor(pos * (myarr.length -1))+1]);		// get new tree from click position
	});
}