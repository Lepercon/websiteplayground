<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

	function Users_model()
	{
		parent::__construct();
	}

	function get_users($ids, $select = '', $limit = null, $offset = 0, $order = 'firstname ASC, surname ASC', $current = FALSE)
	{
		if($ids == 'all') {
			if(!empty($select)) $this->db->select('id, '.$select);
			if(is_int($limit)) $this->db->limit($limit, $offset);
			else if(is_string($limit)) $this->db->like('firstname', $limit, 'after');
			if($current) $this->db->where('current', '1');
			$this->db->order_by($order);
			return $this->db->get('users')->result_array();
		}
		if(!is_array($ids)) $ids = array($ids);
		if(count($ids) == 0) return FALSE;
		$return = array();
		foreach($ids as $id) {
			$this->db->where('id', $id);
			if(!empty($select)) $this->db->select('id, '.$select);
			$tmp = $this->db->get('users')->row_array(0);
			if(!empty($tmp)) $return[] = $tmp;
		}
		if(count($return) == 0) return FALSE;
		if(count($return) == 1) return current($return);
		return $return;
	}

	function get_full_name($id, $pref_name=true)
	{
		$names = $this->get_users($id, 'firstname, prefname, surname');
		if($names === FALSE) return FALSE;
		if($pref_name){
			return user_pref_name($names['firstname'], $names['prefname'], $names['surname']);
		}else{
			return $names['firstname'].' '.$names['surname'];
		}
	}

	function get_all_user_ids_and_names($alumni = FALSE)
	{
		$this->db->select('id, CONCAT(firstname, \' \', surname) as name', FALSE);
		if(!$alumni){
			$this->db->where('current', 1);
		}
		$this->db->order_by('firstname ASC, surname ASC');
		return $this->db->get('users')->result_array();
	}

	function get_level_desc()
	{
		$this->db->select('level_desc');
		$this->db->where('id', $this->session->userdata('id'));
		return return_array_value('level_desc', $this->db->get('users')->row_array(0));
	}
	
	function get_user_levels($u_id)
	{
		$this->db->select('level_id, year, full, description');
		$this->db->where('user_id', $u_id);
		$this->db->join('levels', 'levels.id=level_list.level_id');
		$this->db->order_by('year DESC, full');
		return $this->db->get('level_list')->result_array();
	}

	function get_level($level_id)
	{
		$this->db->where('id', $level_id);
		return $this->db->get('levels')->row_array(0);
	}
	
	function update_level_description($level_id, $desc){
		$data = array('description'=>$desc);
		$this->db->where('id', $level_id);
		$this->db->update('levels', $data); 
	}

	function get_mobile()
	{
		$this->db->select('mobile');
		$this->db->where('id', $this->session->userdata('id'));
		return return_array_value('mobile', $this->db->get('users')->row_array(0));
	}

	function get_gym_availability()
	{
		$this->db->select('availability');
		$this->db->where('id', $this->session->userdata('id'));
		return return_array_value('availability', $this->db->get('users')->row_array(0));
	}

	function get_levels_of_user($user_id = NULL, $full_names = TRUE, $join = ', ', $current_only=TRUE)
	{
		if(is_null($user_id)) return NULL;
		$this->db->select('levels.id as id, levels.full as full, level_list.year as year');
		$this->db->from('levels');
		$this->db->order_by('year DESC');
		$this->db->join('level_list', 'level_list.level_id = levels.id', 'inner');
		$this->db->where('level_list.user_id', $user_id);
        if($current_only){
            $this->db->where('level_list.current', 1);
        }
		$levels = $this->db->get()->result_array();
		if(!empty($levels)) {
			$return = array();
			$select = ($full_names ? 'full' : 'id');
			foreach($levels as $l) {
				$return[] = $l[$select];
			}
			return implode($join, $return);
		}
		else return NULL;
	}

	function convert_string_level_to_id($level)
	{
		if(is_numeric($level)) return intval($level);
		$this->db->select('id');
		$this->db->where('full', $level);
		return return_array_value('id', $this->db->get('levels')->row_array(0));
	}

	function get_users_with_level($lev_id, $select = 'users.id', $current=TRUE)
	{
		$this->db->select($select, false);
		$this->db->from('users');
        $this->db->order_by('level_list.year DESC, users.surname ASC');
		$this->db->join('level_list', 'level_list.user_id = users.id', 'inner');
		$this->db->join('levels', 'level_list.level_id = levels.id', 'inner');
        if(is_array($lev_id)){
            foreach($lev_id as $l){
                $this->db->or_where('level_list.level_id', $l);
                if($current){
					$this->db->where('level_list.current', 1);
				}
            }
        }else{
        	if($current){
				$this->db->where('level_list.current', 1);
			}
            $this->db->where('level_list.level_id', $lev_id);
        }
        $q = $this->db->get()->result_array();
        //log_message('error', $this->db->last_query());
		return $q;
	}

	function get_exec_contact($lev_id = NULL, $display_title = TRUE, $display_email = FALSE)
	{
		if($lev_id == NULL) return FALSE;
		$string = '';
		$users = $this->get_users_with_level($lev_id, 'users.id, firstname, prefname, surname, email');
		if($display_title) {
			$this->db->select('full');
			$this->db->where('id', $lev_id);
			$lv = return_array_value('full', $this->db->get('levels')->row_array(0));
			$string .= $lv.(count($users) > 1 ? 's':'').', ';
		}
		$c = count($users);
		foreach($users as $k => $u) {
			if($k == $c - 1) {
				if($c > 1) $string .= ' or '; // the last in array bigger than one person, echo an or
			}
			else if($k != 0) {
				$string .= ', '; // not the first in array and not the last, echo a comma
			}
			$email_link = ($display_email ? email_link($u['id'], $u['email']) : email_link($u['id']));
			$string .= user_pref_name($u['firstname'],$u['prefname'],$u['surname']).' ('.$email_link.')';
		}
		return $string;
	}
	
	function change_password(){//for alumni only, used when temporary password is changed
		
		$this->load->model('admin_model');
		$salt = $this->admin_model->get_salt();
		
		$this->db->where('username', $this->session->userdata('username'));
		$this->db->update('users', array('password_hash'=>crypt($_POST['password'], $salt), 'temporary_password'=>0, 'custom_email'=>$_POST['email']));
		
		$this->db->where('username', $this->session->userdata('username'));
		$data = $this->db->get('users')->row_array(0);
		$this->session->set_userdata($data);
		$this->send_email();
		return $data;
	}
	
	function update_email(){
		$this->db->where('username', $this->session->userdata('username'));
		$this->db->update('users', array('custom_email'=>$_POST['email'], 'confirmed_email'=>0));
		$this->db->where('username', $this->session->userdata('username'));
		$data = $this->db->get('users')->row_array(0);
		$this->session->set_userdata($data);
		$this->send_email();
	}
	
	function send_email(){
		
		$this->load->library('email');
			
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		$hash = $this->get_hash();
		$url = site_url('confirm_email/'.$this->session->userdata('id').'/'.$hash);
		
		$nl = '<br>';		
		
		$mail = 'Dear '.($this->session->userdata('prefname')==''?$this->session->userdata('firstname'):$this->session->userdata('prefname')).' '.$this->session->userdata('surname').','.$nl.$nl;
		$mail .= 'We have received a request to activate this email address for the Butler JCR account with the username: <b>'.$this->session->userdata('username').'</b>'.$nl.$nl;
		$mail .= 'If this was you please click the following link:'.$nl;
		$mail .= '<a href="'.$url.'">'.$url.'</a>'.$nl.$nl;
		$mail .= 'If you did not send this request, please forward this email to butler.jcr@durham.ac.uk'.$nl.$nl;
		$mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
		
		$this->email->to($this->session->userdata('custom_email'));
		$this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
		$this->email->message($mail);
		$this->email->subject('Butler JCR: Email Confirmation');
		
		if(ENVIRONMENT != 'local'){
			$this->email->send();
		}else{
            log_message('error', 'Email: '.$this->email->print_debugger());
        }

	}
	
	function get_hash($id = NULL){
		if(is_null($id)){
			$id = $this->session->userdata('id');
			$username = $this->session->userdata('username');
			$email = $this->session->userdata('custom_email');
		}else{
			$this->db->where('id', $id);
			$data = $this->db->get('users')->row_array(0);
			$username = $data['username'];
			$email = $data['custom_email'];
		}
		$rand = '8hlZo1B7GGN0134YU32jI4T33gBJHB';
		return md5($id.$username.$email.$rand);
	}

	function remove_email(){
		$this->db->where('username', $this->session->userdata('username'));
		$this->db->update('users', array('custom_email'=>'', 'confirmed_email'=>0));
		$this->db->where('username', $this->session->userdata('username'));
		$data = $this->db->get('users')->row_array(0);
		$this->session->set_userdata($data);
	}
	
	function confirm_email(){
		$id = $this->uri->segment(2);
		$hash = $this->get_hash($id);
		if($hash === $this->uri->segment(3)){
			$this->db->where('id', $id);
			$this->db->update('users', array('confirmed_email'=>1));
			$this->update_session();
			return TRUE;
		}	
		return FALSE;	
	}
	
	function update_session(){
		if(logged_in()){
			$this->db->where('username', $this->session->userdata('username'));
			$data = $this->db->get('users')->row_array(0);
			$this->session->set_userdata($data);
		}
	}
	
	function get_all_roles(){
		$this->db->select('levels.full, levels.description, levels.type, level_list.year, level_list.level_id, users.uid, users.surname, users.firstname, users.prefname, users.email, users.level_desc');
		$this->db->join('level_list', 'level_list.level_id=levels.id');
		$this->db->join('users', 'users.id=level_list.user_id');
		$this->db->order_by('levels.type, levels.full, level_list.year DESC, users.surname, users.firstname');
		$levels = $this->db->get('levels')->result_array();
		$lev = array();
		foreach($levels as $l){
			$lev[$l['type']][$l['level_id']][] = $l;
		}
		return $lev;
	}
	
	function get_all_levels(){
		$this->db->select('levels.id, levels.full');
		$this->db->order_by('full');
		$levels = $this->db->get('levels')->result_array();
		foreach($levels as $l){
			$lev[$l['id']] = $l['full'];
		}
		return $lev;
	}
	
	function get_all_session_data($user_id){
		$this->db->where('id', $user_id);
		$user = $this->db->get('users')->row_array(0);
		$this->db->where('user_id', $user_id);
		$this->db->join('levels', 'levels.id=level_list.level_id');
		$this->db->select('GROUP_CONCAT(level_id) as lid, SUM(full_access) as fa');
		$this->db->group_by('user_id');
		$l = $this->db->get('level_list')->row_array(0);
		$user['level_list'] = $l['lid'];
		$user['logged_in'] = 1;
		$user['full_access'] = ($l['fa'] > 0)?1:0;
		return $user;
	}
	
	function get_all_users($select){
		$this->db->select($select);
		return $this->db->get('users')->result_array();
	}
	
	function get_all_awards(){
		return array();
	}   
	
}