(function ($) {
    $.claims = {
        init: function() {
            $('.select-all-claims').click(function(){
                $('.select-claims').prop('checked', $(this).is(':checked'));
            });
            
            $('.pay-selected').click(function(event){
                event.preventDefault();
                var url = $(this).attr('href');
                var n = $('.select-claims:checked').length;
                if(n === 0){
                    $.common.notify('Error...');
                }else{
                    $('.select-claims:checked').each(function(){
                        url += '/' + $(this).closest('tr').find('#claim-id').html();
                    });
                    window.loc.href = url;
                }
            });
            
            
        }
    };
})(jQuery);


/*  End of file claims.js  */

