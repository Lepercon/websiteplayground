<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
if(isset($data)){
    var_dump($data);
}else{
    echo '<p>data not set</p>';
}
if(isset($string)){
    echo $string;
}
 echo form_open('');
 echo form_submit('submit', 'Reload');
 echo form_close();
?>
