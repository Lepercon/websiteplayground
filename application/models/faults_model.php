<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faults_model extends CI_Model {

    function Faults_model() {
        parent::__construct();
    }

    function get_faults() {
        $this->db->select('users.firstname, users.prefname, users.surname, faults.id, faults.reporter, faults.reported, faults.location, faults.description');
        $this->db->from('faults');
        $this->db->join('users', 'faults.reporter = users.id', 'left');
        $this->db->order_by('reported DESC');
        return $this->db->get()->result_array();
    }
    
    function add_fault() {
        $this->db->insert('faults', array(
            'location' => $this->input->post('location'),
            'description' => $this->input->post('description'),
            'reporter' => $this->session->userdata('id'),
            'reported' => time()
        ));
    }
    
    function delete_fault($id) {
        $this->db->where('id', $id);
        $this->db->delete('faults');
    }
}