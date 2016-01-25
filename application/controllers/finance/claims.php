<?php

class Claims extends CI_Controller {

    function Claims() {
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
        if($this->uri->segment(2) == 'view_claim' && $this->finance_model->finance_permissions()){
            $id = $this->uri->segment(3);
            $claim = $this->finance_model->get_claim($id);
            $this->page_info['title'] .= ' - '.$this->uri->segment(3).' - '.$claim['pay_to'];
        }
    }

    function index() {
        
    }
    
    function claims_form(){
        if(logged_in()){
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');
            $this->load_validation();
            
            $budgets = $this->finance_model->get_budgets();
            foreach($budgets as $b){
                $budgets_list[$b['id']] = $b['budget_name'];
                if(isset($_POST['budget_id']) && $_POST['budget_id'] == $b['id']){
                    $budget = $b;
                }
            }
            
            $validation = $this->form_validation->run();
            if($validation)
                $claim_id = $this->submit_claim($budget);
            if($validation and ($claim_id['id'] !== false)){
                $claim = $this->finance_model->get_claim($claim_id['id']);
                $this->load->view('finance/claims/edit_claim', array(
                    'admin'=>$this->finance_model->finance_permissions(),
                    'budgets'=>$budgets_list,
                    'validation'=>$validation,
                    'claim'=>$claim,
                    'type'=>'new',
                    'page_admin'=>$this->finance_model->finance_permissions(),
                    'file_errors'=>$claim_id['errors']
                ));
            }else{
                $this->load->view('finance/claims/claims_form', array(
                    'budgets'=>$budgets_list,
                    'page_admin'=>$this->finance_model->finance_permissions()
                ));
            }
        }else{
            $this->index();
        }
    }
    
    function get_upload(){
    
        if(isset($_FILES['userfile']) and $_FILES['userfile']['size'] > 0){
            $config = array(
                'upload_path'=>'./application/views/finance/files/',
                'allowed_types'=>'jpeg|jpg|png|pdf',
                'max_size'=>8192,
                'encrypt_name'=>TRUE
            );
            
            $this->load->library('upload', $config);
            if($this->upload->do_upload()){
                $data = $this->upload->data();
                if($data['file_ext'] == '.pdf'){/*
                    $cmd = 'convert -density 300 '.$data['file_name'].' '.$data['raw_name'].'.png';                    
                    shell_exec($cmd);
                    */
                    $imagick = new Imagick(); 
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($data['full_path']);
                    $imagick->writeImages($data['file_path'].$data['raw_name'].'.png', false); 
                    if(file_exists($data['file_path'].$data['raw_name'].'.png')){
                        $data['file_name'] = $data['raw_name'].'.png';
                    }else{
                        $data['file_name'] = '';
                    }                    
                    $i = 0;
                    while(file_exists($data['file_path'].$data['raw_name'].'-'.$i.'.png')){
                        $data['file_name'] .= ($data['file_name']==''?'':',').$data['raw_name'].'-'.$i.'.png';
                        $i++;
                    }
                }
                return array('fn'=>$data['file_name'].',', 'errors'=>FALSE);
            }else{
                $errors = $this->upload->display_errors();
                return array('fn'=>'', 'errors'=>$errors);
            }
        }else{
            return array('fn'=>'', 'errors'=>'');
        }

    }
    
    function submit_claim($budget){
        if(logged_in()){
        
            $files = '';
            $response = '';
            $user_id = $this->session->userdata('id');
            
            //file upload
            $files = $this->get_upload();        
                
            $account = $this->finance_model->encrypt_data($_POST['account-number']);
            $sortcode = $this->finance_model->encrypt_data($_POST['sort-code']);
            
            $pay_to = $_POST['pay_to'];
            $payment_method = $_POST['payment_method'];
            $amount = substr($_POST['amount'],2);
            $item = $_POST['item'];
            $details = nl2br($_POST['details']);                
            
            $res = $this->finance_model->add_claim($user_id, $pay_to, $payment_method, $amount, $item, $budget, $details, $files['fn'], $account, $sortcode);
            return array('id'=>$res['id'], 'errors'=>$files['errors']);            
            
        }
    }
    
