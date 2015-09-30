<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class archive_model extends CI_Model {
    
    function archive_model() {
        parent::__construct();
    }
    
    function get_sections() {
        $this->db->order_by('full ASC');
        return $this->db->get('steering_pages')->result_array();
    }
    
    function get_section_id($section, $sections) {
        if(is_null($section)) return null;
        foreach($sections as $s) if($s['short'] == $section) return $s['id'];
        return null;
    }
    
    function get_years($section) {
        if(is_null($section)) return null;
        $this->db->select('DISTINCT year', FALSE);
        $this->db->where('section_id', $section);
        $this->db->order_by('year DESC');
        $tmp = $this->db->get('steering_docs')->result_array();
        $years = array();
        foreach($tmp as $t) $years[] = $t['year'];
        return $years;
    }
    
    function get_docs($years, $section) {
        if(is_null($section)) return null;
        $this->db->where('section_id', $section);
        if($section == 10) {
            $this->db->order_by('year DESC, month DESC, day DESC, name ASC');
        }
        else {
            $this->db->order_by('year DESC, name ASC, month DESC, day DESC');
        }
        $tmp = $this->db->get('steering_docs')->result_array();
        $docs = array();
        foreach($tmp as $t) $docs[$t['year']][] = $t;
        return $docs;
    }
    
    function add_section($name) {
        $short = str_replace(' ', '_', strtolower($name));
        $this->db->where('short', $short);
        if($this->db->get('steering_pages')->num_rows() > 0) return FALSE;
        $this->db->set(array('short' => $short, 'full' => $name));
        $this->db->insert('steering_pages');
        mkdir(VIEW_PATH.'archive/doc/'.$short);
        return TRUE;
    }
    
    function delete_section($id) {
        $this->db->where('id', $id);
        $section = $this->db->get('steering_pages')->row_array(0);
        $folder = VIEW_PATH.'archive/doc/'.$section['short'];
        if(file_exists($folder)) deleteAll($folder);
        $this->db->where('id', $id);
        $this->db->delete('steering_pages');
    }
}
