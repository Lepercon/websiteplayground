<?php

class View_notifications extends CI_Controller {

    function View_notifications() {
        parent::__construct();
        $this->load->model('finance_model');
        //$this->load->view('finance/feedback');
        $this->finance_model->setup_gocardless();
        $this->page_info = array(
            'id' => 27,
            'title' => 'Finance Notifications',
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
    
    function index(){
        
        $u_id = $this->session->userdata('id');
        $admin = $this->finance_model->finance_permissions();
        $notifications = $this->finance_model->get_notifications($u_id, $admin);
        
        $this->load->view('finance/notifications/view', array(
            'notifications' => $notifications
        ));
    
    }
    
    function change(){
        //ajax requests
        
        $u_id = $this->session->userdata('id');
        $change = $this->uri->segment(4);
        $page_admin = $this->finance_model->finance_permissions();
        
        switch($change){
            case 'status_change':
                $ids = explode(',', $this->input->post('ids'));
                $new_status = $this->input->post('new_status');
                foreach($ids as $id){
                    $this->finance_model->change_notification_status($id, $new_status, $u_id, $page_admin);
                }
                break;
            case 'delete':
                $ids = explode(',', $this->input->post('ids'));
                foreach($ids as $id){
                    $this->finance_model->remove_notification($id, $u_id, $page_admin);
                }
                break;
        }
    }
    
}