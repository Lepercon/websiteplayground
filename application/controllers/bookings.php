<?php

class Bookings extends CI_Controller {
    
    function Bookings() {
        parent::__construct();
        $this->load->model('bookings_model');
        $this->page_info = array(
            'id' => 31,
            'title' => 'Room Bookings',
            'big_title' => '<span class="big-text-small">Room Bookings</span>',
            'description' => '',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => FALSE,
            'css' => array('bookings/bookings'),
            'js' => array('bookings/bookings'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }
    function index() {
       $this->load->view('bookings/home'); 
    }
	
    function forms() {
        $this->load->view('bookings/bookings_forms');
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
        
    function tsandcs() {
        $this->load->view('bookings/tsandcs');
    }
	
    function book(){
        $booking_screen = $this->input->post('Phone_number') == FALSE;
        if(!$booking_screen){
            $details = $this->bookings_model->get_submitted_details();
            $s_time = $details['booking_start'] % (60*60*24);
            $e_time = $details['booking_end'] % (60*60*24);
            $s_date = $details['booking_start'] - $s_time;
            $e_date = $details['booking_end'] - $e_time;
            //$booking_screen = $this->bookings_model->check_clash($details);
        }
        if ($booking_screen || isset ($GLOBALS['errors'])) {
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
            $this->db->where('id', $details['room_id']);
            $room = $this->db->get('bookings_rooms')->row_array(0);
            if (!$room['JCR_owned']){
                $this->bookings_model->send_email($message, $details);
            }
            $this->load->view('bookings/successful', array('details'=>$details));
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

    function excel(){
        if(!(has_level(array(164, 166)) OR is_admin())){
            redirect('bookings');
            return;
        }
        $this->load->library('PHPExcel');
        $this->load->view('bookings/read_excel');
    }

    function add_booking(){
        $booking_screen = $this->input->post('Title') == FALSE;
        if(!$booking_screen){
            $details = $this->bookings_model->get_submitted_details();
            $s_time = $details['booking_start'] % (60*60*24);
            $e_time = $details['booking_end'] % (60*60*24);
            $s_date = $details['booking_start'] - $s_time;
            $e_date = $details['booking_end'] - $e_time;
        }
        if ($booking_screen){
        $this->load->view('bookings/definite_upload', array(
                        'rooms' => $this->bookings_model->get_rooms(), 
                        'reservations' => $this->bookings_model->get_bookings()
                        ));
        }
        else{
            $this->bookings_model->enter_data($details);
            $this->load->view('bookings/definite_upload_done', array('details'=>$details));
        }

    }

    function upload()
    {
        if(!is_admin()){
            redirect('bookings');
            return;
        }
        $config['upload_path'] = 'application/views/bookings/files/';
        $config['allowed_types'] = 'xlsx';
        //$config['file_name'] = date('Excel Upload Y.m.d H:i:s').'.xlsx';
        $config['encrypt_name'] = True;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());
            $this->load->view('bookings/excel_upload_form', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $this->load->view('bookings/excel_upload_success', $data);
            $this->load->library('PHPExcel');
            $this->load->view('bookings/read_excel', array(
                'path' => $data['upload_data']['full_path']
            ));
        }
    }
}