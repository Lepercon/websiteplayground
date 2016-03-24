<?php

class Invoices extends CI_Controller {

    function Invoices() {
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
            'css' => array('finance/finance', 'finance/notifications/notifications', 'finance/invoices/invoices'),
            'js' => array('finance/finance', 'finance/invoices/invoices', 'finance/notifications/notifications'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
        
    }
    
    function my_invoices() {
        $user_id = $this->session->userdata('id');
        $invoices = $this->finance_model->get_invoices($user_id);
        $groups = $this->finance_model->get_groups();
        $permissions = $this->finance_model->finance_permissions();
        $this->load->view('finance/invoices/my_invoices',array(
            'invoices'=>$invoices, 
            'groups'=>$groups,
            'permissions'=>$permissions
        ));
    }


    function my_groups() {
        $user_id = $this->session->userdata('id');
        $groups = $this->finance_model->get_members_groups($user_id);
        $this->load->view('finance/invoices/my_groups',array(
            'groups'=>$groups,
            'permissions' => $this->finance_model->finance_permissions()
        ));
    }


    function my_group($group_id = NULL) {
        if(is_null($group_id)){
            $group_id = $this->uri->segment(4);
        }
        
        $user_id = $this->session->userdata('id');
        $permissions = $this->finance_model->finance_permissions();
        
        if($permissions || $this->finance_model->has_permission($group_id)){
            $this_group = $this->finance_model->get_group($group_id);
            $members = $this->finance_model->get_members($group_id);
            $invoices = $this->finance_model->get_invoices_by_group($group_id, !$this->uri->segment(5));
            $inv_tot = $this->finance_model->get_sorted_invoices_by_group($group_id);
            $this->load->view('finance/invoices/my_group',array(
                'group' => $this_group,
                'members' => $members,
                'invoices' => $invoices,
                'inv_tot'=> $inv_tot
            ));
        }else{
            $this->index();
            return;
        }
    }

    function mark_paid(){
    
        $i_id = $this->input->post('id', true);
        $new_status = ($this->input->post('new_status', true)=='1'?'1':'0');
        $user_id = $this->session->userdata('id');
    
        $invoice = $this->finance_model->get_invoice_by_id($i_id);
        $budget = $this->finance_model->get_budget($invoice['group_id']);
        
        if($user_id === $invoice['member_id'] or ($this->finance_model->finance_permissions())){
            $this->finance_model->update_invoice($i_id, $new_status);
            foreach($budget['owners'] as $b){
                $owners[] = $b['id'];
            }
            $this->finance_model->add_notification($owners, 'Invoices', $this->session->userdata('firstname').' '.$this->session->userdata('surname').' claims to have paid for '.$invoice['name'].'.', 'finance/invoices/my_group/'.$budget['id']);
        }
        $this->output->append_output(array('success' => FALSE));
        return false;
    }

    function my_invoice(){
        $i_id = $this->uri->segment(4);
        $user_id = $this->session->userdata('id');
        $invoice = $this->finance_model->get_invoice_by_id($i_id);
        $group = $this->finance_model->get_group($invoice['group_id']);
        if(empty($invoice) || ($user_id != $invoice['member_id'])) {
            $this->index();
            return;
        }
        $this->load->view('finance/invoices/my_invoice', array('invoice'=>$invoice, 'group'=>$group));
    }

    function add_members() {
    
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('ids', 'New Members', 'required');
        $this->form_validation->set_message('required', "You haven't selected any %s");

        $group_id = $this->uri->segment(4);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        $this_group = $this->finance_model->get_group($group_id);
        $users = $this->users_model->get_all_user_ids_and_names();
        $message = '';

        if($this->finance_model->has_permission($group_id)){
            $res = 0;
            if($this->form_validation->run() !== FALSE){
                $ids = explode(',',$this->input->post('ids', TRUE), -1);
                $res = $this->finance_model->add_members($group_id, $ids);
                if($res != sizeof($ids)){
                    $message = 'One or more of your members is already in this group';
                }
            }
            $this->load->view('finance/invoices/add_members',array('group'=>$this_group, 'users'=>$users, 'new_members'=>$res, 'message'=>$message));
        }else{
            $this->index();
            return;
        }

    }

    function add_invoice(){
    
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('invoice_name', 'Invoice Name', 'required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('details', 'Description', 'max_length[500]');
        $this->form_validation->set_rules('amount', 'Amount', 'required|decimal');

        $group_id = $this->uri->segment(4);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        $users = $this->users_model->get_all_user_ids_and_names();
        $members = $this->finance_model->get_members($group_id);
        $this_group = $this->finance_model->get_group($group_id);
        
        $ids = array();
        $run = false;
        $no_ids = false;
        $res = '';
        
        if(isset($_POST['amount'])){
            $_POST['amount'] = substr($_POST['amount'],2);
        }

        if($this->finance_model->has_permission($group_id)){
            if($this->form_validation->run() !== FALSE){
                $run = true;        
                foreach($_POST as $key => $value){
                    if(strrpos($key, 'debtor_') === 0){
                        $ids[] = $value;
                    }
                }
                $date = (DateTime::createFromFormat('d/m/Y', $_POST['date'])->format('U'));
                $name = $_POST['invoice_name'];
                $amount = $_POST['amount'];                
                $details = $_POST['details'];
                if(!empty($ids)){
                    $res = $this->finance_model->add_invoice($ids, $date, $name, $amount, $group_id, $details);
                }else{
                    $no_ids = true;
                }
            }
            $this->load->view('finance/invoices/add_invoice',array(
                'users'=>$users,
                'members'=>$members,
                'this_group'=>$this_group,
                'run'=>$run,
                'no_ids'=>$no_ids,
                'result'=>$res
            ));
        } else {
            $this->load->view('home/permission_denied');
            return;
        }
        
    }
    
    function adding_invoice(){

        $group_id = $this->uri->segment(4);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        if(!$this->finance_model->has_permission($group_id)){
            $this->index();
            return;
        }
        $invoice_name = $_POST['Invoice_Name'];        
        
        $this->load->view('finance/invoices/adding_invoice', array('invoice_name'=>$invoice_name, 'amount'=>$amount, 'date'=>$date, 'details'=>$details, 'users'=>$users));
    }

    function remove_invoice() {
        $i_id = $this->uri->segment(4);
        if($i_id != FALSE) {
            $invoice = $this->finance_model->get_invoice_by_id($i_id);
            if($this->finance_model->get_permissions($invoice['group_id'], $this->session->userdata('id')) == 1) {
                $this->db->where('id', $i_id);
                $this->db->delete('invoices');
            }
        }
    }

    function admin_mark_paid(){
        $i_id = $_POST['payment_id'];
        $invoice = $this->finance_model->get_invoice_by_id($i_id);
        if($this->finance_model->get_permissions($invoice['group_id'], $this->session->userdata('id')) == 1) {
            $this->db->where('id', $i_id);
            $data = array('paid' => $_POST['status']);
            if($_POST['status']){
                $data['date_paid'] = time();
                $data['marked_by'] = $this->session->userdata('id');
            }
            $this->db->update('invoices', $data);
            $this->finance_model->add_notification($invoice['member_id'], 'Invoices', $_POST['status']?'Your invoice "'.$invoice['name'].'" has been marked as paid.':'Your invoice "'.$invoice['name'].'" has been marked as unpaid.', 'finance/invoices/my_invoices');
        }else{
            log_message('error', 'Permission Denied: '.$i_id.' - '.$this->session->userdata('id'));
        }
    }

    function ballot(){
        
        $this->load->model('ballot_model');
        
        $id = $this->uri->segment(4);
        $u_id = $this->session->userdata('id'); 
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        log_message('error', var_export($ballot,true));
        
        if($ballot['close_time'] < time()){
            if($this->ballot_model->admin()){
                $payments = $this->ballot_model->get_payments($id);
                
                if(isset($_POST['send-invoices'])){
                    $this->ballot_model->send_invoices($ballot, $payments['not_sent']);
                    $payments = $this->ballot_model->get_payments($id);
                }                
                
                $this->load->view('ballot/payments', array(
                    'b' => $ballot,
                    'payments' => $payments
                ));
            }else{
                redirect('ballot');
            }
        }else{
            redirect('ballot/view/'.$ballot['id']);
        }
    }
    
}