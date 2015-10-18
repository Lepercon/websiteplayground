<?php class Finance extends CI_Controller {

    function Finance() {
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
        $this->load->library('page_edit_auth');
        
        $page_admin = $this->finance_model->finance_permissions();
                
        $this->load->view('finance/finance', array(
            'page_admin'=>$page_admin
        ));
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
            $group_id = $this->uri->segment(3);
        }
        
        $user_id = $this->session->userdata('id');
        $permissions = $this->finance_model->finance_permissions();
        
        if($permissions || $this->finance_model->has_permission($group_id)){
            $this_group = $this->finance_model->get_group($group_id);
            $members = $this->finance_model->get_members($group_id);
            $invoices = $this->finance_model->get_invoices_by_group($group_id);
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
            $this->finance_model->add_notification($owners, 'Invoices', $this->session->userdata('firstname').' '.$this->session->userdata('surname').' claims to have paid for '.$invoice['name'].'.', 'finance/my_group/'.$budget['id']);
        }
        $this->output->append_output(array('success' => FALSE));
        return false;
    }

    function my_invoice(){
        $i_id = $this->uri->segment(3);
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

        $group_id = $this->uri->segment(3);
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

        $group_id = $this->uri->segment(3);
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

        $group_id = $this->uri->segment(3);
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
        $i_id = $this->uri->segment(3);
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
            }
            $this->db->update('invoices', $data);
            $this->finance_model->add_notification($invoice['member_id'], 'Invoices', $_POST['status']?'Your invoice "'.$invoice['name'].'" has been marked as paid.':'Your invoice "'.$invoice['name'].'" has been marked as unpaid.', 'finance/my_invoices');
        }else{
            log_message('error', 'Permission Denied: '.$i_id.' - '.$this->session->userdata('id'));
        }
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
    
    function sortcode(){
        $key = 'b1f9d5e9b273812925b63f5b840526ce';
        $password = 'aBcDeFgH1!';
        $sortcode = $this->input->post('sortcode');
        $account = '';
        $url = 'https://www.bankaccountchecker.com/listener.php?key='.$key.'&password='.$password.'&output=json&type=uk&sortcode='.$sortcode.'&bankaccount='.$account;
        $data = file_get_contents($url);
        $this->load->view('finance/claims/sortcode', array('data'=>$data));
    }
    
    function claims(){
        $admin = $this->finance_model->finance_permissions();
        if($admin){
            $this->view_claims();
        }else{
            $this->my_claims();
        }
    }
    
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
        
        if($admin){
            $this->load->view('finance/claims/view_claim', array(
                'admin' => $admin,
                'claim' => $claim
            ));
        }else{
            $this->index();
        }
        
    }
    
    function claim_pdf(){
    
        if($this->finance_model->finance_permissions()){
    
            $this->load->library(array('pdf', 'pdfi'));
            $this->load->helper('download');
            $this->pdfi->fontpath = 'application/font/';
            define('FPDF_FONTPATH','application/font/');
            
            $claim_id = $this->uri->segment(3);
            $claim = $this->finance_model->get_claim($claim_id);
            
            $page_width = 188;
            $page_height = 260;
            
            $this->pdfi->FPDF();
            $this->pdfi->AddPage();
            $this->pdfi->SetFont('Arial', '', 14);
            $this->pdfi->SetTextColor(0,0,0);
            
            // Write something
            $this->pdfi->Image('application\views\finance\claims/logo.png', $this->pdfi->GetX(), $this->pdfi->GetY(), $page_width/4);
            $this->pdfi->Cell($page_width/4, 60, '', 0, 0);
            $this->pdfi->SetFont('Arial', '', 30);
            $this->pdfi->Cell($page_width/2, 60, 'JCR Claims Form', 0, 0, 'C');
            $this->pdfi->Image('application\views\finance\claims/logo.png', $this->pdfi->GetX(), $this->pdfi->GetY(), $page_width/4);
            $this->pdfi->Cell($page_width/4, 60, '', 0, 1);
            $this->pdfi->Ln(10);
            
            $this->pdfi->SetFont('Arial', '', 14);
            $this->pdfi->Cell($page_width/2, 7, 'Pay: '.$claim['pay_to'], 1, 0);
            $this->pdfi->Cell($page_width/2, 7, 'The Sum Of: '.chr(163).$claim['amount'], 1, 1);
            $this->pdfi->Cell($page_width, 7, 'Item: '.$claim['item'], 1, 1);
            $this->pdfi->Cell($page_width/2, 7, 'Budget: '.$claim['budget_name'], 1, 0);
            $this->pdfi->Cell($page_width/2, 7, 'Budget Holder: '.($claim['prefname']==''?$claim['firstname']:$claim['prefname']).' '.$claim['surname'], 1, 1);
            $this->pdfi->Ln(10);
            
            $this->pdfi->SetFont('Arial', 'B', 24);
            $this->pdfi->Write(6, 'Details:');
            $this->pdfi->Ln(10);
            $this->pdfi->SetFont('Arial', '', 12);
            $this->pdfi->Write(6, $claim['details']);
            
            $this->pdfi->SetY(-77);
            $this->pdfi->SetFont('Arial','I',8);
            $this->pdfi->Write(6, 'For JCR Treasurer Use:');
            $this->pdfi->Ln(5);
            $this->pdfi->SetFont('Arial','',12);
            $this->pdfi->Cell($page_width/2, 7, 'Paid On: ', 0, 0, 'R');
            $this->pdfi->Cell($page_width/2, 7, '', 1, 1);
            $this->pdfi->Cell($page_width/2, 7, 'Cheque Number: ', 0, 0, 'R');
            $this->pdfi->Cell($page_width/2, 7, '', 1, 1);
            
            $this->pdfi->Cell($page_width/3, 30, '', 1, 0);
            $this->pdfi->Cell($page_width/3, 30, '', 1, 0);
            $this->pdfi->Cell($page_width/3, 30, '', 1, 1);
            $this->pdfi->Cell($page_width/3, 7, 'JCR President', 1, 0, 'C');
            $this->pdfi->Cell($page_width/3, 7, 'JCR Treasurer', 1, 0, 'C');
            $this->pdfi->Cell($page_width/3, 7, 'College Bursar', 1, 1, 'C');
            
            $files = explode(',', $claim['files'],-1);
            $n = 1;
            $xmin = $this->pdfi->GetX();
            foreach($files as $f){
                if(strpos($f, '.pdf') === FALSE){
                    $this->pdfi->AddPage();
                    $size = getimagesize('application/views/finance/files/'.$f);
                    if($size[0]/$size[1] > 1/sqrt(2)){
                        if($size[0] > 700){
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10, -$size[0] * 25.4/$page_width);
                        }else{
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10);
                        }
                    }else{
                        if($size[1] > 900){
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10, -$size[1] * 25.4/$page_height, -$size[1] * 25.4/$page_height);
                        }else{
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10);
                        }
                    }
                }else{
                    $pagecount = $this->pdfi->setSourceFile('application/views/finance/files/'.$f);  
                    for($i=0; $i<$pagecount; $i++){
                        $this->pdfi->AddPage();
                        $tplidx = $this->pdfi->importPage($i+1, '/MediaBox');
                        $this->pdfi->useTemplate($tplidx, 10, 10, 200); 
                    }
                }                
            }
                        
            // Output
            $filename = VIEW_PATH.'finance/claims/temp_pdf/claim_'.$claim_id.'.pdf';
            $this->pdfi->Output($filename, 'F');
            $this->output->set_content_type('application/pdf');
            $data = file_get_contents($filename);
            force_download('claim_'.$claim_id.'.pdf', $data);
        
        }
    }
    
    function view_notifications(){
        
        $u_id = $this->session->userdata('id');
        $admin = $this->finance_model->finance_permissions();
        $notifications = $this->finance_model->get_notifications($u_id, $admin);
        
        $this->load->view('finance/notifications/view', array(
            'notifications' => $notifications
        ));
    
    }
    
    function notifications(){
        //ajax requests
        
        $u_id = $this->session->userdata('id');
        $change = $this->uri->segment(3);
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

/* End of file finance.php */
/* Location: ./application/controllers/finance.php */
