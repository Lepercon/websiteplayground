<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_edit_auth {

	function __construct() {
	}

	function authenticate($page_name) {
		if(!logged_in()) return 0;
		if(is_admin()) return 2; // admin, full edit and user management rights
		$ci =& get_instance();
		$ci->db->where('page_id', $this->get_page_id_from_name($page_name)); // get all levels for this page
		$result = $ci->db->get('page_edit_rights')->result_array();
		foreach($result as $r) {
			if(has_level($r['level_id'])) return 1; // not admin, but edit rights
		}
		return 0;
	}

	private function get_page_id_from_name($name) {
		include APPPATH.'config/pages.php';
		return $pages[$name]['id'];
	}
}