(function ($) {
    $.involved = {
        section: '',
        page: '',
        init: function () {
            var new_spinner = $('#spinner').clone().attr('id', 'involved-spinner').hide();
            $('#involved-right').append(new_spinner);
            $('#involved-left li a').click(function () {
                $.involved.load_section.call(this);
                history.pushState({}, '', $(this).attr('href'));
                return false;
            });
            $.involved.on_click();
        },

        load_section: function () {
            $(this).addClass('anchor-selected');
            $(this).parent().siblings().find('.anchor-selected').removeClass('anchor-selected');
            $('#involved-content-area').hide();
            $('#involved-spinner').show();
            var url_parts = this.href.slice(this.href.indexOf('index/') + 6).split('/');
            $.involved.page = url_parts[0];
            $.involved.section = url_parts[1];
            $.post(this.href, [{ 'name': 'ajax', 'value': 'none'}], $.involved.load, 'json');
            var url = this.href.split('/index/').join('/poster/');
            var new_spinner = $('#spinner').html()
            $('.poster-outline').html('<div class="jcr-box square-box">'+new_spinner+'</div>');
            $.ajax({
                type: "POST",
                url: url,
                success: function(e) {
                    $('.poster-outline').html(JSON.parse(e).html);
                    $.involved.on_click();
                }
            });
        },

        load: function (data) {
            if (data.redirect) {
                window.location = data.redirect;
                return;
            }
            $('#involved-content-area').html(data.html).jsify();
            $.common.preload_images($('#involved-content-area img'), $.involved.finish_loading);
        },

        finish_loading: function () {
            $('#involved-spinner').hide();
            $('#involved-content-area').show();
            $.common.interface();
            //$('html,body').animate({ scrollTop: $('#involved-right').offset().top }, { duration: 'slow' });
        },
        
        on_click: function(){
            $('.poster-outline img.involved-poster').click(function(){
                var theImage = new Image();
                theImage.src = $('.poster-outline img.involved-poster').attr("src");

                var imageWidth = theImage.width;
                $('.poster-outline img.involved-poster').clone().dialog({
                    modal:true,
                    resizable:false,
                    draggable:false,
                    title:'Poster - '+$('#involved-content-area .wotw-day').eq(0).html(),
                    width:Math.min(imageWidth+10, 500)
                });
                $('.ui-dialog img.ui-dialog-content').width(Math.round($('.ui-dialog img.ui-dialog-content').parent().width())-10);
            });
            console.log('added');   
        }
    };
})(jQuery);