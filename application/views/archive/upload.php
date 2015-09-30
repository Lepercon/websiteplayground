<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$months = array(1=>'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

echo back_link('archive/index/'.$short_section);
eval(error_code());
?>

<h3>Upload new document to <?php echo $section; ?></h3>

* = required
<?php echo form_open_multipart('archive/add_new_doc/'.$short_section, array('class' => 'no-jsify jcr-form', 'id' => 'archive-upload-form'));?>
    <ul class="nolist">
        <li>
            <label>Name *</label>
            <input type="text" name="name" maxlength="50" placeholder="Document Name" />
        </li>
        <li>
            <label>Associated date</label>
            D: <select name="day"><option selected="selected"></option>
            <?php $today = date('j');
            for($i = 1; $i <= 31; $i++) echo '<option value="'.$i.'">'.sprintf('%02d', $i).'</option>';
            ?>
            </select> M: <select name="month"><option selected="selected"></option>
            <?php $today = date('n');
            for($i = 1; $i <= 12; $i++) echo '<option value="'.$i.'">'.$months[$i].'</option>';
            ?>
            </select>
        </li>
        <li>
            <label>Year *</label>
            <select name="year">
            <?php
                $today = date('Y');
                for($i = 2010; $i <= $today + 1; $i++) echo '<option value="'.$i.'" '.($i == $today ? 'selected="selected"' : '').'>'.($i-1).' - '.$i.'</option>';
            ?>
            </select>
        </li>
        <li>
            <label>File *</label>
            <input type="file" name="file" />
        </li>
        <li>
            <label></label>
            <?php
                echo form_submit('upload', 'Upload');
                echo token_ip('archive_upload');
            ?>
        </li>
    </ul>
<?php echo form_close(); ?>