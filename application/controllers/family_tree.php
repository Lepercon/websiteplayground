<?php

class Family_tree extends CI_Controller {

	function Family_tree() {
		parent::__construct();
		$this->load->model('family_model');
		$this->load->library('form_validation');
	}
	
	function index() {
		$this->load->view('family_tree/family_tree');
	}
	
	function view_family() {
		$u_id = $this->uri->segment(3);
		$children = $this->family_model->get_generations_below(0, $u_id);
		$parents = $this->family_model->get_generations_above(0, $u_id);
		$this->load->view('family_tree/view_family', array(
			'children'=>$children, 
			'parents'=>$parents
		));
	}
	
	function new_family() {
	
		if(validate_form_token('new_family')) {
			$other_errors = $this->family_model->check_family();
			if($other_errors === FALSE or !empty($other_errors)) {
				$parents = $this->family_model->get_parents(); 
				$this->load->view('family_tree/add_family', array('parents'=>$parents, 'errors' => TRUE, 'other_errors' => $other_errors));
			}
			else {
				$this->family_model->save_family();
				$parents = $this->family_model->get_parents(); 
				$this->load->view('family_tree/add_family', array('parents'=>$parents, 'success'=>TRUE));
			}
		}
		else {
			$parents = $this->family_model->get_parents(); 
			$this->load->view('family_tree/add_family', array('parents'=>$parents));
		}
		
	}
	
}

/* End of file family_tree.php */
/* Location: ./system/application/controllers/family_tree.php */