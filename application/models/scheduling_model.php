<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Scheduling_model extends CI_Model {    

    function Scheduling_model() {        
        parent::__construct(); // Call the Model constructor    
    }    
    
    function send_email($subject, $message, $from_email, $from_name, $to, $cc=NULL, $bcc=NULL) {                
        
        $this->load->library('email');
        $this->email->clear();
        
        $config['wordwrap'] = FALSE;
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        
        $this->email->from($from_email, $from_name);
        $this->email->to($to);
        if(!is_null($cc))
            $this->email->cc($cc);
        if(!is_null($bcc))
            $this->email->bcc($bcc);
            
        $this->email->subject($subject);
        $this->email->message($message);
        
        if(ENVIRONMENT !== 'development') {
            $this->email->send();
            return $this->email->print_debugger();
        }else{
        
            $email = array(
                'subject' => $subject,
                'to' => $to,
                'from' => array(
                    'email' => $from_email,
                    'name' => $from_name
                ),
                'cc' => $cc,
                'bcc' => $bcc,
                'message' => $message
            );
        
            log_message('error', 'The following email can not be sent using local environment:');
            log_message('error', var_export($email, true));
        }
        
        return -1;
    }
    
    function validate_totp($totp){
        $secret_key = 'sRttWqzrX4wwDnVBKNhwmaMg';
        for($i=-2;$i<=2;$i++){
            $now = date('dmYHi', time() + $i * 60);
            $match = md5($now.$secret_key.$now);
            if($totp == $match){                
                if($i != 0){
                    log_message('error', 'Non 0 totp validation: '.$i);                    
                }
                return 1;
            }
        }
        return 0;
    }    
}