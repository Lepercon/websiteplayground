<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_model extends CI_Model {

    function Finance_model() {
        parent::__construct();
    }

    function get_groups() {
        $this->db->order_by('id DESC');
        return $this->db->get('finance_budgets')->result_array();
    }

    function get_members_groups($member_id) {
        if($this->finance_permissions()){
            $this->db->select('id as group_id, budget_name');
            return $this->db->get('finance_budgets')->result_array();
        }else{
            $this->db->select('id as group_id, budget_name');
            $budgets = $this->db->get('finance_budgets')->result_array();
            foreach($budgets as $k => $b){
                if(!$this->get_permissions($b['group_id'], $member_id)){
                    unset($budgets[$k]);
                }
            }
            return $budgets;
        }
    }
    
    function create_group($u_id, $group_name, $description, $payment){
        $data = array(
           'budget_name' => $group_name,
           'description' => $description,
           'how_to_pay'=>$payment
        );
        $this->db->insert('finance_budgets', $data);
        $id = $this->db->insert_id();
        
        $message = 'A new invoicing group "'.$group_name.'" has been created';
        $link = 'finance/my_group/'.$id;
        $this->add_notification(array(-1, $u_id), 'Invoices', $message, $link);
        
        return $id;
    }
    
    function update_group($group_id, $group_name, $description, $payment, $u_id){
        $this->db->where('id', $group_id);
        $data = array(
           'budget_name' => $group_name,
           'description' => $description,
           'how_to_pay'=>$payment
        );        
        $this->db->update('finance_budgets', $data); 
        
        $message = 'The group "'.$group_name.'" has been updated';
        $link = 'finance/my_group/'.$group_id;
        $this->add_notification(array(-1, $u_id), 'Invoices', $message, $link);

    }
    
    function get_group($group_id) {
        if($group_id == 'all'){
            return array(
                'budget_name' => 'All Budgets',
                'id' => 'all',
                'level_id' => '',
                'balance' => 0.00,
                'balance_date' => 0,
                'sort_order' => 1,
                'description' => '',
                'how_to_pay' => ''
            );
        }
        $this->db->select('*');
        $this->db->where('id', $group_id);
        return $this->db->get('finance_budgets')->row_array(0);
    }

    function get_invoices($member_id) {
        $this->db->where('member_id', $member_id);
        $this->db->order_by('group_id ASC, date ASC');
        return $this->db->get('invoices')->result_array();
    }

    function get_invoices_by_group($group_id) {
        $this->db->select('invoices.*, users.firstname, users.prefname, users.surname, users.email, users.current, users.custom_email');
        if($group_id != 'all'){
            $this->db->where('group_id', $group_id);
        }
        $this->db->order_by('users.surname');
        $this->db->join('users', 'users.id=invoices.member_id');
        return $this->db->get('invoices')->result_array();
    }
    
    function get_sorted_invoices_by_group($group_id){
        $invoices = $this->get_invoices_by_group($group_id);
        $invoice = array();
        foreach($invoices as $i){
            if(!isset($invoice[$i['member_id']])){
                $invoice[$i['member_id']] = array('total'=>0, 'paid'=>0);
                $invoice[$i['member_id']]['name'] = ($i['prefname']==''?$i['firstname']:$i['prefname']).' '.$i['surname'];
                $invoice[$i['member_id']]['email'] = $i['email'];
                $invoice[$i['member_id']]['current'] = $i['current'];
                $invoice[$i['member_id']]['custom_email'] = $i['custom_email'];
            }
            $invoice[$i['member_id']]['total'] += $i['amount'];
            if($i['paid']){
                $invoice[$i['member_id']]['paid'] += $i['amount'];
            }
        }
        return $invoice;

    }
    
    function get_unpaid_marked_paid($group_id){
        $this->db->select('invoices.name, invoices.amount, users.firstname, invoices.member_id, users.prefname, users.surname, users.email');
        if($group_id != 'all'){
            $this->db->where('group_id', $group_id);
        }
        $this->db->where('paid', 0);
        $this->db->where('marked_paid', 1);    
        $this->db->order_by('users.surname');
        $this->db->join('users', 'users.id=invoices.member_id');
        $invoices = $this->db->get('invoices')->result_array();
        $invoice = array();
        foreach($invoices as $i){
            $invoice[$i['member_id']][] = $i;
        }
        return $invoice;        
    }
    
    function get_sorted_invoices($trusting=FALSE){//trusting - whether to use user set values
        $trusting = $trusting?'marked_paid':'paid';
        $this->db->select('invoices.*, users.firstname, users.prefname, users.surname, users.email, users.current, users.custom_email');
        $this->db->order_by('users.surname');
        $this->db->join('users', 'users.id=invoices.member_id');
        $invoices = $this->db->get('invoices')->result_array();
        foreach($invoices as $i){
            if(!isset($invoice[$i['member_id']])){
                $invoice[$i['member_id']] = array('total'=>0, 'paid'=>0);
                $invoice[$i['member_id']]['name'] = ($i['prefname']==''?$i['firstname']:$i['prefname']).' '.$i['surname'];
                $invoice[$i['member_id']]['email'] = $i['email'];
                $invoice[$i['member_id']]['custom_email'] = $i['custom_email'];
                $invoice[$i['member_id']]['current'] = $i['current'];
            }
            $invoice[$i['member_id']]['total'] += $i['amount'];
            if($i[$trusting]){
                $invoice[$i['member_id']]['paid'] += $i['amount'];
            }
        }
        return $invoice;

    }

    function get_members($group_id) {
        $this->db->select('`finance_members`.`id`, `users`.`id` as `u_id`, `users`.`prefname`, `users`.`firstname`, `users`.`surname`, CONCAT(`users`.`prefname`, `users`.`firstname`) as `sort_name`', false);
        if($group_id != 'all'){
            $this->db->where('finance_members.group_id',$group_id);
        }
        $this->db->join('users', 'users.id = finance_members.member_id');
        $this->db->order_by('sort_name ASC');
        $this->db->group_by('users.id');
        return $this->db->get('finance_members')->result_array();
    }

    function get_group_member($group_id, $member_id) {
        $this->db->where('group_id', $group_id);
        $this->db->where('member_id', $member_id);
        return $this->db->get('finance_members')->row_array(0);
    }
    
    function remove_member($group_id, $member_id) {
        $this->db->where('group_id', $group_id);
        $this->db->where('id', $member_id);
        $this->db->where('member_id !=', $this->session->userdata('id'));
        return $this->db->delete('finance_members');
    }

    function get_invoice_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('invoices')->row_array(0);
    }
    
    function update_invoice($id, $status){
        return $this->db->update('invoices', array('marked_paid'=>$status), array('id' => $id));
    }
    
    function get_group_name($group_id){
        $this->load->helper('array');
        $this->db->select('budget_name');
        $this->db->where('id', $group_id);
        $data = $this->db->get('finance_budgets')->row_array(0);
        return element('budget_name', $data, '');
    }
    
    function add_members($group_id, $member_ids){
    
        $group_name = $this->get_group_name($group_id);
        $message = 'You have been added to the group "'.$group_name.'".';
        $link = NULL;
    
        $data = array();
        foreach($member_ids as $m){
            if(!$this->is_group_member($group_id, $m)){
                $data[] = array(
                   'member_id' => $m,
                   'group_id' => $group_id
                );
                $mids[]  = $m;
            }
        }
        if(!empty($data)){
            $this->add_notification($mids, 'Invoices', $message, $link);
            if($this->db->insert_batch('finance_members', $data)){
                return sizeof($data);
            }            
        }
        return 0;
    }
    
    function is_group_member($group_id, $u_id){
        $this->db->select('id');
        $this->db->where('member_id', $u_id);
        $this->db->where('group_id', $group_id);
        $res = $this->db->get('finance_members')->row_array(0);//php<5.5 :(
        return !empty($res);
    }
    
    function add_invoice($member_ids, $date, $name, $amount, $group_id, $details){
    
        $message = 'A new invoice has been added for "'.$name.'".';
        $link = 'finance/my_invoices';
        $this->add_notification($member_ids, 'Invoices', $message, $link);
        
        $data = array();
        foreach($member_ids as $m){
            $data[] = array(
                'date' => $date,
                'name' => $name,
                'member_id' => $m,
                'amount' => $amount,
                'group_id' => $group_id,
                'details' => $details
            );
        }
        return $this->db->insert_batch('invoices', $data);

    }

    function get_permissions($budget_id, $user_id){
    
        if($this->finance_permissions()){
            return TRUE;
        }
        if(isset($this->user_levels)){
            $levels = $this->user_levels;
        }else{
            $levels = $this->users_model->get_user_levels($user_id);
            $this->user_levels = $levels;
        }
        $budget = $this->get_budget($budget_id);
        $admin_levels = explode(',', $budget['level_id']);
        foreach($levels as $l){
            if(in_array($l['level_id'], $admin_levels)){
                return TRUE;
            }
        }
        return FALSE;
        
    }
    
    function get_total_by_group($g_id){
        $this->db->where('group_id', $g_id);
        $this->db->select_sum('amount');
        return $this->db->get('invoices')->row_array(0);
    }
    
    function get_claims(){
        $this->db->order_by('status ASC, id ASC');
        return $this->db->get('finance_claims')->result_array();
    }

    function get_claims_by_status($status){
        $this->db->select("finance_claims.*, finance_budgets.budget_name, finance_budgets.level_id, u1.firstname, u1.prefname, u1.surname, u1.current, u1.custom_email", false);
        $this->db->where('finance_claims.status', $status);
        $this->db->from('finance_claims');
        $this->db->order_by('budget_id ASC, id ASC');
        $this->db->join('finance_budgets', 'finance_budgets.id = finance_claims.budget_id');
        $this->db->join('users u1', 'u1.id = finance_claims.user_id', 'left outer');
        $claims = $this->db->get()->result_array();
        foreach($claims as $k=>$c){
            $levels = explode(',', $c['level_id']);
            $claims[$k]['owners'] = array();
            $claims[$k]['owners_ids'] = '';
            foreach($levels as $l){
                $u = $this->users_model->get_users_with_level($l, 'users.id, users.firstname, users.prefname, users.surname, users.email, users.current, users.custom_email');
                $claims[$k]['owners'] = array_merge($claims[$k]['owners'], $u);                 
            }
            foreach($claims[$k]['owners'] as $c){
                $claims[$k]['owners_ids'] = $c['id'];
            }
        }
        return $claims;
    }
    
    function get_claims_from_user($user_id){
        $this->db->select("finance_claims.*, finance_budgets.budget_name, finance_budgets.level_id, u1.firstname, u1.prefname, u1.surname", false);
        $this->db->order_by('status ASC, finance_claims.budget_id ASC');
        $this->db->where('user_id', $user_id);
        $this->db->group_by('finance_claims.id');
        $this->db->join('finance_budgets', 'finance_budgets.id = finance_claims.budget_id');
        $this->db->join('users u1', 'u1.id = finance_claims.user_id');
        $claims = $this->db->get('finance_claims')->result_array();
        foreach($claims as $k=>$c){
            $levels = explode(',', $c['level_id']);
            $claims[$k]['owners'] = array();
            foreach($levels as $l){
                $claims[$k]['owners'] = array_merge($claims[$k]['owners'], $this->users_model->get_users_with_level($l, 'users.id, users.firstname, users.prefname, users.surname')); 
            }
        }
        return $claims;
    }

    function get_claims_to_user($user_id){
        $levels = $this->users_model->get_user_levels($user_id);
        $le = array();
        foreach($levels as $l){
            $this->db->or_like('level_id', $l['level_id'], 'both');
            $le[] = $l['level_id'];
        }
        $budgets = $this->db->get('finance_budgets')->result_array();
        $bu = array();
        foreach($budgets as $k=>$b){
            $levels = explode(',', $b['level_id']);
            $match = array_intersect($levels, $le);
            if(!empty($match)){
                $bu[] = $b['id'];
            }
        }
        if(empty($bu)){
            return array();
        }
        $this->db->select('finance_claims.*, finance_budgets.budget_name, finance_budgets.level_id, u1.firstname, u1.prefname, u1.surname');
        $this->db->where_in('budget_id', $bu);
        $this->db->join('finance_budgets', 'finance_budgets.id = finance_claims.budget_id');
        $this->db->join('users u1', 'u1.id = finance_claims.user_id');
        $claims = $this->db->get('finance_claims')->result_array();
        foreach($claims as $k=>$c){
            $levels = explode(',', $c['level_id']);
            $claims[$k]['owners'] = array();
            foreach($levels as $l){
                $claims[$k]['owners'] = array_merge($claims[$k]['owners'], $this->users_model->get_users_with_level($l, 'users.id, users.firstname, users.prefname, users.surname')); 
            }
        }
        return $claims;
    }
    
    function get_claim($c_id){
        $this->db->select("finance_claims.*, finance_budgets.budget_name, finance_budgets.level_id, CONCAT(u2.firstname, ' ', u2.surname) as approver_name", false);
        $this->db->where('finance_claims.id', $c_id);
        $this->db->join('finance_budgets', 'finance_budgets.id=finance_claims.budget_id', 'left outer');
        $this->db->join('users u2', 'u2.id = finance_claims.approved_by', 'left outer');
        $c = $this->db->get('finance_claims')->row_array(0);
        $levels = explode(',', $c['level_id']);
        $c['owners'] = array();
        foreach($levels as $l){
            $c['owners'] = array_merge($c['owners'], $this->users_model->get_users_with_level($l, 'users.id, users.firstname, users.prefname, users.surname')); 
        }
        return $c;
    }
    
    function add_claim($user_id, $pay_to, $payment_method, $amount, $item, $budget, $details, $files, $account, $sortcode){
        $data = array(
            'user_id'=>$user_id,
            'pay_to'=>$pay_to,
            'payment_method'=>$payment_method,
            'amount'=>$amount,
            'item'=>$item,
            'budget_id'=>$budget['id'],
            'details'=>$details,
            'files'=>$files,
            'account-number' =>$account,
            'sort-code' => $sortcode
        );
        $d['result'] = $this->db->insert('finance_claims', $data);
        $d['id'] = $this->db->insert_id();
        
        $message = 'A new claim has been added: "'.$item.'" for "'.$pay_to.'".';
        $link = 'finance/my_claims';
        //$this->add_notification(explode(',', $budget['owner_ids']), 'Claims', $message, $link);
        $link = 'finance/view_claims';
        $this->add_notification(-1, 'Claims', $message, $link);
        
        return $d;
    }
    
    function get_budgets(){
        $this->db->select("finance_budgets.*");
        $this->db->order_by('sort_order ASC, budget_name ASC');
        $this->db->from('finance_budgets');
        $this->db->group_by('finance_budgets.id');
        $budgets = $this->db->get()->result_array();
        foreach($budgets as $k=>$b){
            $levels = explode(',', $b['level_id']);
            $budgets[$k]['levels'] = array();
            foreach($levels as $l){
                if(is_numeric($l)){
                    $le = $this->users_model->get_level($l);
                    $budgets[$k]['levels'][$le['id']] = $le['full']; 
                }
            }
        }
        return $budgets;
    }
    
    function new_admin($budget_id, $holder){
        $this->db->where('id', $budget_id);
        $b = $this->db->get('finance_budgets')->row_array(0);
        $levels = explode(',', $b['level_id']);
        $levels[] = $holder;
        $this->db->where('id', $budget_id);
        $this->db->update('finance_budgets', array('level_id'=>implode(',', $levels)));
    }

    
    function remove_admin($budget_id, $v){
        log_message('error', var_export($v, true));
        $this->db->where('id', $budget_id);
        $b = $this->db->get('finance_budgets')->row_array(0);
        $levels = explode(',', $b['level_id']);
        foreach($levels as $k=>$l){
            if($l == $v){
                unset($levels[$k]);
            }
        }
        $this->db->where('id', $budget_id);
        $this->db->update('finance_budgets', array('level_id'=>implode(',', $levels)));
    }
    
    function get_budget($budget_id){
        $this->db->select("finance_budgets.*");
        $this->db->where('finance_budgets.id', $budget_id);
        $budget = $this->db->get('finance_budgets')->row_array(0);
        $levels = explode(',', $budget['level_id']);
        $budget['owners'] = array();
        foreach($levels as $l){
            $budget['owners'] = array_merge($budget['owners'], $this->users_model->get_users_with_level($l, 'users.id, users.firstname, users.prefname, users.surname')); 
        }
        return $budget;
    }
    
    function finance_permissions(){
        
        if(isset($this->permissions)){
            return $this->permissions;
        }
        $admin_levels = $this->get_admin_levels();
        $this->permissions = (logged_in() and (has_level($admin_levels)));
        return $this->finance_permissions();
    
    }
    
    function semi_permission(){
        return $this->finance_permissions(); //ANybody who needs to use 2 factor
    }
    
    function get_admin_levels(){
        return array(1,2,4,14,118);
    }
       
    function add_budget($name, $holder_id){
        
        $message = 'A new budget has been added: "'.$name.'".';
        $link = NULL;
        $this->add_notification(array(-1, $holder_id), 'Claims', $message, $link);
    
        $data = array('budget_name'=>$name);
        $this->db->insert('finance_budgets', $data);
    }
    
    function change_claim_status($claim_id, $new_status){
    
        $claim = $this->get_claim($claim_id);
        switch($new_status){
            case 0:
                $new = 'Waiting for approval from budget holder';
                break;
            case 1:
                $new = 'Waiting for payment';
                break;
            case 2:
                $new = 'Paid';
                break;
        }
        
        $message = 'Claim "'.$claim['item'].'" has been updated to "'.$new.'".';
        $link = 'finance/my_claims';
        $claim['owners_ids'][] = $claim['user_id'];
        $this->add_notification($claim['owners_ids'], 'Claims', $message, $link);

        $link = 'finance/view_claims';
        
        
        $data['status'] = $new_status;
        if($new_status == 1){
            $data['approved_by'] = $this->session->userdata('id');
            $users = $this->users_model->get_users_with_level(array(2,4), 'users.id');
            foreach($users as $u){
                $this->add_notification($u['id'], 'Claims', 'Claim "'.$claim['item'].'" requires printing.', $link);
            }
        }
        $this->db->where('id', $claim_id);
        $this->db->update('finance_claims', $data);
    }
    
    function get_contact(){
        $users = $this->users_model->get_users_with_level(14, 'users.id, users.email, users.prefname, users.firstname, users.surname');
        $user = $users[0];
        return ($user['prefname']==''?$user['firstname']:$user['prefname']).' '.$user['surname'].' (<a href="'.(logged_in()?site_url('/contact/'.$user['id']):('mailto:'.$user['email'])).'">'.$user['email'].'</a>)';
    }
    
    function update_budget($budget_id, $budget_name){
            
        $this->db->where('id', $budget_id);
        return $this->db->update('finance_budgets', array('budget_name'=>$budget_name));
    
    }
        
    function update_claim($claim){
        
        $c = $this->get_claim($claim['id']);
        $message = 'Claim "'.$c['item'].'" has been updated.';
        $link = 'finance/my_claims';
        $c['owners_ids'][] = $c['user_id'];
        $this->add_notification($c['owners_ids'], 'Claims', $message, $link);
        $link = 'finance/view_claims';
        $this->add_notification(-1, 'Claims', $message, $link);
        
        $this->db->where('id', $claim['id']);
        return $this->db->update('finance_claims', $claim);
    }
    
    function get_notification_totals($u_id, $page_admin = false){        
        if($page_admin){
            $this->db->select('count(*) total, (SELECT COUNT(*) FROM finance_notifications WHERE is_read = 0 AND user_id IN ('.$u_id.', -1)) as unread', false);
            $this->db->where_in('user_id', array($u_id, -1));
        }else{
            $this->db->select('count(*) total, (SELECT COUNT(*) FROM finance_notifications WHERE is_read = 0 AND user_id = '.$u_id.') as unread');
            $this->db->where('user_id', $u_id);
        }
        return $this->db->get('finance_notifications')->row_array(0);
    }
    
    function get_notifications($u_id, $page_admin = false){
        if($page_admin){
            $this->db->where_in('user_id', array($u_id, -1));
        }else{
            $this->db->where('user_id', $u_id);
        }
        $this->db->order_by('time desc');
        return $this->db->get('finance_notifications')->result_array();
    }
    
    function verify_notification_owner($id, $u_id){
        $this->db->where('user_id', $u_id);
        $this->db->where('id', $id);
        return $this->db->get('finance_notifications')->num_rows() > 0;
    }
    
    function change_notification_status($id, $new_status, $u_id, $page_admin=false){
        if(!$page_admin and !$this->verify_notification_owner($id, $u_id)){
            return false;
        }
        $this->db->where('id', $id);
        $this->db->update('finance_notifications', array('is_read'=>$new_status)); 
    }
    
    function remove_notification($id, $u_id, $page_admin=false){
        if(!$page_admin and !$this->verify_notification_owner($id, $u_id)){
            return false;
        }
        $this->db->delete('finance_notifications', array('id' => $id)); 
    }
    
    function add_notification($users, $category, $message, $link){
        //$users can be array or string of comma separated integers
        //$users=-1 for admin
        $data = array();
        if(!is_array($users)){
            $users = explode(',', $users);
        }
        foreach($users as $u){
            $data[] = array(
                'time' => time(),
                'user_id' => $u,
                'category' => $category,
                'message' => $message,
                'link' => $link
            );
        }
        $this->db->insert_batch('finance_notifications', $data);
    }
    
    function get_unnotified_notifications(){
        
        $this->db->select('finance_notifications.id, finance_notifications.user_id, users.firstname, users.prefname, users.surname, users.email');
        $this->db->where('notified', 0);
        $this->db->where('is_read', 0);
        $this->db->join('users', 'users.id=finance_notifications.user_id', 'left outer');
        $data = $this->db->get('finance_notifications')->result_array();
        
        $notifications = array();
        foreach($data as $d){
            if(isset($notifications[$d['user_id']]['count'])){
                $notifications[$d['user_id']]['count']++;
            }else{
                $notifications[$d['user_id']]['count'] = 1;
                $notifications[$d['user_id']]['email'] = $d['email'];
                $notifications[$d['user_id']]['name'] = ($d['prefname'] == ''?$d['firstname']:$d['prefname']).' '.$d['surname'];
            }
        }
        
        $this->db->update('finance_notifications', array('notified'=>1));
        
        return $notifications;
    }
    
    function has_permission($budget_id){
        if($this->finance_permissions()){
            return TRUE;
        }else{
            $user_id = $this->session->userdata('id');
            return $this->get_permissions($budget_id, $user_id);
        }
    }
    
    function encrypt_data($data){
        if(empty($data)){
            return '';
        }
        $file = getcwd();
        $pub_key = file_get_contents($file.'/public.key');
        if(openssl_public_encrypt($data, $val, $pub_key)){
            return base64_encode($val);
        }
        return FALSE;
    }
    
    function decrypt_data($data){
        $priv_key = $this->get_private_key();
        if($priv_key === FALSE){
            return 'Not Authorised';
        }
        try {
            if(openssl_private_decrypt(base64_decode($data), $val, $priv_key)){
                return $val;
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        log_message('error', 'Decrypt Failed');
        return FALSE;
    }
    
    /* 2 Factor */
    function verify_password(){
        if(ENVIRONMENT == 'development'){
            return TRUE;
        }
        require_once('validate_its_user.php');
        return validate_its_user($this->session->userdata('username'), $this->input->post('password'));
    }
    
    function new_qr_code(){
        $this->load->library('PHPGangsta_GoogleAuthenticator');
        $ga = new PHPGangsta_GoogleAuthenticator();
        $sec = $this->getSecret(64);
        
        $url = $ga->getQRCodeGoogleUrl('ButlerJCR', $sec, 'Butler JCR Finance');
        
        /* Send Email */
        $this->load->library('email');
            
        $config['wordwrap'] = FALSE;
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        
        $nl = '<br>';        
        
        $mail = 'Dear JCR Webmaster,'.$nl.$nl;
        $mail .= '2 Factor Authentication has been setup by user: '.$this->session->userdata('id').'.'.$nl.$nl;
        $mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
        
        $this->email->to('butler.jcr-webmaster@durham.ac.uk');
        $this->email->from('butler.jcr-webmaster@durham.ac.uk', 'Butler JCR');
        $this->email->message($mail);
        $this->email->subject('2 Factor');
        
        if(ENVIRONMENT != 'development'){
            $this->email->send();
        }
        
        return $url;
    }
    
    function getSecret($n = 64){
        
        $key = 'FKZzJFDplqYQfxn5TTBFK58c6YKfQ45Ry1mUm+lZviNW';
        $string = $key.$this->session->userdata('id').$this->session->userdata('email').$this->session->userdata('uid').$key;
        $hash = str_split(str_repeat(strtoupper(md5($string).sha1($string).hash('ripemd160', $string).hash('sha512', $string)), $n));
        
        $base_32_chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '5', '6', '7', '=');
        $res = '';
        foreach($hash as $h){
            if(in_array($h, $base_32_chars)){
                $res .= $h;
            }
            if(strlen($res) >= $n){
                break;
            }
        }
        return substr($res, 0, $n);
    }
    
    function verify_code($code){
        
        $this->load->library('PHPGangsta_GoogleAuthenticator');
        $ga = new PHPGangsta_GoogleAuthenticator();
        
        $res = $ga->verifyCode($this->getSecret(64), $code);
        
        if($res){
            $this->session->set_userdata('finance-authorised-expire', time() + 60 * 10);//Authorise for 10 minutes
        }
        
        log_message('error', 'Authentication Attempt, User: '.$this->session->userdata('id').', Result: '.($res?'Success':'Failure'));
        
        return $res;
    }
    
    function setup_gocardless(){
        
    }
    
    
    function get_private_key(){
        if($this->finance_permissions() && ($this->session->userdata('finance-authorised-expire') > time())){
            return '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----
';
        }
        return FALSE;
    }
    
}

