<?php

class International extends CI_Controller {

    function International()
    {
        parent::__construct();
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