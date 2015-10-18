<?php

class International extends CI_Controller {

    function International()
    {
        parent::__construct();
        $this->page_info = array(
            'id' => 20,
            'title' => 'International',
            'big_title' => '<span class="big-text-tiny">International Committee</span>',
            'description' => 'Butler College has a strong international community',
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
        $this->load->library('page_edit_auth');
        $this->load->view('international/international', array(
            'access_rights' => $this->page_edit_auth->authenticate('international')
        ));
    }
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */