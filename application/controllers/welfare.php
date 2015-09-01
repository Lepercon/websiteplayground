<?php

class Welfare extends CI_Controller {

	function Welfare()
	{
		parent::__construct();
	}

	function index($section = NULL)
	{
		$this->load->library('page_edit_auth');
		$this->load->model('events_model');
		
		$sections = array('intro', 'supplies', 'drop_in', 'campaigns', 'university', 'town', 'minutes', 'notifications');
		if(is_null($section)){
			$section = $this->uri->rsegment(3);
		}
		if(!in_array($section, $sections)){
			$section = $sections[0];
		}
		
		if(isset($_POST['feedback-requests']) && $_POST['feedback-requests'] !== ''){
			$this->load->library('email');
			
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			
			$nl = '<br>';		
			
			$mail = 'Dear Welfare Officer,'.$nl.$nl;
			$mail .= 'We have received feedback regarding the welfare support at Josephine Butler College.'.$nl.$nl;
			$mail .= 'Comments:'.$nl.'<b>';
			$mail .= nl2br($_POST['feedback-requests']).'</b>'.$nl.$nl;
			$mail .= '<i>ID: '.$this->session->userdata('uid').'</i>';
			$mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
			
			$users = $this->users_model->get_users_with_level(array(11,12), 'users.email');
			$emails = '';
			foreach($users as $u){
				$emails .= ($emails == ''?'':',').$u['email'];
			}
			
			$this->email->to($emails);
			$this->email->cc('butler.jcr@durham.ac.uk');
			$this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
			$this->email->message($mail);
			$this->email->subject('Welfare Feedback');
			
			if(ENVIRONMENT != 'local'){
				$this->email->send();
			}
			$_POST['feedback-success'] = TRUE;

		}
		
		if(isset($_POST['ajax'])){
			$this->load->view('welfare/get_content', array(
				'access_rights' => $this->page_edit_auth->authenticate('welfare'),
				'section' => $section,
				'supplies' => $this->db->get('supplies')->result_array()
			));
		}else{
			$this->load->view('welfare/welfare', array(
				'access_rights' => $this->page_edit_auth->authenticate('welfare'),
				'events' => $this->events_model->get_events_by_category('Welfare', '3'),
				'section' => $section,
				'supplies' => $this->db->get('supplies')->result_array()
			));
		}
	}	
	
	function request_supply() {
		if(empty($_POST['supplies'])) {
			$this->index('supplies');
			return;
		}
		if(validate_form_token('welfare_supplies')) {
			$this->db->where_in('id', $_POST['supplies']);
			$supplies = $this->db->get('supplies')->result_array();
			
			$nl = '<br>';
			$sups = '';
			foreach($supplies as $s) $sups .= '<li><b>'.$s['name'].'</b></li>';
			$mail = isset($_POST['urgent'])?'<b>Urgent Request</b>'.$nl.'This request was marked as urgent by the person making the request.'.$nl.$nl:'';
			$mail .= 'The following supplies have been requested from the anonymous supplies request form:'.$nl.'<ul>'.$sups.'</ul>';
			$mail .= 'The anonymous request code is: <b>'.$_POST['anon_code'].'</b>';
			if(!empty($_POST['room'])){
				$mail .= $nl.$nl.'Room Number (for postbox delivery):'.$nl.'<b>'.$_POST['room'].'</b>';
			}
			$mail .= $nl.$nl.'Comments:'.$nl.'<b>'.$_POST['comments'].'</b>';
			$mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
			
			$this->load->library('email');
			
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			
			$this->email->to(WELFARE_EMAIL);
			$this->email->from(WELFARE_EMAIL, 'Supplies request form');
			$this->email->message($mail);
			if(isset($_POST['urgent'])){
				$this->email->cc('butler.jcr@durham.ac.uk');
				$this->email->subject('URGENT: Supplies request from the Butler JCR website');
			}else{
				$this->email->subject('Supplies request from the Butler JCR website');
			}
			if(ENVIRONMENT != 'local'){
				$this->email->send();
			}else{
                log_message('error', 'Email: '.$this->email->print_debugger());
            }
            if(isset($_POST['notifcation'])){
	            $anon_code = $_POST['anon_code'];
				$this->load->model('welfare_model');
				$this->welfare_model->add_welfare_notification($anon_code);   
            }
			$this->load->view('welfare/supplies_complete', array('code' => $_POST['anon_code'], 'supplies' => $supplies));
		}
		else $this->supplies();
	}

	function change_supply() {
		$this->load->library('page_edit_auth');
		if($this->page_edit_auth->authenticate('welfare') == 0) {
			$this->index('supplies');
			return;
		}
		if(validate_form_token('welfare_supplies')) {
			if(!empty($_POST['new'])) {
				$this->db->set('name', $_POST['new']);
				$this->db->insert('supplies');
			}
			if(!empty($_POST['remove'])) {
				$this->db->where('id', $_POST['remove']);
				$this->db->delete('supplies');
			}
			$this->index('supplies');
		}
		else $this->index('supplies');
	}
	
	function send_notification() {
		$anon_code = $_POST['anonymouscode'];
		$this->load->model('welfare_model');
        $this->welfare_model->send_notification($anon_code);
        $this->welfare_model->delete_notification($anon_code);
        redirect('welfare/index/notifications', 'location');
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */