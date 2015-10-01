<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('admin');
?>
<h2><?php echo ($user['prefname']==''?$user['firstname']:$user['prefname']).' '.$user['surname']; ?></h2>
<h3>Current Image</h3>
<img src="<?php echo get_usr_img_src($user['uid'], 'large'); ?>">
<h3>Upload New</h3>
<?php
    echo form_open_multipart('', 'class="jcr-form no-jsify image-upload"');
    echo form_hidden(array('user-id'=>$user['id']));
    echo form_upload('file-upload');
    echo form_submit('upload-img', 'Upload Image');
    echo form_close();
?>