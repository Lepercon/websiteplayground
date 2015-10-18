<?php class Services extends CI_Controller {

    function Services() {
        parent::__construct();
        $this->page_info = array(
            'id' => 17,
            'title' => 'JBs',
            'big_title' => '<span class="big-text-tiny">JBs</span>',
            'description' => 'JBs Info',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
    
        if(isset($_POST['requests']) && $_POST['requests'] !== ''){
            $this->load->library('email');
            
            $config['wordwrap'] = FALSE;
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            
            $nl = '<br>';        
            
            $mail = 'Dear Services Officers,'.$nl.$nl;
            $mail .= 'We have received feedback regarding the coffee shop at Josephine Butler College.'.$nl.$nl;
            $mail .= 'Comments:'.$nl.'<b>';
            $mail .= nl2br($_POST['requests']).'</b>'.$nl.$nl;
            $mail .= '<i>ID: '.$this->session->userdata('uid').'</i>';
            $mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
            
            $users = $this->users_model->get_users_with_level(17, 'users.email');
            $emails = '';
            foreach($users as $u){
                $emails .= ($emails == ''?'':',').$u['email'];
            }
            
            $this->email->to($emails);
            $this->email->cc('butler.jcr@durham.ac.uk');
            $this->email->bcc('samuel.stradling@durham.ac.uk');
            $this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
            $this->email->message($mail);
            $this->email->subject('Coffee Shop Feedback');
            
            if(ENVIRONMENT != 'development'){
                $this->email->send();
            }
            $_POST['feedback-success'] = TRUE;

        }
        
        $this->load->library('page_edit_auth');
        $this->load->view('services/services', array('access_rights' => $this->page_edit_auth->authenticate('services')));
    }
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */