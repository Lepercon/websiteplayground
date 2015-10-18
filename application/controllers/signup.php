<?php

class Signup extends CI_Controller {

    function Signup()
    {
        parent::__construct();
        $this->load->model('signup_model');
        $this->signup_model->delete_old_signups(); // maintenance
        $this->signup_model->remove_expired();
        $this->load->library('form_validation');
        $this->page_info = array(
            'id' => 10,
            'title' => 'Sign Up',
            'big_title' => NULL,
            'description' => 'Signup to upcoming Butler events',
            'requires_login' => TRUE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('signup/signup'),
            'js' => array('signup/signup'),
            'keep_cache' => FALSE,
            'editable' => FALSE
        );
    }

    function index()
    {
        $this->load->view('signup/signup_landing', array(
            'signups' => $this->signup_model->get_signups(),
            'admin' => $this->signup_model->check_permissions()
        )); //visible events only
    }

    function event($e_id = NULL){
    
        if(isset($_POST['requests-feeback']) && $_POST['requests-feeback'] !== ''){
            $this->load->library('email');
            
            $config['wordwrap'] = FALSE;
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            
            $nl = '<br>';        
            
            $mail = 'Dear Social Chair,'.$nl.$nl;
            $mail .= 'We have received feedback regarding formals at Josephine Butler College.'.$nl.$nl;
            $mail .= 'Comments:'.$nl.'<b>';
            $mail .= nl2br($_POST['requests-feeback']).'</b>'.$nl.$nl;
            $mail .= '<i>ID: '.$this->session->userdata('uid').'</i>';
            $mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
            
            $users = $this->users_model->get_users_with_level(16, 'users.email');
            $emails = '';
            foreach($users as $u){
                $emails .= ($emails == ''?'':',').$u['email'];
            }
            
            $this->email->to($emails);
            $this->email->cc('butler.jcr@durham.ac.uk');
            $this->email->bcc('samuel.stradling@durham.ac.uk');
            $this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
            $this->email->message($mail);
            $this->email->subject('Formals Feedback');
            
            if(ENVIRONMENT != 'development'){
                $this->email->send();
            }
            $_POST['feedback-success'] = TRUE;

        }

    
        if(is_null($e_id)) $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id);
        if($signup === FALSE) {
            $this->index();
            return;
        }
        $this->signup_model->add_attempt($e_id);
        if(!empty($signup['swap_price'])) $max_num_reservations = 2;
        else $max_num_reservations = 4;
        $errors = FALSE;
        $other_errors = array();
        if(validate_form_token('signup-event')) {
            if((isset($_POST['swap']) && ((time() >= $signup['swapping_opens'] && time() < $signup['swapping_closes']) || is_admin()))) {
                $this->form_validation->set_rules('pair1','Pair 1','trim|required');
                $this->form_validation->set_rules('pair2','Pair 2','trim|required');
                if($this->form_validation->run()) {
                    $check = $this->signup_model->check_swap($signup);
                    if($check === TRUE) {
                        $this->signup_model->add_swap($e_id);
                        //refresh signup
                        $signup = $this->signup_model->get_signup($e_id);
                    }
                    else {
                        $errors = TRUE;
                        $other_errors = $check;
                    }
                }
                else {
                    $errors = TRUE;
                }
            }
            elseif((time() >= $signup['signup_opens'] && time() < $signup['signup_closes']) OR ($this->signup_model->check_permission($signup) && (((time() + 15*60) < $signup['signup_opens'])) || time() > $signup['signup_closes'])) {
                if(isset($_POST['res'])) {
                    if((($signup['num_user_reservations'] + $max_num_reservations) <= ($signup['sets'] * $max_num_reservations)) OR ($this->signup_model->check_permission($signup))) { // check user doesn't have existing reservations
                        $this->form_validation->set_rules('reserve','Reservations','trim|required|integer|max_length[2]');
                        if(empty($signup['swap_price'])) $this->form_validation->set_rules('table','Table Number','trim|required|max_length[3]');
                        if($this->form_validation->run()) {
                            $check = $this->signup_model->check_reservation($signup);
                            if($check === TRUE) {
                                if($this->signup_model->add_reservation($e_id, $signup)){
                                    //refresh signup
                                    $signup = $this->signup_model->get_signup($e_id);
                                }else{
                                    $errors = TRUE;
                                    $other_errors = array('Not enough spaces on table.');
                                }
                            }
                            else {
                                $errors = TRUE;
                                $other_errors = $check;
                            }
                        }
                        else {
                            $errors = TRUE;
                        }
                    }else{
                        $errors = TRUE;
                        $other_errors = array('You have already made the maximum number of reservations.');
                    }
                }
                else if(isset($_POST['cancel'])) $this->signup_model->cancel_reservations($e_id, !empty($signup['swap_price']), $this->signup_model->check_permission($signup));
                else if(isset($_POST['details'])) { // reservation details

                    // Set Form validation Rules
                    $this->form_validation->set_rules('user_id','For','trim|integer|max_length[5]');
                    $this->form_validation->set_rules('name','Name','trim'.(!isset($_POST['user_id']) OR $_POST['user_id'] === '' ? '|required': '').'|max_length[100]|xss_clean');
                    foreach(array('starter','main','dessert','drink') as $r) $this->form_validation->set_rules($r,ucfirst($r),'trim|max_length[200]|xss_clean');
                    $this->form_validation->set_rules('pickup','Pickup Location','trim|max_length[100]|xss_clean');
                    $this->form_validation->set_rules('special','Special requirements','trim|max_length[100]|xss_clean');
                    if($this->form_validation->run()) {
                        $check = $this->signup_model->check_reservation_details($signup);
                        if($check === TRUE) {
                            $this->signup_model->add_reservation_details($e_id);
                            // refresh signup
                            $signup = $this->signup_model->get_signup($e_id);
                        }
                        else {
                            $errors = TRUE;
                            $other_errors = $check;
                        }
                    }
                    else $errors = TRUE;
                }
            }
        }
        $num_reservations = $this->signup_model->get_num_reservations($e_id);

