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
            $re = "/(?P<day>[0-9]{2})\\/(?P<month>[0-9]{2})\\/(?P<year>[0-9]{4})/";
            $s_hour = $this->input->post('s_hour');
            $s_min = $this->input->post('s_minute');
            $start = NULL;
            if(preg_match($re, $this->input->post('start_date'), $s_date)){
                $start = $this->epoch_convert($s_hour, $s_min, $s_date);
            }
            $e_hour = $this->input->post('e_hour');
            $e_min = $this->input->post('e_minute');
            if ($this->input->post('Frequency_of_bookings') == 0){
                    preg_match($re, $this->input->post('start_date'), $e_date);
                    $end = $this->epoch_convert($e_hour, $e_min, $e_date);
            }else{
                $end = NULL;
                if(preg_match($re, $this->input->post('last_date'), $e_date)){
                    $end = $this->epoch_convert($e_hour, $e_min, $e_date);
                }
            }
            $equipt = $this->input->post('equipt')!==FALSE?$this->input->post('equipt'):array();
            $equiptment = implode(', ', array_keys($equipt));
            $details = array(
                    'user_id' => $this->session->userdata('id'),
                    'room_id' => $this->input->post('room_id'),
                    'booking_start' => $start,
                    'booking_end' => $end,
                    'frequency' => $this->input->post('Frequency_of_bookings'),
                    'number_of_people' => $this->input->post('Number_of_People'),
                    'Phone_number' => $this->input->post('Phone_number'),
                    'Title' => $this->input->post('Title'),
                    'Equiptment' => $equiptment,
                    'Layout' => $this->input->post('Room_Layout')==FALSE?' ':$this->input->post('Room_Layout'),
            );
            return $details;	
	}
	
	function get_room_id(){
		$details = array('room_id' => $this->input->post('room_id'));
		return $details;
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
        
        function euiptment_id_2_name($id) {
            $this ->db->where('id',$id);
            $equipment = $this->db->get('bookings_equiptment')->row_array();
            return $equipment['e_name'];
        }
	
	function send_email($message, $details){
		$room = $this->room_id_to_name($details);
		$this->load->library('email');
                $config['mailtype'] = 'html';
                $config['wordwrap'] = FALSE;    
                $this->email->initialize($config);
		$this->email->from($this->session->userdata('email'), $this->session->userdata('firstname').' '.$this->session->userdata('surname'));
		$this->email->to('butler.reception@durham.ac.uk'); 
		//$this->email->cc('a.j.naylor@durham.ac.uk'); 
		//$this->email->bcc('a.j.naylor@durham.ac.uk');
		$this->email->subject('Room booking - '.$room.date(' - l',$details['booking_start']));
		$this->email->message($message); 
		$this->email->send();
	}
        


        function room_name2id($name){
            
            $this->db->like('spreadsheet_name', $name, 'none');
            $this->db->or_like('name', $name, 'none');
            $room = $this->db->get('bookings_rooms')->row_array(0);
            if(!empty($room))
                return $room['id'];
            return NULL;
        
    }
}









