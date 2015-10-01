<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!isset($_POST['submit'])){
    echo back_link('admin');
}

?>
<span id="users-list">
<?php foreach($users as $u){ ?>
    <p value="<?php echo $u['id']; ?>"><?php echo $u['surname'].', '.($u['prefname']==''?$u['firstname']:$u['prefname']).' ('.$u['username'].')'; ?></p>
<?php } ?>
</span>
<p>Please select a user by typing their firstname, surname or username into the box below, then select them from the dropdown list.</p>
<?php
    echo form_open('', 'class="jcr-form"');
?>    
    <p><?php echo form_label('User:').form_input(array('name'=>'name', 'id'=>'name', 'placeholder'=>'User', 'autocomplete'=>'off')).form_input(array('name'=>'user-id', 'id'=>'user-id', 'type'=>'hidden')); ?></p>
    <p><?php echo form_label('Reset and Display:', 'radio-display').form_radio(array('name'=>'reset-type', 'value'=>'display', 'checked'=>TRUE, 'class'=>'display-email', 'id'=>'radio-display')); ?></p>
    <p><?php echo form_label('Reset and Email:', 'radio-email').form_radio(array('name'=>'reset-type', 'value'=>'email', 'class'=>'display-email', 'id'=>'radio-email')).form_input(array('name'=>'email', 'id'=>'email', 'placeholder'=>'Email', 'disabled'=>'disabled')); ?></p>
<?php
    echo form_label().form_submit('submit', 'Reset Password');
    echo form_close();
?>