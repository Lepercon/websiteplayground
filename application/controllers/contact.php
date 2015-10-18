<?php

class Contact extends CI_Controller {

    var $email_addresses;

    function Contact() {
        parent::__construct();
        $this->email_addresses = array('jcr' => JCR_EMAIL, 'welfare' => WELFARE_EMAIL);
        $this->db->where('time <', time() - 14*24*60*60); // clear out old anonymous emails
        $this->db->delete('anon_emails');
        $this->load->library('form_validation');
        $this->page_info = array(
            'id' => 7,
            'title' => 'Contact Us',
            'big_title' => '<span class="big-text-medium">Contact Us</span>',
            'description' => 'How to contact us',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('contact/contact'),
            'js' => array('contact/contact'),
            'keep_cache' => FALSE,
            'editable' => FALSE
        );
    }
    
    function index($errors = FALSE, $success = FALSE) {
        if(!logged_in()) {
            $this->load->view('contact/contact', array('user' => array('id' => 'jcr', 'firstname' => 'The', 'surname' => 'JCR'), 'errors' => $errors, 'success' => $success));
        }
        else {
            $this->load->view('contact/contact', array('users' => $this->get_users(), 'errors' => $errors, 'success' => $success));
        }
    }
    
    function user($id = null, $errors = FALSE, $success = FALSE) {
        if(is_null($id)) $id = $this->uri->rsegment(3);
        if($id === FALSE) {
            $this->index();
            return;
        }
        if(is_numeric($id)) {
            $user = $this->users_model->get_users($id);
        } else {
            if($id == 'jcr') {
                $user = array('id' => 'jcr', 'firstname' => 'The', 'prefname' => '', 'surname' => 'JCR', 'title' => $this->email_addresses['jcr']);
            } elseif($id == 'welfare') {
                $user = array('id' => 'welfare', 'firstname' => 'Butler', 'prefname' => '', 'surname' => 'Welfare', 'title' => $this->email_addresses['welfare']);
            } else {
                $this->index();
                return;
            }
        }
        if(!logged_in()) $this->index();
        else $this->load->view('contact/contact', array('user' => $user));
    }

    function send() {
        if(validate_form_token('contact')) {
            $rules = array(
                array(
                    'field' => 'user'.(logged_in() ? '[]' : ''),
                    'label' => 'To',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'email',
                    'label' => 'Email',
                    'rules' => 'trim|'.(logged_in() ? '' : 'required|').'valid_email|xss_clean'
                ),
                array(
                    'field' => 'name',
                    'label' => 'Name',
                    'rules' => 'trim|'.(logged_in() ? '' : 'required|').'xss_clean'
                ),
                array(
                    'field' => 'anonymous',
                    'label' => 'Anonymous',
                    'rules' => 'trim|'.(logged_in() ? 'required|' : '').'xss_clean'
                ),
                array(
                    'field' => 'cc',
                    'label' => 'BCC',
                    'rules' => 'trim|xss_clean'
                ),
                array(
                    'field' => 'subject',
                    'label' => 'Subject',
                    'rules' => 'trim|required|max_length[100]|xss_clean'
                ),
                array(
                    'field' => 'message',
                    'label' => 'Message',
                    'rules' => 'trim|required|max_length[10000]|xss_clean'
                )
            );
            $this->form_validation->set_rules($rules);
            if($this->form_validation->run()) {
                $this->load->library('email');
                if(!logged_in()) {
                    $this->email->to($this->email_addresses[$_POST['user']]);
                    $this->email->from($_POST['email'], $_POST['name']);
                }
                else {
                    if($_POST['anonymous'] == '0') $from = array($this->session->userdata('email'), $this->session->userdata('firstname').' '.$this->session->userdata('surname'));
                    else {
                        // database stuff
                        while(1) {
                            $anon_code = rand_alphanumeric(10);
                            if($this->db->get_where('anon_emails', array('uid' => $anon_code['uid']))->num_rows() == 0) break;
                        }
                        $this->db->set(array('uid' => $anon_code, 'email' => $this->session->userdata('email'), 'subject' => $_POST['subject'], 'message' => $_POST['message'], 'time' => time()));
                        $this->db->insert('anon_emails');
                        // email stuff
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
                $this->index(FALSE, 'Email Sent');
            }
            else $this->index(TRUE, FALSE);
        }
        else $this->index();
    }
    
    function anon_reply() {
        $uid = $this->uri->rsegment(3);
        if($uid === FALSE OR !logged_in()) {
            $this->index();
            return;
        }
        $this->db->where('uid', $uid);
        $details = $this->db->get('anon_emails')->row_array(0);
        if(empty($details)) {
            $this->index();
            return;
        }
        if(validate_form_token('anon_reply')) {
            $this->form_validation->set_rules('amessage','Message','trim|required|max_length[10000]|xss_clean');
            if($this->form_validation->run()) {
                $this->load->library('email');
                $this->email->to($details['email']);
                $this->email->from($this->session->userdata('email'), $this->session->userdata('firstname').' '.$this->session->userdata('surname'));
                $this->email->subject('Re: '.$details['subject']);
                $this->email->message($_POST['amessage']."\r\n\r\n______________\r\n\r\n".'This email was sent via the JCR website in reply to the anonymous email you sent:'."\r\n\r\n".$details['message']."\r\n\r\n______________\r\n\r\n".'The sender has no knowledge of your identity, however if you reply directly to this message the recipient will be able to identify you.  To reply to this email anonymously, visit {unwrap}'.site_url('contact/user/'.$this->session->userdata('id')).'{/unwrap}');
                $this->email->send();
                $this->load->view('contact/anon_reply', array('details' => $details, 'errors' => FALSE, 'success' => 'Reply Sent'));
            }
            else {
                $this->load->view('contact/anon_reply', array('details' => $details, 'errors' => TRUE, 'success' => FALSE));
                return;
            }
        }
        else {
            $this->load->view('contact/anon_reply', array('details' => $details, 'errors' => FALSE, 'success' => FALSE));
        }
    }
    
    private function get_users() {
        $this->db->select('users.id as id, firstname, prefname, surname');
        $this->db->from('users');
        $this->db->order_by('surname ASC');
        $this->db->join('level_list', 'level_list.user_id = users.id', 'inner');
        $users = $this->db->get()->result_array();
        $processed = array();
        foreach($users as $k => $v) {
            if(in_array($v['id'], $processed)) {
                unset($users[$k]);
            } else {
                $processed[] = $v['id'];
            }
        }
        foreach($users as &$u) $u['title'] = $this->users_model->get_levels_of_user($u['id'], TRUE);
        array_unshift($users,
            array('id' => 'jcr', 'firstname' => 'The', 'prefname' => '', 'surname' => 'JCR', 'title' => $this->email_addresses['jcr']),
            array('id' => 'welfare', 'firstname' => 'Butler', 'prefname' => '', 'surname' => 'Welfare', 'title' => $this->email_addresses['welfare'])
        );
        return $users;
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
/* End of file contact.php */
/* Location: ./application/controllers/contact.php */