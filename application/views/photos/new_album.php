<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>New Photo Album</h2>
<?php

    if(isset($_POST['error'])){
        echo '<div class="validation_errors">'.$_POST['error'].'</div>';
    }
    
    echo form_open('', 'class="jcr-form"');
    echo '<p>'.form_label('Album Name:', 'album-name').form_input(array('name'=>'album-name', 'placeholder'=>'Album Name')).'</p>';
    echo '<p>'.form_label('Date:', 'date').form_input(array('name'=>'date', 'placeholder'=>date('d/m/Y'), 'class'=>'datepicker', 'value'=>date('d/m/Y'))).'</p>';
    echo '<p><label for="album-desc" style="vertical-align: top!important;">Album Description:</label>'.form_textarea(array('name'=>'album-desc', 'placeholder'=>'Album Description')).'</p>';
    echo form_label().form_submit('create', 'Create Album');
    echo form_close();