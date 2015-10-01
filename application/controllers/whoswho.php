<?php

class Whoswho extends CI_Controller {
    
    var $pages;
    
    function Whoswho() {
        parent::__construct();
        $this->pages = array('exec', 'assistants', 'sports', 'societies', 'committees', 'support', 'services', 'staff');
    }
    
    function index() {

        $page = $this->get_page();
        $this->load->library('page_edit_auth');
        
        $member = $this->uri->rsegment(4, '');
        $title = $this->users_model->get_levels_of_user($member, TRUE, ', ', FALSE);
        if($title == ''){
            $mem = '';
        }else{
            $mem = $this->users_model->get_users($member, 'id, uid, firstname, prefname, surname, level_desc');
            $mem['title'] = $title;
            $mem['roles'] = $this->users_model->get_user_levels($mem['id']);
        }

        if($this->input->post('profile') !== FALSE){
            $this->load->view('whoswho/users', array('mem' => $mem));
        }else{
            $this->load->view('whoswho/whoswho', array(
                'page' => $page, 
                'all_whoswho' => $this->all_whoswho($page),
                'access_rights' => $this->page_edit_auth->authenticate('whoswho'),
                'mem' => $mem
            ));
        }
    }
    
    function print_profiles() {

        $page = $this->get_page();
        $this->load->library('page_edit_auth');
        
        $member = $this->uri->rsegment(4, '');
        $title = $this->users_model->get_levels_of_user($member, TRUE, ', ', FALSE);
        

        $this->load->view('whoswho/whoswho_print', array(
            'page' => $page, 
            'all_whoswho' => $this->users_model->get_users_on_page($page),
            'access_rights' => $this->page_edit_auth->authenticate('whoswho')
        ));
    }
    
    function get_page() {
        $page = $this->uri->rsegment(3, 'exec');
        if(!in_array($page, $this->pages)) $page = 'exec';
        return $page;
    }
    
    function mem() {
        $page = $this->get_page();
        $member = $this->uri->rsegment(4, '');
        $title = $this->users_model->get_levels_of_user($member, TRUE, ', ', FALSE);
        if($title == '') $mem = '';
        else {
            $mem = $this->users_model->get_users($member, 'id, uid, firstname, prefname, surname, level_desc');
            $mem['title'] = $title;
            $mem['roles'] = $this->users_model->get_user_levels($mem['id']);
        }

        if($this->input->is_ajax_request()) $this->load->view('whoswho/users', array('mem' => $mem));
        else $this->load->view('whoswho/whoswho', array('mem' => $mem, 'all_whoswho' => $this->all_whoswho($page), 'page' => $page, 'access_rights' => $this->page_edit_auth->authenticate('whoswho')));
    }
    
    function save_order() {
        $page = $this->get_page();
        if(!is_admin() OR empty($_POST['order'])) return;
        $this->db->select('id');
        $this->db->where('type', $page);
        $levels = $this->db->get('levels')->result_array();
        $level_list = array();
        foreach($levels as $l) {
            $level_list[] = $l['id'];
        }

        foreach($_POST['order'] as $k => $u) {
            $this->db->where('user_id', $u);
            $this->db->where_in('level_id', $level_list);
            $this->db->set('order', $k);
            $this->db->update('level_list');
        }
    }

    private function all_whoswho($page) {
        $this->db->select('users.id, firstname, prefname, surname, email, uid, level_desc, levels.full');
        $this->db->from('users');
        $this->db->join('level_list', 'users.id = level_list.user_id AND level_list.current=1', 'inner');
        $this->db->join('levels', 'levels.id = level_list.level_id', 'inner');
        $this->db->where('levels.type', $page);
        $this->db->order_by('level_list.order ASC, users.surname ASC');
        $whoswho = $this->db->get()->result_array();
        $processed = array();
        foreach($whoswho as $k => $v) {
            if(in_array($v['id'], $processed)) {
                unset($whoswho[$k]);
            } else {
                $processed[] = $v['id'];
            }
        }
        $all_whoswho = array();
        foreach($whoswho as $mem) {
            $mem['title'] = $this->users_model->get_levels_of_user($mem['id'], TRUE);
            $all_whoswho[] = $mem;
        }
        return $all_whoswho;
    }
    
    function history(){
        
        $roles = $this->users_model->get_all_roles();
        $cr = $this->uri->rsegment(3);
        
        $this->load->view('whoswho/banner');
        $this->load->view('whoswho/history', array(
            'roles'=>$roles,
            'cr'=>$cr
        ));
    }
    
    function awards(){
        
        $awards = $this->users_model->get_all_awards();
        
        $this->load->view('whoswho/banner');
        $this->load->view('whoswho/awards', array(
            'awards'=>$awards
        ));
    }
    
    function feedback(){
        
        if(logged_in()){
            $this->load->library('email');
            
            $config['wordwrap'] = FALSE;
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            
            $nl = '<br>';        
            
            $mail = 'Dear JCR President,'.$nl.$nl;
            $mail .= 'We have received feedback regarding the whoswho page at butlerjcr.com.'.$nl.$nl;
            $mail .= 'Page: '.$_POST['page'].$nl.'Comments:'.$nl.'<b>';
            $mail .= nl2br($_POST['text']).'</b>'.$nl.$nl;
            $mail .= '<i>ID: '.$this->session->userdata('uid').'</i>';
            $mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
            
            $this->email->to('butler.jcr@durham.ac.uk');
            $this->email->bcc('samuel.stradling@durham.ac.uk');
            $this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
            $this->email->message($mail);
            $this->email->subject('JCR Feedback');
            
            if(ENVIRONMENT != 'development'){
                $this->email->send();
            }
        }        
        
    }    
}

/* End of file whoswho.php */
/* Location: ./application/controllers/whoswho.php */