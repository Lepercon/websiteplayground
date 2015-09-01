<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Family_model extends CI_Model {

	function Family_model() {
		parent::__construct();
	}
	
	function get_generations_below($levels, $u_id){
		$m_id = $this->get_marriage_by_user($u_id);
		if(!empty($m_id)){
			$children = $this->get_children($m_id);
		}else{
			$children = array();
		}
		if($levels !== 1){
			$n = sizeof($children);
			for($i=0;$i<$n;$i++){
				$children[$i]['children'] = $this->get_generations_below($levels - 1, $children[$i]['user_id']);
			}
		}
		return $children;
	}
	
	function get_generations_above($levels, $u_id){
		$user = $this->get_user_info($u_id);
		if(isset($user['marriage_id'])){
			$parents = $this->get_marriage_by_id($user['marriage_id']);
			if($levels !== 1){
				$n = sizeof($parents);
				for($i=0;$i<$n;$i++){
					if(isset($parents[$i])){
						$parents[$i]['parents'] = $this->get_generations_above($levels - 1, $parents[$i]['id']);
					}
				}
			}
			return $parents;
		}else{
			return array();
		}
	}
	
	function get_marriage_by_id($m_id){
		$select = 'family_marriages.user_1, family_marriages.user_2, family_marriages.user_3, family_marriages.user_4';
		$this->db->where('family_marriages.id', $m_id);		
		for($i=1;$i<=4;$i++){
			$this->db->join('users u'.$i, 'u'.$i.'.id = family_marriages.user_'.$i, 'left outer');
			$select .= ', u'.$i.'.firstname as u'.$i.'_firstname';
			$select .= ', u'.$i.'.prefname as u'.$i.'_prefname';
			$select .= ', u'.$i.'.surname as u'.$i.'_surname';
		}
		$this->db->select($select);
		$data = $this->db->get('family_marriages')->row_array(0);
		$parents = array();
		if(!empty($data)){
			$parents['m_id'] = $m_id;
			for($i=1;$i<=4;$i++){
				$parents[$i-1]['id'] = $data['user_'.$i];
				$parents[$i-1]['firstname'] = $data['u'.$i.'_firstname'];
				$parents[$i-1]['prefname'] = $data['u'.$i.'_prefname'];
				$parents[$i-1]['surname'] = $data['u'.$i.'_surname'];
				$parents[$i-1]['parents'] = array();
			}
		}
		return $parents;
	}
	
	function get_marriage_by_user($u_id){
		$this->db->select('id');
		for($i=1;$i<=4;$i++){
			$this->db->or_where('user_'.$i, $u_id);
		}
		$marriage = $this->db->get('family_marriages')->row_array(0);
		if(empty($marriage)){
			return $marriage;
		}else{
			return $marriage['id'];
		}
	}
	
	function get_user_info($u_id){
		$this->db->where('user_id', $u_id);
		return $this->db->get('family_tree')->row_array(0);
	}
	
	function get_children($m_id){
		$this->db->select('family_tree.user_id, users.firstname, users.prefname, users.surname');
		$this->db->where('marriage_id', $m_id);
		$this->db->from('family_tree');
		$this->db->join('users', 'users.id = family_tree.user_id');
		return $this->db->get()->result_array();

	}
	
	function get_family($u_id) {
		return array();
	}
	
	function get_parents() {
		$DB2 = $this->load->database('family', TRUE);
		$DB2->select('id, name, surname, yeargroup');
		$DB2->limit(600);
		$DB2->order_by("yeargroup", "desc");
		$DB2->order_by("name", "asc");
		$query = $DB2->get('people');
		return $query->result_array();
	}
	
	function check_family() {
		$this->form_validation->set_rules('parent_1', 'Parent 1', 'trim|required|integer|greater_than[0]');
		if($_POST['parent_2']!=0){
			$this->form_validation->set_rules('parent_2', 'Parent 2', 'trim|required|integer');
		}
		if($_POST['parent_3']!=0){
			$this->form_validation->set_rules('parent_3', 'Parent 3', 'trim|required|integer');
		}
		if($_POST['parent_4']!=0){
			$this->form_validation->set_rules('parent_4', 'Parent 4', 'trim|required|integer');
		}
		$this->form_validation->set_message('greater_than', 'Parent 1 is required.');
		$this->form_validation->set_rules('first_name[0]', 'Child 1\'s Name', 'trim|required|max_length[30]|xss_clean');
		$this->form_validation->set_rules('last_name[0]', 'Child 1\'s Surname', 'trim|required|max_length[30]|xss_clean');
		$this->form_validation->set_rules('year[0]', 'Child 1\'s Year', 'trim|required|integer');
		
		if($this->form_validation->run()) {
			$errors = array();
			return $errors;
		}
		else return FALSE;
	}
	
	function save_family() {
		$DB2 = $this->load->database('family', TRUE);
		
		if($_POST['parent_2']==0){
			$_POST['parent_2']=NULL;
		}
		if($_POST['parent_3']==0){
			$_POST['parent_3']=NULL;
		}
		if($_POST['parent_4']==0){
			$_POST['parent_4']=NULL;
		}
		
		foreach($_POST['first_name'] as $v => $name){
			if (!empty($name)) {
				$data = array(
				   'parent_1' => $_POST['parent_1'] ,
				   'parent_2' => $_POST['parent_2'] ,
				   'parent_3' => $_POST['parent_3'] ,
				   'parent_4' => $_POST['parent_4'],
				   'created_by' => $this->session->userdata('id'),
				   'name' => $name ,
				   'surname' => $_POST['last_name'][$v] ,
				   'yeargroup' => $_POST['year'][$v]
				);
				$DB2->insert('people', $data);
			}
		}
	}
	
}