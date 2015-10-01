<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events/view_event/'.$e_id);

eval(error_code().success_code()); ?>
<h3>Upload a New File</h3>
    <?php echo form_open_multipart('events/add_file/'.$e_id, array('class' => 'no-jsify jcr-form')); ?>
        <ul class="nolist">
            <li>
                <label>File to Upload</label><?php echo form_upload('userfile'); ?>
            </li>
            <li>
                <label>File description</label><?php echo form_textarea(array(
                    'name' => 'description',
                    'value' => ($errors ? set_value('description') : ''),
                    'rows' => '6',
                    'placeholder' => 'File Description',
                    'title' => 'Optional. Give a description of the file content.',
                    'class' => 'input-help'
                )); ?>
            </li>
            <li>
                <label></label><?php echo token_ip('add_file');
                echo form_submit('add_file', 'Add File'); ?>
            </li>
        </ul>
    <?php echo form_close();?>
