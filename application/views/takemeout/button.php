<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed'); 
?>
<div style="background-image:url(<?php echo site_url('application/views/details/img/users/'.$u['uid'].'_medium.jpg'); ?>);" id="tap-img"></div>

<script>
    $('#tap-img').click(function(){
        $.ajax({
            url:'http://localhost/butler.jcr/takemeout/swap',
            type: 'POST',
            data:{
                new_status:$('#tap-img').hasClass('out')?1:0
            }
        }).success(function(){
            $('#tap-img').toggleClass('out');
        });
    });
    setInterval(function(){
        $.ajax({url:'http://localhost/butler.jcr/takemeout/info'}).success(function(e){
            data = JSON.parse(e);
            if(JSON.parse(data.html).status==1){
                $('#tap-img').removeClass('out');
            }else{
                $('#tap-img').addClass('out');
            }
        })
    }, 1000);
</script>
<style>
    .out{
        opacity: .2;
        font-size:300px;
        color:red;
        text-align: center;
        
    }
    .out:after{
        content:"X";
    }
    #tap-img{
        border:2px solid #eeb300; 
        width:300px;
        height:300px;
        background-size: cover;
    }
</style>
<?php
/*  End of file button.php  */