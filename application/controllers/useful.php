<?php

class Useful extends CI_Controller {

    function Useful() {
        parent::__construct();
        $this->page_info = array(
            'id' => 30,
            'title' => 'Useful Info',
            'big_title' => NULL,
            'description' => 'Just some generally useful information',
            'requires_login' => FALSE,
            'allow_non-butler' => FALSE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
    
        $page = $this->uri->segment(3);
        $pages = array('home'=>'Useful Pages', 'taxis'=>'Taxi Numbers', 'takeaway'=>'Takeaways in Durham');

        if(!isset($pages[$page])){
            reset($pages);
            $page = key($pages);
        }    
    
        $this->load->view('useful/useful', array('pages'=>$pages, 'page'=>$page));
    }
    
}
