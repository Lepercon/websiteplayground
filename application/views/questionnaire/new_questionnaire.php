<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('questionnaire');
eval(error_code());

echo form_open('questionnaire/add/'.$e_id, array('class' => 'jcr-form'));
	$this->load->view('questionnaire/questionnaire_form');
	echo form_submit('create', 'Create Questionnaire');
	echo form_hidden('event_id', $e_id);
	echo token_ip('new_questionnaire');
echo form_close(); ?>