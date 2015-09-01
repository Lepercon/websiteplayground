<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class welfare_model extends CI_Model {
	
	function add_welfare_notification() {
		parent::__construct();
		$data = array(
		   'userid' => $this->session->userdata('id') ,
		   'anonymous_code' => $_POST['anon_code']
		);
	
		$this->db->insert('welfare_requests', $data); 
	}
	
	function get_notifications() {
		$this->db->select('anonymous_code');
		$query = $this->db->get('welfare_requests');
		return $query;
	}
	
	function send_notification() {
		$this->db->select('userid');
		$this->db->where('anonymous_code', $_POST['anonymouscode']);
		$query = $this->db->get('welfare_requests')->row_array();
		$who = $query['userid'];
		
		$this->db->select('email');
		$this->db->where('id', $who);
		$query = $this->db->get('users')->row_array();
		$email = $query['email'];
		
		$mail = 'Your welfare delivery is ready for collection.';
		$mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
		
		$this->load->library('email');
		
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		$this->email->to($email);
		$this->email->from(WELFARE_EMAIL, 'Supplies request form');
		$this->email->message($mail);
		$this->email->subject('Supplies request from the Butler JCR website');
		if(ENVIRONMENT != 'local'){
			$this->email->send();
		}else{
            log_message('error', 'Email: '.$this->email->print_debugger());
        }
	}
	
	function delete_notification() {
		$this->db->where('anonymous_code', $_POST['anonymouscode']);
		$this->db->delete('welfare_requests');
	}
}