    /*function claims(){
        $admin = $this->finance_model->finance_permissions();
        if($admin){
            $this->view_claims();
        }else{
            $this->my_claims();
        }
    }*/
    
    function view_claims(){
    
        $show_to_be_reviewed = $this->uri->segment(3);
        $show_paid = $this->uri->segment(4);
        if($this->finance_model->finance_permissions()){
        
            $claims_waiting = $this->finance_model->get_claims_by_status(1);
            if($show_to_be_reviewed){
                $claims_to_be_reviewed = $this->finance_model->get_claims_by_status(0);
            }else{
                $claims_to_be_reviewed = FALSE;
            }
            
            if($show_paid){
                $claims_paid = $this->finance_model->get_claims_by_status(2);
            }else{
                $claims_paid = FALSE;
            }
            $all_levels = $this->users_model->get_all_levels();
            
            $budgets = $this->finance_model->get_budgets();
            
            $this->load->view('finance/claims/view_claims', array(
                'claims_waiting'=>$claims_waiting, 
                'claims_to_be_reviewed'=>$claims_to_be_reviewed, 
                'claims_paid'=>$claims_paid,
                'budgets'=>$budgets,
                'all_levels'=>$all_levels,
                'admin'=>true
            ));
            
        }else{
            $this->load->view('home/permission_denied');
        }
    }
    
    function my_claims(){
        if(logged_in()){ 
        
            $user_id = $this->session->userdata('id'); 
            $from_user_claims = $this->finance_model->get_claims_from_user($user_id);
            $to_user_claims = $this->finance_model->get_claims_to_user($user_id);
                        
            $this->load->view('finance/claims/my_claims', array(
                'from_user_claims'=>$from_user_claims, 
                'to_user_claims'=>$to_user_claims,
                'admin'=>$this->finance_model->finance_permissions()
            ));
                
        }
    }
    
    
    function add_budget(){
        
        if($this->finance_model->finance_permissions()){ 
            return $this->finance_model->add_budget($_POST['newname'], $_POST['holdername']);
        }
        return false;
    }
    
    function change_claim_status(){
        
        $claim_id = $_POST['claimid'];
        $new_status = $_POST['newstatus'];
        $claim = $this->finance_model->get_claim($claim_id);
        $user_id = $this->session->userdata('id');
        $permission = false;
        $admin = $this->finance_model->finance_permissions();
        
        $levels = $this->users_model->get_user_levels($user_id);
        $le = array();
        foreach($levels as $l){
            $this->db->or_like('level_id', $l['level_id'], 'both');
            $le[] = $l['level_id'];
        }
        
        switch($claim['status']){
            case 0:
                if($new_status == 1){
                    $admin_levels = explode(',', $claim['level_id']);
                    $match = array_intersect($admin_levels, $le);
                    $permission = !empty($match);
                }                   
                break;                
            case 1:
                if($new_status == 0){
                    $permission = in_array($user_id, explode(',', $budget['owner_ids']));
                }elseif($new_status == 2){
                    $permission = $admin;
                }
                break;
            case 2:
                if($new_status == 1)
                    $permission = $admin;
                   break;
         }    
         $permission = $permission || $admin; 
         if($permission){
             return $this->finance_model->change_claim_status($claim_id, $new_status);
         }else{
             log_message('error', 'Permission Denied claim_id='.$claim_id.' new_status='.$new_status.' user_id='.$user_id);
             return false;
         }
    }
    
