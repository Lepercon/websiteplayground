<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup');

eval(error_code());
if(!isset($errors)) $errors = FALSE;

echo form_open('signup/new_signup/'.$e_id, array('class' => 'jcr-form no-jsify'));
	$this->load->view('signup/signup_builder');
	echo form_submit('new_signup', 'Create Signup');
	echo form_hidden('event_id', $e_id);
	echo token_ip('new_signup');
echo form_close();
?>