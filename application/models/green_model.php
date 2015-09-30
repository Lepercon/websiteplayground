<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Green_model extends CI_Model {

    function Green_model() {
        parent::__construct();
    }

    function get_tip() {
        $this->db->select('tip');
        $this->db->where('day', date('d'));
        $this->db->where('month', date('m'));
        $this->db->limit(1);
        $tip = $this->db->get('green_tips')->row_array();
        if(empty($tip)) return FALSE;
        else return $tip['tip'];
    }

    function get_tips() {
        $this->db->order_by('month ASC, day ASC');
        return $this->db->get('green_tips')->result_array();
    }

    function add_tip() {
        $date = explode("/",$this->input->post('date'));
        $submit['tip'] = $this->input->post('tip');
        $submit['day'] = $date[0];
        $submit['month'] = $date[1];
        $submit['added'] = time();
        $submit['added_by'] = $this->session->userdata('id');
        $this->db->set($submit);
        $this->db->insert('green_tips');
    }

    function delete_tip($tip_id) {
        $this->db->where('id', $tip_id);
        $this->db->delete('green_tips');
    }
}