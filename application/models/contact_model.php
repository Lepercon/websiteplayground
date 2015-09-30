<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class contact_model extends CI_Model {

    var $email_addresses;
    
    function contact_model() {
        parent::__construct();
        $this->clear_old();
        $this->email_addresses = array('jcr' => JCR_EMAIL, 'welfare' => WELFARE_EMAIL);
    }
    
    function clear_old() {
        $this->db->where('time <', time() - 14*24*60*60); // clear out old anonymous emails
        $this->db->delete('anon_emails');
    }
        
    function send_email() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user[]','To','trim|required|xss_clean');
        $this->form_validation->set_rules('email','Email','trim|'.(logged_in() ? '' : 'required|').'valid_email|xss_clean');
        $this->form_validation->set_rules('name','Name','trim|'.(logged_in() ? '' : 'required|').'xss_clean');
        $this->form_validation->set_rules('anonymous','Anonymous','trim|'.(logged_in() ? 'required|' : '').'xss_clean');
        $this->form_validation->set_rules('cc','BCC','trim|xss_clean');
        $this->form_validation->set_rules('subject','Subject','trim|required|max_length[100]|xss_clean');
        $this->form_validation->set_rules('message','Message','trim|required|max_length[10000]|xss_clean');
        if($this->form_validation->run()) {
            $this->load->library('email');
            if(!logged_in()) {
                $this->email->to($this->email_addresses[$_POST['user']]);
                $this->email->from($_POST['email'], $_POST['name']);
            }
            else {
                if($_POST['anonymous'] == '0') $from = array($this->session->userdata('email'), $this->session->userdata('firstname').' '.$this->session->userdata('surname'));
                else {
                    // insert unique anonymous reply code in database
                    while(1) {
                        $anon_code = rand_alphanumeric(10);
                        if($this->db->get_where('anon_emails', array('uid' => $anon_code['uid']))->num_rows() == 0) break;
                    }
                    $this->db->set(array('uid' => $anon_code, 'email' => $this->session->userdata('email'), 'subject' => $_POST['subject'], 'message' => $_POST['message'], 'time' => time()));
                    $this->db->insert('anon_emails');
                    // add anonymous information to from field and message body of email
                    $from = array('butler.jcr@durham.ac.uk', 'Anonymous (via JCR website) - no reply');
                    $_POST['message'] = $_POST['message']."\r\n\r\n______________\r\n\r\n".'This email was sent anonymously via the JCR website.  Please do not reply to this email.  To reply to the message, visit {unwrap}'.site_url('contact/anon_reply/'.$anon_code).'{/unwrap} within two weeks of receiving this message.';
                }
                $this->email->to($this->parse_users($_POST['user']));
                $this->email->from($from[0], $from[1]);
            }
            if(!empty($_POST['cc'])) $this->email->bcc($this->session->userdata('email'));
            $this->email->subject($_POST['subject']);
            $this->email->message($_POST['message']);
            $this->email->send();
            return TRUE;
        }
        else return FALSE;
    }

    private function parse_users($users) {
        $ret = array();
        if(!is_array($users)) $users = array($users);
        foreach($users as $u) {
            if(is_string($u) && isset($this->email_addresses[$u])) $ret[] = $this->email_addresses[$u];
            else {
                $this->db->where('id', $u);
                $this->db->select('email');
                $email = return_array_value('email', $this->db->get('users')->row_array(0));
                $ret[] = $email;
            }
        }
        return $ret;
    }
}
