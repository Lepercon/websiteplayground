<?php

class Green extends CI_Controller {

    function Green()
    {
        parent::__construct();
        $this->load->model('green_model');
        $this->page_info = array(
            'id' => 19,
            'title' => 'Green JCR',
            'big_title' => '<span class="big-text-medium">Green JCR</span>',
            'description' => 'Butler JCR is a green JCR, find out how to help keep it that way.',
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
        $this->load->view('green/green', array(
            'access_rights' => $this->page_edit_auth->authenticate('green'),
            'green_chair' => $this->users_model->get_exec_contact($this->users_model->convert_string_level_to_id('Green Committee Rep'), FALSE, FALSE),
            'green_tip' => $this->green_model->get_tip(),
            'events' => $this->events_model->get_events_by_category('Green', '3')
        ));
    }

    function manage_tips()
    {
        if(!(is_admin() or has_level('Green Committee Rep'))) {
            $this->index();
            return;
        }
        $errors = FALSE;
        if(isset($_POST['add_tip']) && validate_form_token('add_tip')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('tip', 'Tip', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('date', 'Date', 'trim|required|max_length[10]');
            if($this->form_validation->run()) {
                $this->green_model->add_tip();
            }
            else $errors = TRUE;
        }
        $tips = $this->green_model->get_tips();
        $this->load->view('green/manage_tips', array('tips' => $tips, 'errors' => $errors));
    }

    function delete_tip()
    {
        if(!(is_admin() or has_level('Green Committee Rep'))) {
            $this->index();
            return;
        }
        $d = $this->uri->rsegment(3);
        if($d !== FALSE && is_numeric($d)) {
            $this->green_model->delete_tip($d);
        }
        $this->manage_tips();
    }
    
    function butler_bikes(){
    
        if(!has_level('any')) {
            $this->index();
            return;
        }
        $this->load->view('green/butler_bikes');
    
    }
}

/* End of file voting.php */
/* Location: ./application/controllers/voting.php */