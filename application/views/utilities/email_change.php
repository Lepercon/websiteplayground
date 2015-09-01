<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Alternative Email Address</h2>
<p>To continue using the site, please supply an alternative email address.</p>
<p>This email will be used to reset your password once you no longer have access to your Durham email.</p>
<?php echo validation_errors('<div class="validation_errors"><span class="ui-icon ui-icon-close inline-block"></span>', '</div>'); ?>
<?php
	echo form_open('', 'class="jcr-form"');
	
	echo '<p>'.form_label('Username:').$this->session->userdata('username').'</p>';
	echo '<p>'.form_label('Email').form_input(array('name'=>'email', 'placeholder'=>'Email', 'value'=>isset($_POST['email'])?$_POST['email']:$this->session->userdata('custom_email'))).'</p>';
	
	echo form_label();
	echo form_submit('confirm', 'Confirm Email');
	echo form_close();
?>
<p>We promise to never use this email to send you anything spammy.</p>