    function edit_budgets(){
    
        $admin = $this->finance_model->finance_permissions();
        if(!$admin){    
            $this->index();
        }
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        if(isset($_POST['edit-budget-submit'])){
            $this->form_validation->set_rules('budget_name', 'Budget Name', 'required|min_length[3]|max_length[50]|xss_clean');
        }elseif(isset($_POST['remove-user-submit'])){
            foreach($_POST as $k => $v){
                if(strpos($k, 'remove_') !== FALSE){
                    $this->finance_model->remove_admin($_POST['budget_id'], $v);
                }
            }
        }
        
        $users = $this->users_model->get_all_user_ids_and_names();
        $run_successs = $this->form_validation->run();
        
        if($run_successs){
            $budget_id = $this->input->post('budget_id', TRUE);
            $budget_name = $this->input->post('budget_name', TRUE);
            $this->finance_model->update_budget($budget_id, $budget_name);
        }
        
        if(isset($_POST['holder']) && $_POST['holder']){
            $this->finance_model->new_admin($_POST['budget_id'], $_POST['holder']);
        }
        
        $this->load->view('finance/claims/edit_budgets', array(
            'budgets'=>$this->finance_model->get_budgets(),
            'users'=>$users,
            'success'=>$run_successs,
            'all_levels' => $this->users_model->get_all_levels()
        ));
    }
        
    function edit_claim(){
        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load_validation();

        $user_id = $this->session->userdata('id'); 
        $c_id = $this->uri->segment(3);
        $old_claim = $this->finance_model->get_claim($c_id);
        $admin = $this->finance_model->finance_permissions();
        $budgets = $this->finance_model->get_budgets();
        foreach($budgets as $b){
            $budgets_list[$b['id']] = $b['budget_name'];
        }
        $files = $this->get_upload();
        $post_data = $this->input->post(NULL, TRUE);
        if($this->input->post('id') !== FALSE){
            $data = array(
                'id'=>$post_data['id'],
                'pay_to'=>$post_data['pay_to'],
                'payment_method'=>$post_data['payment_method'],
                'amount'=>substr($post_data['amount'],2)+0,            
                'item'=>$post_data['item'],
                'budget_id'=>$post_data['budget_id'],
                'details'=>str_replace('\r\n', '&#10;', $post_data['details']),
                'files'=>$old_claim['files'].$files['fn']
            );
            
            if($_POST['account-number'] != 'Hidden'){
                $data['account-number'] = $this->finance_model->encrypt_data($post_data['account-number']);
            }
            if($_POST['sort-code'] != 'Hidden'){
                $data['sort-code'] = $this->finance_model->encrypt_data($post_data['sort-code']);
            }
        }else{
            $data = $old_claim;
        }
        if($admin or (($old_claim['user_id'] === $user_id) and ($old_claim['status'] == 0))){
            $validation = $this->form_validation->run();
            if($validation){
                $this->finance_model->update_claim($data);
            }
            $this->load->view('finance/claims/edit_claim', array(
                'claim'=>$this->finance_model->get_claim($c_id),
                'admin'=>$admin,
                'budgets'=>$budgets_list,
                'validation'=>$validation,
                'type'=>'edit',
                'file_errors'=>$files['errors'],
                'page_admin'=>$this->finance_model->finance_permissions()
            ));
        }else{
            $this->index();
        }
        
    }
    
    function remove_file(){
        $user_id = $this->session->userdata('id'); 
        $c_id = $_POST['claim_id'];
        $file = $_POST['file'];
        $claim = $this->finance_model->get_claim($c_id);
        $admin = $this->finance_model->finance_permissions();
        
        if($admin or (($claim['user_id'] === $user_id) and ($claim['status'] == 0))){
            $files = explode(',', $claim['files'],-1);
            $file_list = array();
            foreach($files as $f){
                if($f != $file){
                    $file_list[] = $f;
                }
            }
            if(sizeof($file_list) > 0){
                $claim['files'] = implode(',', $file_list).',';
            }else{
                $claim['files'] = '';
            }
            return $this->finance_model->update_claim(array('id'=>$claim['id'], 'files'=>$claim['files']));
        }
        return false;
    }
    
