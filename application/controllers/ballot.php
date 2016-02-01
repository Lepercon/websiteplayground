<?php

class Ballot extends CI_Controller {

    function Ballot () {
        parent::__construct();
        $this->load->model('ballot_model');
        $this->page_info = array(
            'id' => 32,
            'title' => 'Ballot',
            'big_title' => NULL,
            'description' => '',
            'requires_login' => TRUE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('ballot/ballot'),
            'js' => array('ballot/ballot'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
        $this->ballot_admin = is_admin();
        $this->load->helper('html');
    }

    function index() {
    
        $ballots = $this->ballot_model->get_ballots();
        $this->load->view('ballot/ballot', array(
            'ballots'=>$ballots
        ));
    }
    
    function view_ballot(){        
            
        $id = $this->uri->segment(3);
        
        if($id == 4 && $this->session->userdata('year_group') == 1){
            cshow_error('Sorry, freshers are unable to sign up to this formal.', 401, 'Access denied');
            return;
        }
        $u_id = $this->session->userdata('id'); 
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        $_SESSION['errors'] = array();
        $this->ballot_model->signup_check($ballot['id']);
    
        if($ballot['open_time'] < time() && $ballot['close_time'] > time()){
            
            if($this->ballot_model->signup_check($ballot['id'])){
            
                $i = 1;
                $user = array();
                $num_guests = 0;
                while(isset($_POST['person-'.$i]) && $i <= $ballot['max_group']){
                    if($_POST['person-'.$i] !== ''){
                        if($_POST['id-'.$i] == -1){
                            $index = 'id-'.$i;
                            if($num_guests >= $ballot['max_guests']){
                                $_SESSION['errors'][] = 'You have already signed up the maximum number of guests.';
                                $i++;
                                continue;
                            }
                            $num_guests++;
                        }else{
                            $index = $_POST['id-'.$i];
                            if(empty($index)){
                                $i++;
                                if($ballot['allow_guests']){
                                    $_SESSION['errors'][] = 'If you are tying to sign up a guest, just type "Guest" in the box';
                                }else{
                                    $_SESSION['errors'][] = 'You must enter select a name from the drop down list after you have started typing.';
                                }
                                continue;
                            }
                        }
                        if($_POST['id-'.$i] == -1){
                            $user[$index]['name'] = $_POST['guestname-'.$i];
                        }else{
                            $user[$index]['name'] = $_POST['person-'.$i];
                        }
                        $user[$index]['requirements'] = $_POST['requirements-'.$i];
                        $user[$index]['user_id'] = $_POST['id-'.$i];
                        $j = 0;
                        $user[$index]['options'] = '';
                        while(isset($_POST['option-'.$i.'-'.$j])){
                            $user[$index]['options'] .= ($user[$index]['options']==''?'':';').$_POST['option-'.$i.'-'.$j];
                            $j++;
                        }
                        $user[$index]['created_by'] = $u_id;
                        $user[$index]['ballot_id'] = $id;
                        $user[$index]['split_group'] = $_POST['split-group'];
                        $user[$index]['timestamp'] = time();
                        $user[$index]['priority_score'] = $this->ballot_model->get_priority($id, $user[$index]['user_id']);
                    }
                    $i++;
                }

                if(!empty($user)){
                    $this->ballot_model->update_ballot($id, $u_id, $user);
                }
            }
        
        }
        
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        $users = $this->users_model->get_all_user_ids_and_names();
        $tables = $this->ballot_model->get_tables($id);
        
        $this->load->view('ballot/view_ballot', array(
            'b'=>$ballot,
            'users'=>$users,
            'user_id' => $u_id
        ));
        
        $this->get_tables();
        /*$this->load->view('ballot/view_tables', array(
            'tables'=>$tables,
            'u_id'=>$u_id,
            'b'=>$ballot
        ));*/
        
    }
    
    function create(){
        
        if(!$this->ballot_admin){
            redirect('ballot');
            return;
        }
        
        if($this->input->post('submit') != FALSE){
            $id = $this->ballot_model->create_signup();
            redirect('ballot/view_ballot/'.$id);
            return;
        }
        
        $this->load->view('ballot/create', array(
            'events' => $this->ballot_model->get_events()
        ));
        
    }
    
    function view_signups(){
        $id = $this->uri->segment(3);
        $u_id = $this->session->userdata('id'); 
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        
        if($this->ballot_admin){
            $people = $this->ballot_model->get_people($id, $this->uri->segment(4));
            $this->load->view('ballot/view_people', array(
                'b' => $ballot,
                'people' => $people
            ));
        }else{
            redirect('ballot');
        }
    }
    
    function get_tables(){
        
        $id = $this->uri->segment(3);
        $u_id = $this->session->userdata('id'); 
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        $ballot_open = $ballot['close_time'] > time();
        
        if(!$ballot_open){
            $this->ballot_model->table_assignment2($id);
            $tables = $this->ballot_model->get_tables($id, $ballot_open, $ballot_open);
            
            $this->load->view('ballot/view_tables', array(
                'tables'=>$tables,
                'u_id'=>$u_id,
                'b'=>$ballot
            ));
        }
        
    }
    
    function email(){
        $id = $this->uri->segment(3);
        $u_id = $this->session->userdata('id'); 
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        $this->load->view('ballot/email', array('ballot'=>$ballot));
    }
    
    function payments(){
        $id = $this->uri->segment(3);
        $u_id = $this->session->userdata('id'); 
        $ballot = $this->ballot_model->get_ballot($id, $u_id);
        
        if($ballot['close_time'] < time()){
            if($this->ballot_admin){
                $payments = $this->ballot_model->get_payments($id);
                
                if(isset($_POST['send-invoices'])){
                    $this->ballot_model->send_invoices($ballot, $payments['not_sent']);
                    $payments = $this->ballot_model->get_payments($id);
                }                
                
                $this->load->view('ballot/payments', array(
                    'b' => $ballot,
                    'payments' => $payments
                ));
            }else{
                redirect('ballot');
            }
        }else{
            redirect('ballot/view/'.$ballot['id']);
        }
    }
    
    function payment(){
        if($this->ballot_admin){
            
            $methods = array(
                '' => NULL,
                'bank' => 'bank_transfer',
                'cash' => 'cash', 
                'cheque' => 'cheque',
                'cheque_college' => 'cheque_college'
            );
            
            $data['paid'] = $this->input->post('mark_paid');
            $data['payment_method'] = $methods[$this->input->post('method')];
            $data['marked_by'] = $this->session->userdata('id');
            $id = $this->input->post('id');
            $this->db->update('invoices', $data, array('id'=>$id));
        }
    }
        
}

/* End of file ballot.php */
/* Location: ./application/controllers/ballot.php */