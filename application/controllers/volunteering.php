<?php

class Volunteering extends CI_Controller {

    function Volunteering()
    {
        parent::__construct();
        $this->load->model('volunteering_model');
        $this->page_info = array(
            'id' => 33,
            'title' => 'Volunteering Projects',
            'big_title' => '<span class="big-text-medium">Projects</span>',
            'description' => 'Butler JCR is involved in many volunteering project, find out more.',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index()
    {
        $this->load->model('events_model');
        $this->load->library('page_edit_auth');
        $this->load->view('volunteering/volunteering', array(
            'access_rights' => $this->page_edit_auth->authenticate('volunteering'),
            'vice_president' => $this->users_model->get_exec_contact($this->users_model->convert_string_level_to_id('Vice-President'), FALSE, FALSE),
            'events' => $this->events_model->get_events_by_category('Volunteering', '3')
        ));
    }

    
}

/* End of file voting.php */
/* Location: ./application/controllers/voting.php */