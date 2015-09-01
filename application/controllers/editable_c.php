<?php class Editable_c extends CI_Controller {

	var $page;

	function Editable_c()
	{
		parent::__construct();
		$this->page = $this->uri->rsegment(3);
		$this->load->library('editable', array('page' => $this->page));
		$this->editable->set_image_path(VIEW_PATH.$this->page.'/img/upload');
		$this->editable->set_doc_path(VIEW_PATH.$this->page.'/doc');
	}

	function save_page() {
		/*if(!$this->check_edit_rights()) {
			return;
		}*/
		$auth_code = md5($this->session->userdata('id').date('Y').$this->page.$_POST['file_path'].'50j05t9jk5-f59gk9fkfj8');
		$test = $this->input->post('auth');
		if($auth_code != $test){
			return;
		}
		$this->editable->set_file_path(VIEW_PATH.$this->page.'/'.$_POST['file_path'].'.php'); // path provided
		$this->editable->save_page();
	}

	function load_page_images() {
		if(!$this->check_edit_rights()) {
			return;
		}
		$this->editable->set_image_save_url(site_url($this->page.'/save_image'));
		$this->editable->set_image_url(VIEW_URL.$this->page.'/img/upload/');
		$this->editable->load_page_images();
	}

	function delete_image() {
		if(!$this->check_edit_rights()) {
			return;
		}
		$this->editable->delete_image();
	}

	function load_page_docs() {
		if(!$this->check_edit_rights()) {
			return;
		}
		$this->editable->set_doc_save_url(site_url($this->page.'/save_doc'));
		$this->editable->set_doc_url(VIEW_URL.$this->page.'/doc/');
		$this->editable->load_page_docs();
	}

	function delete_doc() {
		if(!$this->check_edit_rights()) {
			return;
		}
		$this->editable->delete_doc();
	}

	function save_image() {
		if(!$this->check_edit_rights()) {
			return;
		}
		$this->editable->save_image();
	}

	function save_doc() {
		if(!$this->check_edit_rights()) {
			return;
		}
		$this->editable->save_doc();
	}

	function get_page_functions() {
		$GLOBALS['controller_json'] = json_encode($this->editable->get_permitted_methods());
	}

	private function check_edit_rights() {
		$this->load->library('page_edit_auth');
		return $this->page_edit_auth->authenticate($this->page);
	}
}

/* End of file editable_c.php */
/* Location: ./application/controllers/editable_c.php */