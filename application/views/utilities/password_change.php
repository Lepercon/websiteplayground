<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Temporary Password</h2>
<p>You are currently using a temporary password so you are required to change it, you will be unable to access the site until you do so.</p>
<p>Please supply a non @durham.ac.uk email. Your new password must be at least 6 characters.</p>
<?php echo validation_errors('<div class="validation_errors"><span class="ui-icon ui-icon-close inline-block"></span>', '</div>'); ?>
<?php
	echo form_open('', 'class="jcr-form"');
	
	echo '<p>'.form_label('Username:').$this->session->userdata('username').'</p>';
	echo '<p>'.form_label('Email').form_input(array('name'=>'email', 'placeholder'=>'Email', 'value'=>isset($_POST['email'])?$_POST['email']:$this->session->userdata('custom_email'))).'</p>';
	echo '<p>'.form_label('New Password:').form_password(array('name'=>'password', 'placeholder'=>'Password')).'</p>';
	echo '<p>'.form_label('Confirm Password:').form_password(array('name'=>'confirm_password', 'placeholder'=>'Confirm Password')).'</p>';
	
	echo form_label();
	echo form_submit('change', 'Change Password');
	echo form_close();
?>