<?php class Voting extends CI_Controller {

    function Voting() {
        parent::__construct();
        $this->page_info = array(
            'id' => 21,
            'title' => 'Voting',
            'big_title' => NULL,
            'description' => 'Vote in Butler JCR or Durham University elections',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('voting/voting'),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
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