    function load_validation(){
        
        $config = array(
            array(
                'field'   => 'pay_to', 
                'label'   => 'Pay To', 
                'rules'   => 'required'
            ),
            array(
                'field'   => 'amount', 
                'label'   => 'Amount', 
                'rules'   => 'required|callback_amount_check'
            ),
            array(
                'field'   => 'item', 
                'label'   => 'Item', 
                'rules'   => 'required'
            )                
        );
        $this->form_validation->set_rules($config);
        
    }
    
    public function amount_check($str){
        if(is_numeric(substr($str,2))){
            return true;
        }else{
            $this->form_validation->set_message('amount_check', 'The %s field must be a number.');
            return false;
        }
    }
    
    function create_group(){
    
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Group Name', 'required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('owner_id', 'Group Owner', 'required|is_natural_no_zero');
        $this->form_validation->set_message('is_natural_no_zero', 'You must select a valid name for %s.');
        $this->form_validation->set_rules('details', 'Description', 'required');
        $this->form_validation->set_rules('payment', 'Payment Details', 'required');
        
        $u_id = $this->session->userdata('id');
        if(!isset($_POST['owner'])){
            $_POST['owner'] = $this->users_model->get_full_name($u_id, false);
            $_POST['owner_id'] = $u_id;
        }
        
        if ($this->form_validation->run() == FALSE){
            $users = $this->users_model->get_all_user_ids_and_names();
            
            $budgets = $this->finance_model->get_budgets();
            $budget_list = array();
            foreach($budgets as $b){
                $budget_list[$b['id']] = $b['budget_name'];
            }

            $this->load->view('finance/invoices/create_group', array(
                'users'=>$users,
                'budgets'=>$budget_list
            ));        
        }else{
            $u_id = $this->input->post('owner_id');
            $group_name = $this->input->post('name');
            $description = $this->input->post('details');
            $payment = $this->input->post('payment');
            $g_id = $this->finance_model->create_group($u_id, $group_name, $description, $payment);
            if(is_numeric($g_id)){
                $current_user = $this->session->userdata('id');
                if($current_user === $u_id){
                    $members = array($u_id);
                }else{
                    $members = array($u_id, $current_user);
                }
                $this->finance_model->add_members($g_id, $members, 1);
                $this->load->view('finance/invoices/group_create_success');
                $this->my_group($g_id);
            }
        }
    }
    
