<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$path = 'involved/img/posters/'.$details['short'].'.png';
if(!file_exists(VIEW_PATH.$path)){
    $path = 'involved/img/posters/default.png';
}
?>
<div class="jcr-box square-box">
    <img class="involved-poster" src="<?php echo VIEW_URL.$path; ?>">
    <?php
        if($access_rights){
            echo '<h3>Upload New Poster:</h3>';
            echo form_open_multipart('involved/index/'.$page.'/'.$details['short'], 'class="jcr-form no-jsify"');
            echo form_upload('userfile', '', 'style="width:200px;"');
            echo form_hidden(array('page'=>$details['short']));
            echo form_submit('upload', 'Upload');
            echo form_close();
        }
    ?>
</div>