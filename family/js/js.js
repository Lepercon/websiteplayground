$(function () {
	$("#js-warning").remove();
	$("#helpContent").hide();
	if ($('html').is('.ie6, .ie7, .ie8, .ie9')) {
		$("#ms-warning").append('To view an interactive version of this page, use Google Chrome or Mozilla Firefox.');
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
		showSpinner();
		postXHR('./getDot.php', { surname: surname }).done(function(dot) {
			if (dot == 'nobody') {
				$("#tree").html("No college members found with surname \"" + surname + "\"");
				showTitle('Error');
			} else {
				showDot(dot);
				showTitle(surname);
			}
		});
	});
}

function postXHR(url, data) {
	return $.ajax({
		type: 'POST',
		url: url,
		data: data
	}).fail(function() {
		showTitle('Error');
		$("#tree").html("Request failed, please try again");
	});
}

function showSpinner() {
	$("#tree").html('<div id="spinner"><img src="spinner.gif" alt="Loading Josephine Butler College Family Tree"/></div>');
}

function showTitle(title) {
	document.title = "Josephine Butler College Family Tree - " + title;
}

// request tree information in dot format based on id
function  getDot(id) {
	$(document).ready(function () {
		$("#surname").val('');
		showSpinner();
		postXHR('./getDot.php', { id: id }).done(function (dot) {
			if (dot == 'nobody') {
				$("#tree").html("Nobody found with id: \"" + id + "\"");
			} else {
				showDot(dot);
				postXHR('./getName.php', { id: id }).done(function(name) {
					showTitle(name);
				});
			}
		});
	});
}

function showDot(dot) {
	if ($('html').is('.ie6, .ie7, .ie8, .ie9')) {
		$('#tree').html("<img src='https://chart.googleapis.com/chart?cht=gv&amp;chl="+dot+";chs=150x150'>");
	} else {
		$('#tree').html(Viz(dot, 'svg'));
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
}