(function ($) {
	$.signup = {
		init: function () {
			var spinner = $('#spinner').clone().attr('id', 'signup-spinner');
			spinner.append($('<div />').addClass('inline-block')).hide().insertBefore('#reservation-form');

			$('#reservation-form').submit($.signup.submit_func);

			if (typeof serverTimeArray !== 'undefined') {
				$.signup.serverTime();
			}

			$.signup.form_init();

			$('#refresh-tables').click(function (e) {
				e.preventDefault();
				$.signup.refresh_tables();
			});

			$('#signup-tables').on('click', '.signup-delete-button', $.signup.conf_delete);

			$('#print-link').click($.common.print);
			$.signup.rebuild_form();
			$.signup.admin_init();
		},

		admin_init: function () {
			var tab = $('#signup-food-choices-table');
			if (tab.size() > 0) {
				var heading = $('<h3 />').text('Columns');
				var ul = $('<ul />, <ol />').addClass('nolist');
				var trs = tab.find('tr');
				trs.eq(0).find('th').each(function (i, v) {
					var els = $();
					trs.each(function () {
						els = els.add($(this).find('th, td').eq(i));
					});
					ul.append(
						$('<li />').append(
							$('<input />', { 'type': 'checkbox', 'checked': 'checked' }).click(function () {
								var checked = $(this).is(':checked');
								if (checked) els.show();
								else els.hide();
							})
						).append(' ' + $(this).text())
					);
				});
				$('<div />').addClass('no-print').append(heading).append(ul).insertBefore(tab);
			}
			var tab = $('select[name=type]');
			if (tab.size() > 0) {
				tab.change(function () {
					$.signup.rebuild_groups();
					$.signup.rebuild_form();
				});
			}
			var tab = $('.signup-group-delete');
			if (tab.size() > 0) {
				tab.click(function (e) {
					$.signup.remove_group(e);
				});
			}
			var tab = $('.signup-group-add');
			if (tab.size() > 0) {
				tab.click(function () {
					var selectValue = $('select[name^=seats]').first().val();
					var spanHtml = $('<label />', { 'class': 'signup-group-number' });
					var selectHtml = $('<select />', { 'name': 'seats[]' });
					for (var i = 4; i <= 250; i += 1) {
						selectHtml.append('<option value="' + i + '"' + (i == selectValue ? ' selected="selected"' : '') + '>' + i + '</option>');
					}
					var inputHtml = $('<input />', { 'type': 'text', 'name': 'table_names[]', 'placeholder': 'Name', 'title': 'Optional field' });
					var addHtml = $('<span />', { 'class': 'signup-group-delete ui-icon ui-icon-trash inline-block' }).click(function (e) {
						$.signup.remove_group(e);
					});
					tab.parent('li').before(
						$('<li />').append(spanHtml).append(selectHtml).append(inputHtml).append(addHtml)
					);
					$.signup.rebuild_groups();
				});
			}
		},

		remove_group: function (e) {
			var tab = $('.signup-group-delete');
			if (tab.size() > 1) {
				$(e.target).parent('li').hide('slow', function () {
					$(this).remove();
					$.signup.rebuild_groups();
				});
			}
		},

		rebuild_groups: function () {
			var tableGroup = ($('select[name=type]').val() == 1 ? 'Table' : $('select[name=type]').val() == 2 ? 'Table' : $('select[name=type]').val() == 3 ? 'Coach' : $('select[name=type]').val() == 4 ? 'T-Shirt Size' : 'Group');
			$('.signup-group-number').each(function (index) {
				$(this).text(tableGroup + ' ' + (index + 1));
			});
		},
		
		rebuild_form: function () {
			var signup_type = $('select[name=type]').val();
			if(signup_type != 2){
				$("#swapping").hide();
			}
			else{
				$("#swapping").show();
			}
			if(signup_type == 3 || signup_type == 4 || signup_type == 5){
				$("#food").hide();
			}
			else{
				$("#food").show();
			}
		},

		submit_func: function (e, cancel) {
			var spinner = $('#signup-spinner');
			// change spinner height
			spinner.find('div').css('height', $(this).height());
			// show and hide
			$(this).hide();
			spinner.show();
			// get data
			var data = $(this).serializeArray();
			// find the name in the names index
			var index = $.inArray($('#signup-name input').val(), $.signup.names.names);
			if (index !== -1) data.push({ 'name': 'user_id', 'value': $.signup.names.ids[index] });
			// is this the cancel button?
			if (cancel) data.push({ 'name': 'cancel', 'value': '' });
			var form = this;
			$.post(this.action, data, function (data) {
				// redirect if told to do so (logout etc)
				if (data.redirect) {
					window.location = data.redirect;
					return;
				}
				// change content
				form.innerHTML = data.html;
				// show and hide
				spinner.hide();
				$.signup.form_init();
				$(form).show();
				// update tables
				$.signup.refresh_tables();
			}, 'json');
			return false;
		},

		refresh_tables: function () {
			$.ajax({
				url: script_url + 'signup/tables_refresh/' + $('#e_id').val(),
				cache: false,
				type: 'GET',
				dataType: 'json',
				success: function (data) {
					// redirect if told to do so (logout etc)
					if (data.redirect) {
						window.location = data.redirect;
						return;
					}
					$('#signup-tables').html(data.html);
				}
			});
		},

		form_init: function () {
			var options = $('#signup-for').detach().find('option[value]');
			$.signup.names = {
				names: [],
				ids: []
			};
			$(options).each(function () {
				$.signup.names.names.push(this.innerHTML);
				$.signup.names.ids.push(this.value);
			});
			var suName = $('#signup-name');
			suName.find('input').autocomplete({ 'source': $.signup.names.names, 'delay': 0, 'minLength': 3 });
			suName.find('label').text('Name');
			suName.find('span').remove();

			// cancel button
			$('#signup-cancel').click(function (e) {
				$.signup.submit_func.call($('#reservation-form')[0], e, true);
				return false;
			});
		},

		conf_delete: function () {
			var cont = $('<div />').addClass('delete-conf-box');
			cont.html('Are you sure you want to delete ' + $.trim($(this).parent().text()) + '\'s booking?<br />');
			var del = this;
			cont.dialog({
				resizable: false,
				modal: true,
				buttons: {
					"Delete": function () {
						$.common.goTo(del.href);
						$(this).dialog("close");
					},
					Cancel: function () {
						$(this).dialog("close");
					}
				}
			});
			return false;
		},

		serverTime: function () {
			var curTime = serverTimeArray;
			var timeDiff = serverTimeArray - new Date();
			var hour = $.signup.format_number(curTime.getHours());
			var min = $.signup.format_number(curTime.getMinutes());
			var sec = $.signup.format_number(curTime.getSeconds());
			$('<h2 />', { 'id': 'signup-cur-time' }).html('Server Time: <span id="signup-cur-hour">' + hour + '</span>' + ':' + '<span id="signup-cur-min">' + min + '</span>' + ':' + '<span id="signup-cur-sec">' + sec + '</span>').insertAfter('#servertime');
			//$('<p />', { 'id': 'error' }).html('Your Computer Clock Is Wrong By: ' + Math.abs(Math.round(timeDiff/1000)) + ' second(s).').insertAfter('#signup-cur-time');
			var hr = $('#signup-cur-hour');
			var mi = $('#signup-cur-min');
			var se = $('#signup-cur-sec');
			// increment sec function
			var nextSec = function () {
				/*if (sec == 59) {
					sec = 0;
					if (min == 59) {
						min = 0;
						if (hour == 23) {
							hour = 0;
						} else {
							hour++;
						}
						hr.text($.signup.format_number(hour));
					} else {
						min++;
					}
					mi.text($.signup.format_number(min));
				} else {
					sec++;
				}*/
				computer_time = new Date();
				var server_time = new Date(computer_time.getTime() + timeDiff);
				se.text($.signup.format_number(server_time.getSeconds()));
				mi.text($.signup.format_number(server_time.getMinutes()));
				hr.text($.signup.format_number(server_time.getHours()));
			};
			// increment second
			setInterval(function () { nextSec(); }, 1000);
		},

		format_number: function (num) {
			num = parseInt(num);
			if (num < 10) return '0' + num.toString();
			return num.toString();
		}
	};

})(jQuery);