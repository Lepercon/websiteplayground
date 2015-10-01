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
    
    function get_submitted_details(){
        $re = "/(?<day>[0-9]{2})\\/(?<month>[0-9]{2})\\/(?<year>[0-9]{4})/";
        $hour = $this->input->post('hour');
        $minute = $this->input->post('minute');
        if($this->input->post('booking_start') !== FALSE){
            $start = $this->input->post('booking_start');
        }else{
            $start_date = $this->input->post('start_date');
            $start = NULL;
            if(preg_match($re, $start_date, $sdate)){
                $start = mktime ($hour, $minute, 0, $sdate['month'], $sdate['day'], $sdate['year'] );
            }
        }
        if ($this->input->post('Frequency_of_bookings') == 'No repeat'){
            $end = $start;
        }elseif($this->input->post('booking_end') !== FALSE){
            $end = $this->input->post('booking_end');
        }else{
            $end_date = $this->input->post('last_date');
            $end = NULL;
            if(preg_match($re, $end_date, $edate)){
                $end = mktime ($hour, $minute, 0, $edate['month'], $edate['day'], $edate['year'] );
            }
        }
        $details = array(
            'room_id' => $this->input->post('Rooms')==FALSE?$this->input->post('room_id'):$this->input->post('Rooms'),
            'user_id' => $this->session->userdata('id'),
            'booking_start' => $start,
            'booking_end' => $end,
            'frequency' => $this->input->post('Frequency_of_bookings'),
            'number_of_people' => $this->input->post('Number_of_People'),
            'length_min' => $this->input->post('Length'),
            'Phone_number' => $this->input->post('Phone_number'),
            'Title' => $this->input->post('Title'),
            'Equiptment' => $this->input->post('Extras'),
            'Layout' => $this->input->post('Room_Layout')
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
    }
    
    function room_id_to_name($details){
        $this ->db->where('id',$details['room_id']);
        $r = $this->db->get('bookings_rooms')->row_array();
        return $r['name'];
    }
}









