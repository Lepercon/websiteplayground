<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
    if(isset($_POST['order'])){
        echo '<div class="validation_'.($_POST['order']['success']?'success':'errors').'"><span class="ui-icon ui-icon-'.($_POST['order']['success']?'check green-icon':'close').'"></span>'.$_POST['order']['message'].'</div>';
    }
?>