<?php

class Bookings extends CI_Controller {
    
    function Bookings() {
        parent::__construct();
        $this->load->model('bookings_model');
    }
    function index() {
        $error = array();
            if ($this->input->post('mysubmit') !== FALSE || $this->input->post('confirm_booking') !== FALSE){
            $details = $this->bookings_model->get_submitted_details();
            $error = $this->bookings_model->error_check($details);
        }
        if ($this->input->post('mysubmit') !== FALSE && $this->input->post('confirm_booking') == FALSE && empty($error)) {
            if (in_array($details['room_id'], array(1,2,3,5,11)) && $details['Layout'] == FALSE && $details['Equiptment'] == FALSE){
                $this->load->view('bookings/layout', array('details'=>$details, 'rooms' => $this->bookings_model->get_rooms(), 'layouts' => $this->bookings_model->get_layouts(), 'equiptment' => $this->bookings_model->get_equiptment()));
            }else{
                $this->load->view('bookings/confirm', array('details'=>$details));
            }
        }elseif($this->input->post('confirm_booking') !== FALSE && empty($error)){
            $this->bookings_model->enter_data($details);
            $this->load->view('bookings/successful');
        }else{
            $this->load->view('bookings/view_bookings', array(
                'rooms' => $this->bookings_model->get_rooms(), 
                'reservations' => $this->bookings_model->get_bookings(),
                'error' => $error
            ));
        }
    }
    function room(){
    }
}