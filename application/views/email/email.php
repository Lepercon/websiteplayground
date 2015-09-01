<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h2>Use this form to send an email.</h2>
<?php 
	if(isset($_POST['submit'])){ ?>
		<p>Sent!</p>
	<?php }

	echo form_open('', 'class="jcr-form"');
	
	echo '<p>'.form_label('To:').form_input(array('name'=>'to', 'placeholder'=>'Their Email', 'title'=>'You can separate multiple addresses by commas if you wish.', 'value'=>(isset($_POST['to'])?$_POST['to']:''))).'</p>';
	echo '<p>'.form_label('From:').form_input(array('name'=>'from', 'placeholder'=>'Name', 'value'=>(isset($_POST['from'])?$_POST['from']:''))).' '.form_input(array('name'=>'from-email', 'placeholder'=>'Email', 'value'=>(isset($_POST['from-email'])?$_POST['from-email']:''))).'</p>';
	echo '<p>'.form_label('Subject:').form_input(array('name'=>'subject', 'placeholder'=>'Subject', 'value'=>(isset($_POST['subject'])?$_POST['subject']:''))).'</p>';
	echo '<p>'.form_label('Message:').form_textarea(array('name'=>'message', 'placeholder'=>'Your Message', 'value'=>(isset($_POST['message'])?$_POST['message']:''))).'</p>';
	echo '<p>'.form_label('').form_submit('submit', 'Send Message').'</p>';
	
	echo form_close();
?>