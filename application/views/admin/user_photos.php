<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!isset($noback)){
    echo back_link('admin');
}
?>
<h2>New User:</h2>
<span id="users-list">
<?php foreach($users as $u){ ?>
    <p value="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></p>
<?php } ?>
</span>
<p>Please select a user by typing their firstname, surname or username into the box below, then select them from the dropdown list.</p>
<?php
    echo form_open('', 'class="jcr-form"');
?>    
    <p><?php echo form_label('User:').form_input(array('name'=>'name', 'id'=>'name', 'placeholder'=>'Name', 'autocomplete'=>'off')).form_input(array('name'=>'user-id', 'id'=>'user-id', 'type'=>'hidden')); ?></p>    
<?php
    echo form_label().form_submit('choose-name', 'Select User');
    echo form_close();
?>