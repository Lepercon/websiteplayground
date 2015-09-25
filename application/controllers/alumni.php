<?php class Alumni extends CI_Controller {

	function Alumni() {

		parent::__construct();
		$this->load->model('alumni_model');

	}

	function index() {

		$this->load->model('events_model');
		$this->load->library('page_edit_auth');		
		$this->load->view('alumni/alumni', array(
			'access_rights' => $this->page_edit_auth->authenticate('alumni'),
			'events' => $this->events_model->get_events_by_category('Alumni', '3'),
			'admin' => $this->alumni_model->alumni_permissions(),
			'current_scdo' => $this->alumni_model->scdo_contact(),
			'signups'=>$this->alumni_model->get_signups()
		));

	}

	function sign_up(){

	

		$this->load->model('events_model');

		$e_id = $this->uri->segment(3);		
		$alumni_event_info = $this->alumni_model->get_event_info($e_id);

		if(!empty($alumni_event_info)){					

			$event_info = $this->events_model->get_event($e_id);
			$current_scdo = $this->alumni_model->scdo_contact();

			$this->load->view('alumni/sign_up', array(
				'alumni_event_info' => $alumni_event_info, 
				'event_info' => $event_info,
				'current_scdo' => $current_scdo));

		}else{
			$this->index();
		}

	}

	function submit_sign_up(){

		$this->load->model('events_model');
		$email = $_POST['email'];

		$this->output->set_header('Content-type: application/json');

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			log_message('error', 'Invalid Email: '.$_POST['email']);
			$results = array(
			   'error' => true,
			   'error_msg' => 'Invalid Email'
			);			

			log_message('error', json_encode($results));
			$this->output->append_output(json_encode($results));
			return false;

		}

		$event_id = $_POST['event_id'];
		$num_tickets = $_POST['num_tickets'];
		$name = $_POST['name'];
		$name_at_uni = $_POST['name_at_uni'];
		$guests = $_POST['guest_names'];
		$options = $_POST['options'];
		$address = $_POST['address'];
		$email = $_POST['email'];
		$phone = $_POST['phone_number'];
		$graduation_year = $_POST['graduation_year'];
		$subject = $_POST['subject'];
		$special_requirements = $_POST['requirements'];
		$rand = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz",200)), 0, 10);
		$id = $this->alumni_model->add_signup($event_id, $num_tickets, $name, $name_at_uni, $guests, $options, $address, $email, $phone, $graduation_year, $subject, $special_requirements, $rand);
		
		$results = array(
		   'error' => false,
		   'error_msg' => '',
		   'id' => $id,
		   'key' => $rand
		);

		$this->output->append_output(json_encode($results));

		//Email the user
		$this->load->library('email');
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$event = $this->alumni_model->get_event_info($event_id);
		$event_info = $this->events_model->get_event($event_id);		
		$users = $this->users_model->get_users_with_level(4, 'users.email, users.firstname, users.prefname, users.surname');
		$scdo = ($users[0]['prefname']==''?$users[0]['firstname']:$users[0]['prefname']).' '.$users[0]['surname'];
		$users[0]['email'] = 'Samuel.Stradling@durham.ac.uk';

		$m = 'Hey!<br /><br />';
		$m .= 'Thank you for signing up to this event.';
		$m .= '<br /><br />';
		$m .= 'Date: '.date('l dS F Y', $event_info['time']);
		$m .= '<br />';
		$m .= 'Tickets Requested: '.$num_tickets.' (£'.$num_tickets*$event['cost'].')';;
		
		if(!is_null($event['email_info'])){
			$m .= '<br />'.str_replace(chr(10),'<br>', $event['email_info']);
		}

		$url = site_url('alumni/view_signup/'.$id.'/'.$rand);
		$m .= '<br /><br />';		
		$m .= 'For info about the event please go to: <a href="'.$url.'">'.$url.'</a>';	
		$m .= '<br /><br />';
		$m .= $scdo;

		$this->email->from($users[0]['email'], $scdo);
		$this->email->to($email);
		$this->email->bcc($users[0]['email']);		
		$this->email->subject('Alumni Event: '.$event_info['name']);
		$this->email->message($m);

		if(ENVIRONMENT !== 'development') {
			$this->email->send();
		}else{
            log_message('error', 'Email: '.$this->email->print_debugger());
        }
		
		return;	

	}

	

	function view_signup(){

		$this->load->model('events_model');
		$signup_id = $this->uri->segment(3);
		$key = $this->uri->segment(4);

		$signup = $this->alumni_model->get_signup($signup_id);
		$current_scdo = $this->alumni_model->scdo_contact();

		if($signup['rand'] == $key){		
			$this->load->view('alumni/view_signup', array(
				'signup' => $signup,
				'alumni_event_info' => $this->alumni_model->get_event_info($signup['alumni_event_id']),
				'event_info' => $this->events_model->get_event($signup['alumni_event_id']),
				'current_scdo' => $current_scdo));					
		}else{
			$this->index();
		}

	}		

    function create_signup(){

		if($this->alumni_model->alumni_permissions()){
        	$this->load->model('events_model');

	        $events = $this->events_model->get_events_by_category('all');
	        $u_id = $this->session->userdata('id');
	        $user = $this->users_model->get_users($u_id);
	        $email = $user['email'];
	        $this->load->view('alumni/create_signup', array(
	        	'user_email'=>$email,
	            'event'=>$events));
	    }else{
	    	$this->index();
	    }
    }
    
    function send_email(){
   		
		$this->load->dbutil();
		$backup =& $this->dbutil->backup(); 
		$this->load->helper('file');
		write_file('backups/jcr'.date('Ymd His').'.sql.gz', $backup); 
	    
	}

}



/* End of file alumni.php */
/* Location: ./application/controllers/alumni.php */