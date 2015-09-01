<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alumni_model extends CI_Model {

	var $pages;

	function Alumni_model() {
		parent::__construct(); // Call the Model constructor
	}

	function get_event_info($e_id) {
		$this->db->where('event_id', $e_id);
		return $this->db->get('alumni_events')->row_array(0);
	}
	
	function alumni_permissions(){
		return logged_in() and (is_admin() or has_level(array(4)));
	}
	
	function add_signup($event_id, $num_tickets, $name, $name_at_uni, $guests, $options, $address, $email, $phone, $graduation_year, $subject, $special_requirements, $rand){
		$data = array(
			'alumni_event_id'=>$event_id,
			'number_tickets'=>$num_tickets,
			'name'=>$name,
			'name_at_uni'=>$name_at_uni,
			'guests'=>$guests,
			'options'=>$options,
			'address'=>$address,
			'email'=>$email,
			'telephone'=>$phone,
			'graduation_year'=>$graduation_year,
			'subject'=>$subject,
			'special_requirements'=>$special_requirements,
			'rand'=>$rand
		);
		$this->db->insert('alumni_signup', $data);
		return $this->db->insert_id();

	}
	
	function get_signup($id){
		$this->db->where('id', $id);
		return $this->db->get('alumni_signup')->row_array(0);
	}
	
	function scdo_contact(){
		$users = $this->users_model->get_users_with_level(4, 'users.email, users.prefname, users.firstname, users.surname');
		$user = $users[0];
		return ($user['prefname']==''?$user['firstname']:$user['prefname']).' '.$user['surname'].' (<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>)';
	}
	
	function get_signups(){
		return $this->db->get('alumni_events')->result_array();
	}
	
	function has_open_signup($id){
		$this->db->where('event_id', $id);
		$event = $this->db->get('alumni_events')->row_array(0);
		return !empty($event) && (is_null($event['signup_deadline']) || $event['signup_deadline'] > time());
	}

}