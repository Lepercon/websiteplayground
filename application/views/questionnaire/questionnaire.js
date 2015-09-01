(function($) {
	$.questionnaire = {
		init: function() {
			$.questionnaire.admin_init();
		},
		admin_init: function() {
			$.questionnaire.qDeleteInit();
			$.questionnaire.qResetInit();
			$("#q-add").on('click touch', function(e) {
				e.preventDefault();
				var question = $($(".q-question")[0]).clone();
				question.find('.q-question-name').tooltip();
				question.find('.q-choices').tooltip();
				question.find(".q-radio").prop('name','input-temp');
				$.questionnaire.qReset(question);
				question.insertBefore('#q-add');
				delete question;
				$.questionnaire.qNumber();
			});
			$("#content").on('change', ".q-radio", function(e) {
				if($(this).hasClass("q-opt1")) {
					$(this).closest(".q-question").find(".q-choices").val('').parent('li').show();
				} else if($(this).hasClass("q-opt0")) {
					$(this).closest(".q-question").find(".q-choices").val('').parent('li').hide();
				} else if($(this).hasClass("q-opt2")) {
					$(this).closest(".q-question").find(".q-choices").val('Yes;No').parent('li').hide();
				}
			});
		},
		qDeleteInit: function() {
			$("#content").on('click touch', ".q-delete", function(e) {
				e.preventDefault();
				if($(".q-delete").length > 1) {
					$(this).closest('.q-question').remove();
					$.questionnaire.qNumber();
					delete question;
				} else {
					$.common.notify('You cannot delete the only question.');
				}
			});
		},
		qResetInit: function() {
			$("#content").on('click touch', ".q-reset", function(e) {
				e.preventDefault();
				$.questionnaire.qReset($(this).closest('.q-question'));
			});
		},
		qReset: function(question) {
			question.find('.q-question-name').val('');
			question.find('.q-choices').val('').parent('li').hide();
			question.find('.q-radio').prop('checked',false);
			question.find('.q-opt0').prop('checked',true);
		},
		qNumber: function() {
			$(".q-question").each(function(index) {
				$(this).find(".q-number").text(index + 1);
				$(this).find(".q-radio").prop('name','input' + index);
			});
		}
	};
})(jQuery);