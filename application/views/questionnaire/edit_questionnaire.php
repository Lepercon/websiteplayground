<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('questionnaire/answer/'.$q['id']);
eval(error_code());

echo form_open('questionnaire/edit/'.$q['id'], array('class' => 'jcr-form'));
	$this->load->view('questionnaire/questionnaire_form');
	echo form_submit('save', 'Save Changes');
	echo token_ip('edit_questionnaire');
echo form_close(); ?>