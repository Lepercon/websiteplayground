<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup/event/'.$e['id']);

eval(error_code());

echo form_open('signup/edit_signup/'.$e['id'], array('class' => 'jcr-form no-jsify'));
	$this->load->view('signup/signup_builder', array('e' => $e));
	echo form_submit('edit_signup', 'Save changes');
	echo form_hidden('event_id', $e['event_id']);
	echo token_ip('edit_signup');
echo form_close();?>