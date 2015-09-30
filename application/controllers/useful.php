<?php

class Useful extends CI_Controller {

    function Useful() {
        parent::__construct();
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
