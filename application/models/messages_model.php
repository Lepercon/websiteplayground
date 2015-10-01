<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages_model extends CI_Model {


    function Messages_model() {
        parent::__construct(); // Call the Model constructor
    }
    
    function get_messages(){
        $this->db->select('events.name, messages.message');
        $this->db->from('messages');
        $this->db->where('expiry >', time());
        $this->db->or_where('expiry is null');
        $this->db->order_by('order asc, messages.id asc');
        $this->db->join('events', 'messages.event_id=events.id', 'left outer');
        return $this->db->get()->result_array();
    }
    
    function get_all_messages(){
        $this->db->from('messages');
        $this->db->order_by('id asc');
        $this->db->where('expiry >', time());
        $this->db->or_where('expiry is null');
        return $this->db->get()->result_array();
    }
    
    function update_messages($data){
        $this->db->empty_table('messages');
        $this->db->insert_batch('messages', $data);
    }
    
    function new_message($message){
        $this->db->set('message', $message);
        $this->db->insert('messages');
        return $this->db->insert_id();
    }

}