        if($num_reservations > 0) {
            $res = $this->signup_model->get_first_reservation($e_id);
            $users = $this->users_model->get_all_user_ids_and_names();
        }
        else {
            $users = '';
            $res = '';
        }

        if($this->input->is_ajax_request() && (isset($_POST['cancel']) or isset($_POST['details']) or isset($_POST['res']))) $this->load->view('signup/booking_form', array(
            'e' => $signup,
            'num_reservations' => $num_reservations,
            'res' => $res,
            'token' => token_ip('signup-event'),
            'users' => $users,
            'errors' => $errors,
            'other_errors' => $other_errors
        ));
        else $this->load->view('signup/event', array(
            'e' => $signup,
            'num_reservations' => $num_reservations,
            'pairs' => (!empty($signup['swap_price']) ? $this->signup_model->get_group_reservations($e_id) : FALSE),
            'num_swaps' => (!empty($signup['swap_price']) ? $this->signup_model->get_num_swaps($e_id) : FALSE),
            'res' => $res,
            'users' => $users,
            'errors' => $errors,
            'other_errors' => $other_errors
        ));
    }

    function tables_refresh()
    {
        $signup_id = $this->uri->rsegment(3);
        if($signup_id === FALSE OR !$this->input->is_ajax_request()) {
            $this->signup($signup_id);
            return;
        }
        $this->load->view('signup/tables', array('e' => $this->signup_model->get_signup($signup_id)));
    }

    function new_signup()
    {
        if(!has_level('any')) {
            $this->index();
            return;
        }
        $e_id = $this->uri->rsegment(3);

        $this->load->model('admin_model');

        if(validate_form_token('new_signup')) {
            $other_errors = $this->signup_model->check_signup();
            if($other_errors === FALSE or !empty($other_errors)) {
                $this->load->view('signup/new_signup', array('e_id' => $e_id, 'errors' => TRUE, 'other_errors' => $other_errors, 'levels' => $this->admin_model->get_level_names(TRUE, TRUE)));
            }
            else {
                $id = $this->signup_model->save_signup();
                redirect('signup/event/'.$id);
            }
        }
        else $this->load->view('signup/new_signup', array('e_id' => $e_id, 'levels' => $this->admin_model->get_level_names(TRUE, TRUE)));
    }

    function edit_signup()
    {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $this->load->model('admin_model');
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->index();
            return;
        }
        if(validate_form_token('edit_signup')) {
            $other_errors = $this->signup_model->check_signup();
            if($other_errors === FALSE or !empty($other_errors)) {
                $this->load->view('signup/edit_signup', array('e' => $signup, 'errors' => TRUE, 'other_errors' => $other_errors, 'levels' => $this->admin_model->get_level_names(TRUE, TRUE)));
            }
            else {
                $id = $this->signup_model->save_signup($e_id, $signup);
                redirect('signup/event/'.$id);
            }
        } else $this->load->view('signup/edit_signup', array('e' => $signup, 'levels' => $this->admin_model->get_level_names(TRUE, TRUE)));
    }

    function food_choices()
    {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->index();
            return;
        }
        $reservations = $this->signup_model->get_reservations($e_id);
        if($this->uri->rsegment(4, 0) == 1) {
            $GLOBALS['controller_json'] = $this->load->view('signup/food_choices_csv', array('e' => $signup, 'reservations' => $reservations), TRUE);
            $GLOBALS['prevent_space_replace'] = TRUE;
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="'.$signup['name'].'.csv"');
        }
        else {
            $this->load->view('signup/food_choices', array('e' => $signup, 'reservations' => $reservations));
        }
    }

    function catering()
    {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->index();
            return;
        }
        $this->load->view('signup/catering', array('signup' => $signup, 'cater' => $this->signup_model->get_catering($e_id)));
    }

    function swap_totals()
    {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->index();
            return;
        }
        $names = array();
        $swaps = array();
        foreach($this->signup_model->get_swap_totals($e_id) as $s) $names[] = $s['movement_by'];
        foreach(array_count_values($names) as $k => $v) $swaps[] = array('name' => $this->users_model->get_full_name($k), 'number' => $v);
        foreach($swaps as $k => $v) {
            $name[$k] = $v['name'];
            $number[$k] = $v['number'];
        }
        array_multisort($name, SORT_ASC, $number, SORT_ASC, $swaps);
        $this->load->view('signup/swap_totals', array('e_id' => $e_id, 'swaps' => $swaps));
    }

    function swaps()
    {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->index();
            return;
        }
        $this->load->view('signup/movements', array('e_id' => $e_id, 'movements' => $this->signup_model->get_movements($e_id)));
    }

    function delete_booking()
    {
        $e_id = $this->uri->rsegment(3);
        $b_id = $this->uri->rsegment(4);
        if($e_id === FALSE OR $b_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->event($e_id);
            return;
        }
        $this->signup_model->delete_booking($e_id, $b_id);
        $this->event($e_id);
    }

    function cancel_signup()
    {
        $e_id = $this->uri->rsegment(3);
        if($e_id === FALSE) {
            $this->index();
            return;
        }
        $signup = $this->signup_model->get_signup($e_id, FALSE);
        if($this->signup_model->check_permission($signup) == FALSE) {
            $this->event($e_id);
            return;
        }
        if(validate_form_token('cancel_signup')) {
            if(isset($_POST['cancel'])) $this->signup_model->cancel_signup($e_id);
            $this->index();
            return;
        }
        $this->load->view('signup/cancel_signup', array('e_id' => $e_id));
    }
    
    function stats(){
        $e_id = $this->uri->rsegment(3);
        $attempts = $this->signup_model->get_attempts($e_id);
        $this->load->view('signup/stats', array(
            'e_id' => $e_id,
            'attempts' => $attempts
        ));

    }
}

/* End of file signup.php */
/* Location: ./system/application/controllers/signup.php */