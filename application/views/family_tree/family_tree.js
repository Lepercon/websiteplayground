$( document ).ready(function() {
    var c = 1;
    $( ".child-add" ).click(function() {
    	$( "#child1" ).clone().append( "<span class='child-delete ui-icon ui-icon-trash inline-block'></span>" ).attr('id', 'child'+(++c) ).appendTo( "#children" );
    });
    
    $(document).on('click', '.child-delete', function() {
        $(this).parent().remove();
    });
    
});