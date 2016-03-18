<?php class Scheduling extends CI_Controller {
    
    function Scheduling() {
        parent::__construct();
        $this->load->model('scheduling_model');
    }
    
    function index() {
        $this->load->view('scheduling/scheduling');
    }
   
    function send_email(){
        
        $totp = $this->uri->segment(3);
        if($this->scheduling_model->validate_totp($totp)){
            
            $subject = 'Test';    	
            $message = 'This is a test email, thank you for reading';
            $from_email = 'samuel.stradling@durham.ac.uk';
            $from_name = 'Samuel Stradling';
            $to = 'samuel.stradling@durham.ac.uk';
            $res = $this->scheduling_model->send_email($subject, $message, $from_email, $from_name, $to);
            
        }else{
        
            $res = 'Invalid totp: '.$totp.' Timestamp: '.date('d/m/Y H:i:s');
            log_message('error', $res);
            
        }
        
        $this->load->view('scheduling/scheduling', array('output'=>$res));	
    }
    
    function finance_daily(){

		$this->load->model('finance_model');
		
		$totp = $this->uri->segment(3);
		if($this->scheduling_model->validate_totp($totp)){
	
		
			$subject = 'JCR Finance';
			$from_email = 'butler.treasurer@durham.ac.uk';
			$from_name = 'Josephine Butler College JCR Finance';
			
			$notifications = $this->finance_model->get_unnotified_notifications();
			$nl = '<br>';
			
			foreach($notifications as $n){
				
				if(is_null($n['email'])){
					$users = $this->users_model->get_users_with_level(14, 'users.email');
					$n['email'] = array();
					foreach($users as $u){
						$n['email'][] = $u['email'];
					}
					$n['name'] = 'JCR Treasurer';
				}
				
				$to = $n['email'];
				
				$message = 'Dear '.$n['name'].','.$nl.$nl;
				$message .= 'You have <b>'.$n['count'].'</b> new unread finance notifications.'.$nl;
				$message .= 'To view these notifications, please visit the <a href="'.site_url('finance/view_notifications').'">butlerjcr website</a>.'.$nl.$nl;
				$message .= 'Thank you,'.$nl.'ButlerJCR Finance'.$nl.$nl;
				$message .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
				
				$this->scheduling_model->send_email($subject, $message, $from_email, $from_name, $to);
				
			}
			
			if(date('w') == 4){//Is it a thursday?
				//$this->finance_weekly();
			}
		
		}else{
		
			$res = 'Invalid totp: '.$totp.' Timestamp: '.date('d/m/Y H:i:s');
			log_message('error', $res);
		
		}
		
	}
	
	function finance_weekly(){
			
		$totp = $this->uri->segment(3);
		if(!($this->scheduling_model->validate_totp($totp))){
			return;		
		}
		
		if((date('W') == 23) || (date('W') == 37)){// June, then September
			$this->pictureless_people();
		}
		
		$this->load->model('finance_model');
		$invoices = $this->finance_model->get_sorted_invoices(TRUE);
		$claims = $this->finance_model->get_claims_by_status(0);
		
		foreach($claims as $c){
			if(!empty($c['owners_ids'])){
				foreach($c['owners'] as $id){
					if(!isset($invoices[$id['id']])){
						$invoices[$id['id']]['name'] = ($id['prefname']==''?$id['firstname']:$id['prefname']).' '.$id['surname'];
						$invoices[$id['id']]['email'] = $id['email'];
						$invoices[$id['id']]['current'] = $id['current'];
						$invoices[$id['id']]['custom_email'] = $id['custom_email'];
					}
					$invoices[$id['id']]['claims'][] = $c['item'].' for '.$c['pay_to'].' ('.$c['budget_name'].')';
				}
			}
		}
		
		$subject = 'Butler JCR Finance - Weekly Reminder';
		$from_email = 'butler.treasurer@durham.ac.uk';
		$from_name = 'Butler JCR Finance';
		$bcc = 'butler.treasurer@durham.ac.uk';
		
		foreach($invoices as $i){	
			if((isset($i['total']) && ($i['total']-$i['paid']) > 0) || isset($i['claims'])){
				if($i['current']){
					$to = $i['email'];
				}else{
					$to = $i['custom_email'];
				}
				$nl = '<br>';
				$message = 'Dear '.$i['name'].','.$nl.$nl;
				if(isset($i['total'])){
					$message .= 'You have due invoices totalling <b>&#163;'.number_format($i['total']-$i['paid'], 2).'</b>. ';
					$message .= 'To view these invoices, please visit the <a href="'.https_url('finance/my_invoices').'">butlerjcr website</a>.'.$nl;					
				}
				if(isset($i['claims'])){
					$message .= 'You'.(isset($i['total'])?' also':'').' have the following <a href="'.site_url('finance/my_claims').'">claims</a> waiting to be approved by youself, to reduce any delay in payment please log on and check them as soon as possible:'.$nl;
					foreach($i['claims'] as $c){
						$message .= '&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$c.$nl;
					}
					$message .= $nl;
				}
				$message .= 'If you think you have paid this invoice, please ensure you go to the link above and click "mark as paid".'.$nl.$nl;
				$message .= 'If you have any questions, feel free to reply to this email.'.$nl.$nl;
				$message .= 'Thank you,'.$nl.'ButlerJCR Finance'.$nl.$nl;
				$message .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
				$this->scheduling_model->send_email($subject, $message, $from_email, $from_name, $to, array(), $bcc);
			}
		}
		
	}
	
	function pictureless_people(){
	
		$totp = $this->uri->segment(3);
		if(!($this->scheduling_model->validate_totp($totp))){
			return;		
		}
		
		$this->load->model('scheduling_model');
	
		$this->db->select("users.id, users.firstname, users.prefname, users.surname, users.uid, users.email, levels.full, levels.type");
		$this->db->join('users', 'users.id=level_list.user_id');
		$this->db->join('levels', 'levels.id=level_list.level_id');
		$this->db->where('level_list.current', 1);
		//$this->db->group_by('levels.type');
		$users = $this->db->get('level_list')->result_array();
		$path = VIEW_PATH.'details/img/users/';
		$no_image = array();
		foreach($users as $u){
			if(!file_exists($path.$u['uid'].'_large.jpg')){
				$no_image[] = $u;
			}
		}
		$nl = '<br>';
		$url = site_url('details/profile');
		$whoswho = anchor('whoswho', 'whoswho');
		
		$sections = array(
			'societies' => ' information about how to contact you is displayed in the '.$whoswho.' section of the site, along with the section about your '.anchor('involved/index/societies', 'society').'. Having a photo makes these parts much friendlier to other students, and may encourage them to join your society.',
			'assistants' => ' information about how to contact you is displayed in the '.$whoswho.' section of the site, along with other relevant sections of the site. Having a photo makes these parts much friendlier to other students, and may encourage them to run for your role.',
			'sports' => ' information about how to contact you is displayed in the '.$whoswho.' section of the site, along with the section about your '.anchor('involved/index/sports', 'sports team').'. Having a photo makes these parts much friendlier to other students, and may encourage them to join your team.',
			'committees' => ' information about how to contact you is displayed in the '.$whoswho.' section of the site, along with other relevant sections of the site. Having a photo makes these parts much friendlier to other students, and may encourage them to run for your role.',
			'support' => ' information about how to contact you is displayed in the '.$whoswho.' section of the site. Having a photo makes this part of the website much friendlier to others, which is important as part of your role is supporting students.',
			'exec' => ' information about how to contact you is displayed in the '.$whoswho.' section of the site, along with other relevant sections of the site. Having a photo makes these parts much friendlier to other students, and may encourage them to run for your role.'
		);
		
		$subject = 'butlerjcr.com';
		$from_email = 'butler.jcr@durham.ac.uk';
		$from_name = 'butlerjcr.com';
		
		foreach($no_image as $u){
			$to = $u['email'];
			if(isset($sections[$u['type']]) || ($u['type'] == 'services')){
				$email = 'Dear '.($u['prefname']==''?$u['firstname']:$u['prefname']).','.$nl.$nl;
				$email .= "We noticed that you don't have a profile image uploaded to the JCR website. ";
				$email .= 'The site is viewed by many people every day, and having photos of people creates a more welcoming page, especially for prospective students.'.$nl.$nl;
				if($u['type'] == 'services'){
					if($u['full'] == 'Coffee Shop Staff'){
						$link = 'services';
						$section = 'Coffee Shop';
					}elseif($u['full'] == 'Kitchen Staff'){
						$link = 'services';
						$section = 'Kitchen';
					}else{//Bar Staff & Supervisors
						$link = 'bar';
						$section = 'Bar';
					}
					$email .= 'As <b>'.$u['full'].'</b>, infomation about how to contact you is displayed in the '.$whoswho.' section of the site, along with the section promoting the '.anchor($link, $section).'. Having a photo makes these parts much friendlier to other students, and may encourage them to apply for a job at the '.$section.'.'.$nl.$nl;
				}else{
					$email .= 'As <b>'.$u['full'].'</b>, '.$sections[$u['type']].$nl.$nl;
				}
				$email .= 'We were just wondering if you could upload a photo, and maybe write something about being <b>'.$u['full'].'</b> by going to:'.$nl.' <a href="'.$url.'">'.$url.'</a>'.$nl.$nl;
				$email .= 'Thanks for taking time to read this email, and thank you if you have taken time to upload a photo.'.$nl.$nl;
				$email .= 'All the best,'.$nl.'The Butler JCR Web Team';
				$email .= '<hr>'.$nl;
				$email .= 'If you have any questions, feel free to reply to this email.'.$nl.$nl;
				$this->scheduling_model->send_email($subject, $email, $from_email, $from_name, $to, array(), 'samuel.stradling@durham.ac.uk')	;		
				log_message('error', 'email sent to '.$to);
			}else{
				log_message('error', 'email no sent to '.$to.' ('.$u['type'].')');
			}
		}
		$this->load->view('display_data', array('string'=>'Emails Sent'));
	}


}