<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model {

	function Common_model() {
		parent::__construct(); // Call the Model constructor
	}
	
	function get_menu_structure(){
		$this->db->order_by('sort_order, id');
		return $this->db->get('menu_structure')->result_array();
	}
	
	function get_sorted_menu(){
		
		$data = $this->get_menu_structure();
		
		$i = -1;
		foreach($data as $d){
			if($d['level'] == 0){
				$menu[++$i]['title']['display'] = $d['display_name'];
				$menu[$i]['title']['link'] = $d['link'];
				$ii = -1;
				foreach($data as $e){
					if($e['parent_id'] == $d['id']){
						$menu[$i][++$ii]['title']['display'] = $e['display_name'];
						$menu[$i][$ii]['title']['link'] = $e['link'];
						$iii = -1;
						foreach($data as $f){
							if($f['parent_id'] == $e['id']){
								$menu[$i][$ii][++$iii]['display'] = $f['display_name'];
								$menu[$i][$ii][$iii]['link'] = $f['link'];
							}
						}
					}
				}
			}
		}
		
		return $menu;
	}
    
    function get_contact($level_id){
    	$users = $this->users_model->get_users_with_level($level_id, 'users.id, users.email, users.prefname, users.firstname, users.surname');
		$user = $users[0];
		return ($user['prefname']==''?$user['firstname']:$user['prefname']).' '.$user['surname'].' (<a href="'.(logged_in()?site_url('/contact/'.$user['id']):('mailto:'.$user['email'])).'">'.$user['email'].'</a>)';
    }
    
    function update_page_count($return = FALSE){
    	$url = $this->uri->ruri_string();
    	$this->db->where('url', $url);
    	$this->db->set('count', 'count+1', FALSE);
    	$this->db->set('last_access', time());
    	$this->db->update('page_stats'); 
    	if($this->db->affected_rows() === 0){
    		$this->db->insert('page_stats', array('url'=>$url, 'last_access'=>time()));
    	}
    	
    	$id = $this->session->userdata('id');
    	if($id !== FALSE){
			$this->db->where('url', $url);
			$this->db->where('user_id', $id);
	    	$this->db->set('count', 'count+1', FALSE);
	    	$this->db->set('last_access', time());
	    	$this->db->update('user_stats'); 
	    	if($this->db->affected_rows() === 0){
	    		$this->db->insert('user_stats', array('url'=>$url, 'user_id'=>$id, 'last_access'=>time()));
	    	}
    	}
    	
    	if($return){
    		$this->db->where('url', $url);
    		$data = $this->db->get('page_stats')->row_array(0);
    		return $data['count'];
    	}
    	return NULL;
    }
    
    function get_files($section){
    	$this->db->where('section', $section);
    	$this->db->order_by('date DESC');
    	return $this->db->get('files')->result_array();
    }
    
    function upload_files($section, $path){
    	
    	$config['upload_path'] = './application/views/'.$path;
    	$config['allowed_types'] = 'gif|jpg|png|pdf';
    	$config['encrypt_name'] = TRUE;
    	
    	if(!file_exists($config['upload_path'])){
    		mkdir($config['upload_path'], 0777, TRUE);
    	}
    	
    	$this->load->library('upload', $config);
    	if(!$this->upload->do_upload('file_upload')){
			return $this->upload->display_errors('<p class="validation_errors"><span style="display:inline-block" class="ui-icon ui-icon-close"></span>Upload Failure: ', '</p>');
		}else{
			$data = $this->upload->data();
			$this->store_file($data, $section);
			return true;
		}
    
    }
    
    function store_file($file, $section){
    	$data['file_name'] = substr($file['full_path'], strpos($file['full_path'], '/views/')+6);
    	$data['section'] = $section;
    	$data['name'] = $_POST['file_name']==''?$file['orig_name']:$_POST['file_name'];
    	$data['date'] = mktime(0, 0, 0, $_POST['file_month'], $_POST['file_day'], $_POST['file_year']);
    	$this->db->insert('files', $data);
    }
    
    function remove_file($id, $section){
    	$this->db->where('id', $id);
    	$this->db->where('section', $section);//for securuity
    	$file = $this->db->get('files')->row_array(0);
    	if(!empty($file)){
	    	$this->db->where('id', $id);
	    	$this->db->where('section', $section);
	    	$this->db->delete('files'); 
	    	unlink('application/views'.$file['file_name']);
	    	return TRUE;
    	}
    	return FALSE;
    }

}