<?php

class Bookings extends CI_Controller {
    
    function Bookings() {
        parent::__construct();
        $this->load->model('bookings_model');
    }
    function index() {
       $this->load->view('bookings/home'); 
    }
	function calender() {
		$year = $this->uri->segment(3, date('Y'));
		$month = $this->uri->segment(4, date('m'));
		$day = $this->uri->segment(5, date('d'));
		$date = mktime(12, 0, 0, $month, $day, $year);
		$this->load->view('bookings/calender', array(
			'rooms' => $this->bookings_model->get_rooms(),
			'date'=>$date
		));
	}
	
	function book(){
            $booking_screen = $this->input->post('Phone_number') == FALSE;
            if(!$booking_screen){
                $details = $this->bookings_model->get_submitted_details();
                if ($this->input->post('booking_start') > $this->input->post('booking_end')){
                    $GLOBALS['errors'][] = 'Start date after end date';
                    $booking_screen = TRUE;
                }
                $s_time = $this->input->post('s_hour') * 60 + $this->input->post('s_min');
                $e_time = $this->input->post('e_hour') * 60 + $this->input->post('e_min');
                if ($s_time > $s_time){  
                        $GLOBALS['errors'][] = 'Start time after end time';
                }
                $this->bookings_model->check_clash($details);
            }
            if ($booking_screen || isset($GLOBALS['errors'])){
                    $this->load->view('bookings/book_room', array(
                            'rooms' => $this->bookings_model->get_rooms(), 
                            'reservations' => $this->bookings_model->get_bookings(),
                            'layouts' => $this->bookings_model->get_layouts(),
                            'equiptment' => $this->bookings_model->get_equiptment()
                            ));
            }
            else{
                $id = $this->bookings_model->enter_data($details);
		$message = $this->load->view('bookings/Email', array(
                                                                    'b' => $this->bookings_model->get_booking($id),
                                                                    'room' => $this->bookings_model->get_rooms(),
                                                                    'layout' => $this->bookings_model->get_layouts(),
                                                                    'equiptment' => $this->bookings_model->get_equiptment()
                                                                    ), true);
                //$this->bookings_model->send_email($message, $details);
                $this->load->view('bookings/successful');
            }
	}
	
	function book_old() {
		$error = array();
		if ($this->input->post('mysubmit') !== FALSE || $this->input->post('confirm_booking') !== FALSE){
			//$details = $this->bookings_model->get_submitted_details();
			//$error = $this->bookings_model->error_check($details);
		}
		if (($this->input->post('main_details') !== FALSE || $this->input->post('extra_details') !== FALSE) && $this->input->post('confirm_booking') == FALSE && empty($error)) {
				$details = $this->bookings_model->get_room_id();
			if (in_array($details['room_id'], array(1,2,3,5,11)) && $this->input->post('extra_details') === FALSE){
				$details = $this->bookings_model->get_submitted_details();
				$this->load->view('bookings/layout', array('details'=>$details,
					'rooms' => $this->bookings_model->get_rooms(),
					'layouts' => $this->bookings_model->get_layouts(),
					'equiptment' => $this->bookings_model->get_equiptment()
				));
			}else{
				if ($this->input->post('extra_details') !== FALSE){
					$details = $this->bookings_model->input_equipt_layout();
				}else{
					$details = $this->bookings_model->get_submitted_details();
				}
				$this->load->view('bookings/confirm', array('details'=>$details));
			}
		}elseif($this->input->post('confirm_booking') !== FALSE && empty($error)){ 
			$details = $this->bookings_model->confirm_details();
			$id = $this->bookings_model->enter_data($details);
			$message = $this->load->view('bookings/Email', array(
				'b' => $this->bookings_model->get_booking($id),
				'room' => $this->bookings_model->get_rooms(),
				'layout' => $this->bookings_model->get_layouts(),
				'equiptment' => $this->bookings_model->get_equiptment()
			), true);
			//$this->bookings_model->send_email($message, $details);
			$this->load->view('bookings/successful');
		}else{
			$this->load->view('bookings/view_bookings', array(
				'rooms' => $this->bookings_model->get_rooms(), 
				'reservations' => $this->bookings_model->get_bookings(),
				'error' => $error
			));
		}
	}
	function email(){
		$id = $this->uri->segment(3);
		$this->load->view('bookings/Email', array(
				'b' => $this->bookings_model->get_booking($id),
				'room' => $this->bookings_model->get_rooms(),
				'layout' => $this->bookings_model->get_layouts(),
				'equiptment' => $this->bookings_model->get_equiptment()
			));
	}
}