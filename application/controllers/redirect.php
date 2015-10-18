<?php

class Redirect extends CI_Controller {

    function Redirect() {
        parent::__construct();        
        $this->page_info = array(
            
        );
    }

    function index() {
        $this->load->view('display_data');
    }
    
    function link(){
        $this->load->view('display_data', array('data'=>1));
    }
    
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */