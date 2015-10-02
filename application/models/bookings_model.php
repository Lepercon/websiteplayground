<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bookings_model extends CI_Model {

	function Bookings_model() {
		parent::__construct(); // Call the Model constructor
	}
	
	function get_rooms(){
		return $this->db->get('bookings_rooms')->result_array();
	}
	
	function get_layouts(){
		return $this->db->get('bookings_layout')->result_array();
	}
	
	function get_equiptment(){
		return $this->db->get('bookings_equiptment')->result_array();
	}

	
	function get_bookings(){
		$this->db->join('bookings_rooms', 'bookings_rooms.id = bookings_reservations.room_id');
		return $this->db->get('bookings_reservations')->result_array();
	}
	
	function get_booking($id){
		$this->db->where('bookings_reservations.id', $id);
		$this->db->join('bookings_rooms', 'bookings_rooms.id = bookings_reservations.room_id');
		return $this->db->get('bookings_reservations')->row_array();

	}
	
	function epoch_convert($hour, $min, $date){
		$epoch = mktime ($hour, $min, 0, $date['month'], $date['day'], $date['year'] );
		return $epoch;
	}
	
	function get_submitted_details(){
		$re = "/(?<day>[0-9]{2})\\/(?<month>[0-9]{2})\\/(?<year>[0-9]{4})/";
		$hour = $this->input->post('s_hour');
		$min = $this->input->post('s_minute');
		if($this->input->post('booking_start') !== FALSE){
			$start = $this->input->post('booking_start');
		}else{
			$this->input->post('start_date');
			$start = NULL;
			if(preg_match($re, $this->input->post('start_date'), $date)){
				preg_match($re, $this->input->post('start_date'), $date);
				$start = $this->epoch_convert($hour, $min, $date);
			}
		}
		$hour = $this->input->post('e_hour');
		$min = $this->input->post('e_minute');
		if ($this->input->post('Frequency_of_bookings') == 0){
			preg_match($re, $this->input->post('start_date'), $date);
			 $end = $this->epoch_convert($hour, $min, $date);
		}elseif($this->input->post('booking_end') !== FALSE){
			$end = $this->input->post('booking_end');
		}else{
			$end = NULL;
			if(preg_match($re, $this->input->post('last_date'), $date)){
				preg_match($re, $this->input->post('last_date'), $date);
				$end = $this->epoch_convert($hour, $min, $date);
			}
		}
		$details = array(
			'room_id' => $this->input->post('room_id'),
			'booking_start' => $start,
			'booking_end' => $end,
			'frequency' => $this->input->post('Frequency_of_bookings'),
			'number_of_people' => $this->input->post('Number_of_People'),
			'Phone_number' => $this->input->post('Phone_number'),
			'Title' => $this->input->post('Title')
		);
		return $details;	
	}
	
	function get_room_id(){
		$details = array('room_id' => $this->input->post('room_id'));
		return $details;
	}
	
	function input_equipt_layout(){
		$equiptment = array();
		$equipt = $this->input->post('equipt');
		array_keys($equipt);
		$equiptment = implode(', ', $equipt);
		$details = array(
						'room_id' => $this->input->post('room_id'),
						'Equiptment' => $equiptment,
						'Layout' => $this->input->post('Room_Layout')==FALSE?' ':$this->input->post('Room_Layout'),
						'booking_start' => $this->input->post('booking_start'),
						'booking_end' => $this->input->post('booking_end'),
						'frequency' => $this->input->post('frequency'),
						'number_of_people' => $this->input->post('number_of_people'),
						'Phone_number' => $this->input->post('Phone_number'),
						'Title' => $this->input->post('Title')
						);
		return $details;
	}
	
	function confirm_details(){
		$details = array(
			'room_id' => $this->input->post('room_id'),
			'user_id' => $this->session->userdata('id'),
			'booking_start' => $this->input->post('booking_start'),
			'booking_end' => $this->input->post('booking_end'),
			'frequency' => $this->input->post('frequency'),
			'number_of_people' => $this->input->post('number_of_people'),
			'Phone_number' => $this->input->post('Phone_number'),
			'Title' => $this->input->post('Title'),
			'Equiptment' => $this->input->post('Equiptment'),
			'Layout' => $this->input->post('Layout')
		);
		return $details;
	}

	function error_check($details){
		$error = array();
		if ($details['booking_start'] > $details['booking_end']){
			$error[] = 'Last date is not after start date';
		}
		return $error;
	}
	
	function enter_data($details){
		$this->db->insert('bookings_reservations', $details);
		$id = $this->db->insert_id();
		
		$times = array();
		switch($details['frequency']){
			case 0:
				$times = array($details['booking_start']);
				break;
			case 1:
				for($t = $details['booking_start']; $t <= $details['booking_end']; $t+=60*60*24*7){
					$times[] = $t;
				}
				break;
			case 2:
				for($t = $details['booking_start']; $t <= $details['booking_end']; $t+=60*60*24*14){
					$times[] = $t;
				}
				break;
			case 3:
				for($t = $details['booking_start']; $t <= $details['booking_end']; $t+=60*60*24*28){
					$times[] = $t;
				}
				break;
		}
		$booking_len = (($details['booking_end'] - $details['booking_start']) % (60*60*24));
		$instance = array();
		foreach ($times as $t){
			$end_t = $t + $booking_len;
			$instance[] = array('time_start' => $t,
							'time_end' => $end_t,
							'booking_id'=> $id,
							'room_id' => $details['room_id']
							);
		}
		$this->db->insert_batch('bookings_instances', $instance);
		return $id;
	}
	
	function room_id_to_name($details){
		$this ->db->where('id',$details['room_id']);
		$r = $this->db->get('bookings_rooms')->row_array();
		return $r['name'];
	}
	
	function send_email($message, $details){
		$room = $this->room_id_to_name($details);
		$this->load->library('email');
		$this->email->from($this->session->userdata('email'), $this->session->userdata('firstname').' '.$this->session->userdata('surname'));
		$this->email->to('a.j.naylor@durham.ac.uk'); 
		//$this->email->cc('a.j.naylor@durham.ac.uk'); 
		//$this->email->bcc('them@their-example.com'); 
		$this->email->subject('Room booking - '.$room.date(' - l',$details['booking_start']));
		$this->email->message($message); 
		$this->email->send();
	}
}









