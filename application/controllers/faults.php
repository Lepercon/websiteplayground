<?php

class Faults extends CI_Controller {

	function Faults() {
		parent::__construct();
		$this->load->model('faults_model');
	}
	
	function index($errors = FALSE) {
		$this->load->view('faults/faults', array(
			'faults' => $this->faults_model->get_faults(),
			'errors' => $errors
		));
	}
	
	function report() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('location', 'Problem Location', 'required|trim|max_length[100]|xss_clean');
		$this->form_validation->set_rules('description', 'Problem Description', 'required|trim|xss_clean');
		if($this->form_validation->run()) {
			$this->faults_model->add_fault();
			$this->load->view('faults/success');
		} else {
			$this->index(TRUE);
		}
	}
	
	function delete() {
		$p_id = $this->uri->segment(3);
		if(!is_admin() OR $p_id == FALSE) {
			$this->index();
			return;
		}
		$this->faults_model->delete_fault($p_id);
	}
}
/* End of file faults.php */
/* Location: ./application/controllers/faults.php */