    function edit_group(){
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        $group_id = $this->uri->segment(3);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        if(!$this->finance_model->has_permission($group_id)){
            $this->index();
            return;
        }
        
        $this->form_validation->set_rules('name', 'Group Name', 'required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('details', 'Description', 'required');
        $this->form_validation->set_rules('payment', 'Payment Details', 'required');
            
        $messages = '';    
            
        if ($this->form_validation->run() !== FALSE){
            $u_id = $this->session->userdata('id');
            $group_name = $this->input->post('name');
            $description = $this->input->post('details');
            $payment = $this->input->post('payment');
            $budget = $this->input->post('budget');
            $this->finance_model->update_group($group_id, $group_name, $description, $payment, $budget, $u_id);
            $messages = '<div class="validation_success">Your group has been updated.</div>';
        }
        $users = $this->users_model->get_all_user_ids_and_names();
        $group_info = $this->finance_model->get_group($group_id);
        
        /*$budgets = $this->finance_model->get_budgets();
        $budget_list = array();
        foreach($budgets as $b){
            $budget_list[$b['id']] = $b['budget_name'];
        }*/
        
        $this->load->view('finance/invoices/edit_group', array(
            'users'=>$users,
            'group_info'=>$group_info,
            'messages'=>$messages
        ));
    }
    
    function view_claim(){
        
        $admin = $this->finance_model->finance_permissions();
        $id = $this->uri->segment(3);
        
        $file = getcwd();
        $priv_key = $this->finance_model->get_private_key();
        
        $claim = $this->finance_model->get_claim($id);
        $claim['account-number'] = $this->finance_model->decrypt_data($claim['account-number']);
        $claim['sort-code'] = $this->finance_model->decrypt_data($claim['sort-code']);
        
        if($admin || ($u_id == $claim['user_id'])){
            $this->load->view('finance/claims/view_claim', array(
                'admin' => $admin,
                'claim' => $claim
            ));
        }else{
            $this->index();
        }
        
    }
    
    
    function remove_member(){
    
        $group_id = $this->input->post('group');
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        
        if($this->finance_model->has_permission($group_id)) {
            $this->finance_model->remove_member($group_id, $this->input->post('user'));
        }
    }

    function change_permissions(){
    
        $group_id = $this->input->post('group');
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        
        if($this->finance_model->has_permission($group_id)){
            $this->finance_model->change_permission($group_id, $this->input->post('user'), $this->input->post('new_status'));
        }
    }
    
    function view_group_totals($sent_emails=NULL){
    
        $group_id = $this->uri->segment(3);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        
        if($this->finance_model->has_permission($group_id)){
            
            $this_group = $this->finance_model->get_group($group_id);
            $invoices = $this->finance_model->get_sorted_invoices_by_group($group_id);
            $this->load->view('finance/invoices/totals', array(
                'group'=>$this_group,
                'invoices'=>$invoices,
                'sent_emails'=>$sent_emails
            ));
            
        }else{
            $this->index();
        }
    }
    
    function view_expected(){
    
        $group_id = $this->uri->segment(3);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        
        if($this->finance_model->has_permission($group_id)){
            
            $this_group = $this->finance_model->get_group($group_id);
            $invoices = $this->finance_model->get_unpaid_marked_paid($group_id);
            $this->load->view('finance/invoices/expected', array(
                'group'=>$this_group,
                'invoices'=>$invoices
            ));
            
        }else{
            $this->index();
        }
    }
    
    function show_qr_code(){
        
        if($this->finance_model->semi_permission()){
            
            if(isset($_POST['generate'])){
                if($this->finance_model->verify_password()){
                    $url = $this->finance_model->new_qr_code();
                    $this->load->view('finance/qr/code', array(
                        'url' => $url
                    ));
                    return;
                }else{
                    $GLOBALS['password-verify-qr-errors'] = 'Password Incorrect';
                }
            }
            $this->load->view('finance/qr/password');
            
        }else{
            $this->index();
        }
        
    }
    
    function authorise(){
        if($this->finance_model->semi_permission()){
            
            if(isset($_POST['authorise'])){
                if($this->finance_model->verify_code($_POST['code'])){
                    $GLOBALS['messages'] = 'You are authorised for the next 10 minutes';
                    $this->index();
                    return;
                }else{
                    $GLOBALS['authorise-verify-errors'] = 'Code Incorrect';
                }
            }
            $this->load->view('finance/qr/authorise');
            
        }else{
            $this->index();
        }
    }
    
    function remind_invoice(){
    
        $group_id = $this->uri->segment(3);
        $user_id = $this->session->userdata('id');
        $group = $this->finance_model->get_group_member($group_id, $user_id);
        
        if($this->finance_model->has_permission($group_id)){
            
            $this_group = $this->finance_model->get_group($group_id);
            $invoices = $this->finance_model->get_sorted_invoices_by_group($group_id);
            
            $this->load->model('scheduling_model');
            $subject = 'Butler JCR Finance';
            $from_email = 'butler.jcr@durham.ac.uk';
            $from_name = 'Butler JCR Finance';
            $bcc = 'Samuel.Stradling@durham.ac.uk';
            
            foreach($invoices as $i){    
                if(($i['total']-$i['paid']) > 0){
                    $to = $i['email'];
                    $nl = '<br>';
                    $message = 'Dear '.$i['name'].','.$nl.$nl;    
                    $message .= 'You have due invoices for <b>'.$this_group['budget_name'].'</b> totalling <b>&#163;'.number_format($i['total']-$i['paid'], 2).'</b>'.$nl;
                    $message .= 'To view these invoices, please visit the <a href="'.site_url('finance/my_invoices').'">butlerjcr website</a>.'.$nl.$nl;
                    $message .= 'Thank you,'.$nl.'ButlerJCR Finance'.$nl.$nl;
                    $message .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
                    $this->scheduling_model->send_email($subject, $message, $from_email, $from_name, $to, array(), $bcc);
                }
            }
            
            $this->view_group_totals(TRUE);
            
        }else{
            $this->index();
        }

    
    }
    
    
    
    function claim(){
        
        $budgets = $this->finance_model->get_budgets();
        foreach($budgets as $b){
            $budgets_list[$b['id']] = $b['budget_name'];

        }
        
        if(isset($_POST['budget_id'])){
            
            $amount = 0;
            $item = '';
            $prices = $this->input->post('price');
            $items = $this->input->post('item');
            $shops = $this->input->post('shop');
            foreach($prices as $k=>$p){
                if(is_numeric($k)){
                    $amount += (substr($prices[$k], 2)+0);
                    $item .= ($item==''?'':', ').$items[$k];
                }
            }
            
            $files = '';
            $config = array(
                'upload_path'=>'./application/views/finance/files/',
                'allowed_types'=>'jpeg|jpg|png|pdf',
                'max_size'=>8192,
                'encrypt_name'=>TRUE
            );
            $this->load->library('upload', $config);
            foreach($_FILES as $k=>$f){
                if($k !== 'upload_NUMBER'){
                    
                    if($this->upload->do_upload($k)){
                        $data = $this->upload->data();
                        if($data['file_ext'] == '.pdf'){
                            //$cmd = 'convert -density 300 '.$data['file_name'].' '.$data['raw_name'].'.png';                    
                            //shell_exec($cmd);

                            $imagick = new Imagick(); 
                            $imagick->setResolution(150, 150);
                            $imagick->readImage($data['full_path']);
                            $imagick->writeImages($data['file_path'].$data['raw_name'].'.png', false); 
                            if(file_exists($data['file_path'].$data['raw_name'].'.png')){
                                $data['file_name'] = $data['raw_name'].'.png';
                            }else{
                                $data['file_name'] = '';
                            }                    
                            $i = 0;
                            while(file_exists($data['file_path'].$data['raw_name'].'-'.$i.'.png')){
                                $data['file_name'] .= ($data['file_name']==''?'':',').$data['raw_name'].'-'.$i.'.png';
                                $i++;
                            }
                        }
                        $files .= $data['file_name'].',';
                    }
                }
            }
            
            $details = array(
                'user_id' => $this->session->userdata('id'),
                'pay_to' => $this->input->post('name'),
                'account-number' => $this->finance_model->encrypt_data($this->input->post('account-number')),
                'sort-code' => $this->finance_model->encrypt_data($this->input->post('sort-code')),
                'payment_method' => $this->input->post('claim-type')=='Cheque'?0:1,
                'amount' => $amount,
                'item' => $item,
                'budget_id' => $this->input->post('budget_id'),
                'details' => $this->input->post('details'),
                'files' => $files
            );
            
            $this->db->insert('finance_claims', $details);
            $id = $this->db->insert_id();
            foreach($prices as $k => $p){
                if(is_numeric($k)){
                    $details = array(
                        'claim_id' => $id,
                        'item_name' => $items[$k],
                        'store' => $shops[$k],
                        'amount' => substr($prices[$k],2)
                    );
                    $this->db->insert('finance_claims_items', $details);
                }
            }
            
            redirect('finance/view_claim/'.$id);
            return;
        }
            
        $this->load->view('finance/claims/new_claim', array(
            'budgets' => $budgets_list
        ));
    }

    
    
}