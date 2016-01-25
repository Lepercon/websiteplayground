<?php

class Home extends CI_Controller {

    function Home() {
        parent::__construct();
        $this->load->model('finance_model');
        //$this->load->view('finance/feedback');
        $this->finance_model->setup_gocardless();
        $this->page_info = array(
            'id' => 27,
            'title' => 'Finance',
            'big_title' => '<span class="big-text-small">My Finances</span>',
            'description' => 'JCR, Sports and Society finances',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => TRUE,
            'css' => array('finance/finance', 'finance/notifications/notifications'),
            'js' => array('finance/finance', 'finance/invoices/invoices', 'finance/notifications/notifications'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {

    }
    
    function payments(){
        $this->load->library('GoCardless');
        $user_id = $this->session->userdata('id');
        $in = $this->finance_model->get_invoices($user_id);
        $gr = $this->finance_model->get_groups();
        $permissions = $this->finance_model->finance_permissions();
        foreach($in as $i){
            $invoices[$i['id']] = $i;
        }
        foreach($gr as $g){
            $groups[$g['id']] = $g;
        }
        $this->load->view('finance/payments/merchant', array(
            'invoices'=>$invoices, 
            'groups'=>$groups,
            'permissions'=>$permissions
        ));
    }
    
    function payment_complete(){
        $this->load->view('finance/payments/payment_complete');
    }
    
    
}