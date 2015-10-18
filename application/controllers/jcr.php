<?php

class Jcr extends CI_Controller {

    function Jcr() {
        parent::__construct();
        $this->page_info = array(
            'id' => 18,
            'title' => 'The JCR',
            'big_title' => '<span class="big-text-small">What is the JCR?</span>',
            'description' => 'What is Butler College JCR?',
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
    
        if(isset($_POST['feedback-requests']) && $_POST['feedback-requests'] !== ''){
            $this->load->library('email');
            
            $config['wordwrap'] = FALSE;
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            
            $nl = '<br>';        
            
            $mail = 'Dear JCR President,'.$nl.$nl;
            $mail .= 'We have received feedback regarding the JCR at Josephine Butler College.'.$nl.$nl;
            $mail .= 'Comments:'.$nl.'<b>';
            $mail .= nl2br($_POST['feedback-requests']).'</b>'.$nl.$nl;
            $mail .= '<i>ID: '.$this->session->userdata('uid').'</i>';
            $mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
            
            $this->email->to('butler.jcr@durham.ac.uk');
            $this->email->cc('butler.alumni@durham.ac.uk');
            $this->email->bcc('samuel.stradling@durham.ac.uk');
            $this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
            $this->email->message($mail);
            $this->email->subject('JCR Feedback');
            
            if(ENVIRONMENT != 'development'){
                $this->email->send();
            }
            $_POST['feedback-success'] = TRUE;

        }

        $this->load->library('page_edit_auth');
        $this->load->view('jcr/jcr', array('access_rights' => $this->page_edit_auth->authenticate('jcr')));
    }
}

/* End of file voting.php */
/* Location: ./application/controllers/voting.php */