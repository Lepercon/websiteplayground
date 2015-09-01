<?php class Prospective extends CI_Controller {

	function Prospective($authenticated = FALSE) {
		parent::__construct();
	}

	function index() {
		$this->load->library('page_edit_auth');
		$admin = $this->page_edit_auth->authenticate('prospective');
		$section = $this->uri->segment(3, '');
		$sections = array(
			'welcome' => 'Welcome to Butler',
			'durham' => 'Durham and its Colleges',
			'rooms' => 'Rooms and Facilities',
			'get_involved' => 'Sports, Societies and Committees'
		);
		
		if(!array_key_exists($section, $sections))
			$section = key($sections);
		
		if(isset($_POST['ajax'])){
			$this->load->view('prospective/get_section', array(
				'access_rights' => $admin,
				'section' => $section
			));
		}else{
			$this->load->view('prospective/section', array(
				'access_rights' => $admin,
				'section' => $section,
				'sections' => $sections
			));
		}
		
	}
	
	function page(){
		$this->index();
	}
}

/* End of file prospective.php */
/* Location: ./application/controllers/prospective.php */