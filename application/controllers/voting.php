<?php class Voting extends CI_Controller {

	function Voting() {
		parent::__construct();
	}

	function index() {
		$this->load->library('page_edit_auth');
		
		$sections = array('upcoming'=>'Upcoming Elections', 'elections'=>'Butler Elections', 'poster'=>'Making a Poster');
		$keys = array_keys($sections);
		$section = $this->uri->segment(3);
		if(!in_array($section, $keys)){
			$section = $keys[0];
		}
        
        $contact = $this->common_model->get_contact(18);
		
		$this->load->view('voting/voting', array(
			'access_rights' => $this->page_edit_auth->authenticate('voting'),
			'sections' => $sections,
			'section' => $section,
            'contact' => $contact
		));
	}
}

/* End of file voting.php */
/* Location: ./application/controllers/voting.php */