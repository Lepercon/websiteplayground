<?php class Bar extends CI_Controller {

    function Bar() {
        parent::__construct();
    }

    function index() {
    
        if(isset($_POST['requests']) && $_POST['requests'] !== ''){
            $this->load->library('email');
            
            $config['wordwrap'] = FALSE;
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            
            $nl = '<br>';        
            
            $mail = 'Dear Bar Stewards,'.$nl.$nl;
            $mail .= 'We have received feedback regarding the bar at Josephine Butler College.'.$nl.$nl;
            $mail .= 'Comments:'.$nl.'<b>';
            $mail .= nl2br($_POST['requests']).'</b>'.$nl.$nl;
            $mail .= '<i>ID: '.$this->session->userdata('uid').'</i>';
            $mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
            
            $users = $this->users_model->get_users_with_level(8, 'users.email');
            $emails = '';
            foreach($users as $u){
                $emails .= ($emails == ''?'':',').$u['email'];
            }
            
            $this->email->to($emails);
            $this->email->cc('butler.jcr@durham.ac.uk');
            $this->email->bcc('samuel.stradling@durham.ac.uk');
            $this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
            $this->email->message($mail);
            $this->email->subject('Bar Feedback');
            
            if(ENVIRONMENT != 'development'){
                $this->email->send();
            }
            $_POST['feedback-success'] = TRUE;

        }
        
        $this->load->model('events_model');
        $this->load->library('page_edit_auth');
        $this->load->view('bar/bar', array(
            'access_rights' => $this->page_edit_auth->authenticate('bar'),
            'events' => $this->events_model->get_events_by_category('Bar', '3')
        ));
    }

}

/* End of file bar.php */
/* Location: ./application/controllers/bar.php */