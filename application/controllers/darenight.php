<?php class Darenight extends CI_Controller {

    function Bar() {
        parent::__construct();
        $this->page_info = array(
            'id' => 35,
            'title' => 'Dare Night',
            'big_title' => NULL,
            'description' => NULL,
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
    
                 
    }

}

/* End of file bar.php */
/* Location: ./application/controllers/bar.php */