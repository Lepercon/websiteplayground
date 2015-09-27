<?php

class Email extends CI_Controller {

	function Email() {
		parent::__construct();
	}

	function index() {
			
		if(isset($_POST['submit']) && logged_in()){
		
			$this->load->library('email');
			
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			
			$this->email->to($_POST['to']);
			$this->email->from($_POST['from-email'], $_POST['from']);
			$this->email->message(nl2br($_POST['message']));
			$this->email->subject($_POST['subject']);
			$_POST['submit'] = TRUE;
			
			if(ENVIRONMENT != 'development'){
				$this->email->send();
			}
			
		}
		$this->load->view('email/email');
	}
	
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */