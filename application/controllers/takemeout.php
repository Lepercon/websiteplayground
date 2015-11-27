<?php

class Takemeout extends CI_Controller {

    function Takemeout() {
        parent::__construct();
        
        $this->page_info = array(
            'id' => 2,
            'title' => 'Take Me Out',
            'big_title' => '<span class="big-text-medium">Take Me Out</span>',
            'description' => 'Take Me Out',
            'requires_login' => TRUE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
        $u_id = $this->session->userdata('id');
        $this->db->where('user_id', $u_id);
        $this->db->join('users', 'users.id=takemeout.user_id');
        $u = $this->db->get('takemeout')->row_array(0);
        if(empty($u)){
            $this->load->view('takemeout/not_signed_up');
        }else{
            $this->load->view('takemeout/button', array(
                'u' => $u
            ));
        }
    }
    
    function swap(){
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->set('status', $_POST['new_status']);
        $this->db->update('takemeout');
    }
    
    function admin(){
        if(isset($_POST['reset'])){
            $this->db->set('status', 1);
            $this->db->update('takemeout');
        }
            
        $this->load->view('takemeout/admin');
    }
    
    function info(){
        $this->db->where('user_id', $this->session->userdata('id'));
        $info = $this->db->get('takemeout')->row_array();
        $this->load->view('takemeout/info', array('info'=>$info));
    }
  
}