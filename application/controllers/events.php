<?php

class Events extends CI_Controller {

    function Events() {
        parent::__construct();
        $this->load->model('events_model');
        $this->load->library('form_validation');
        $this->page_info = array(
            'id' => 3,
            'title' => 'Calendar',
            'big_title' => NULL,
            'description' => 'Butler JCR Event Calendar',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('events/events'),
            'js' => array('events/events'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    public function index($now = FALSE) {
        if($now === TRUE) {
            $year = date('Y');
            $month = date('n');
        }
        else {
            $year = $this->uri->segment(3, date('Y'));
            $month = $this->uri->segment(4, date('n'));
        }
        if(!is_numeric($year)) {
            $year = date('Y');
        }
        if(!is_numeric($month)) {
            $month = date('n');
        }
        $this->load->view('events/calendar', array(
            'data' => $this->events_model->get_month($year, $month)
        ));
    }

    public function ical() {
        $this->load->view('events/ical_instructions');
    }

    public function getcal() {
        $events = $this->events_model->get_events_by_category('all', 0);
$output = "BEGIN:VCALENDAR
VERSION:2.0
METHOD:PUBLISH
CALSCALE:GREGORIAN
X-PUBLISHED-TTL:PT3H
PRODID:-//ButlerJCR//ButlerJCR//EN\n";

        foreach($events as $e) {
$output .= "BEGIN:VEVENT
SUMMARY:".$e['name']."
UID:".$e['id']."
DESCRIPTION:".str_replace(array("\n", "\r"), '', str_replace(",", "\,", str_replace("\"", "DQUOTE", str_replace(";", "\;", $e['description']))))."
LOCATION:".str_replace(array("\n", "\r"), '', str_replace(",", "\,", str_replace("\"", "DQUOTE", str_replace(";", "\;", $e['location']))))."
URL:".site_url('events/view_event/'.$e['id'])."
DTSTART:".gmdate('Ymd\THis\Z', $e['time'])."
DTEND:".($e['end'] < time() ? gmdate('Ymd\THis\Z', $e['time'] + 60 * 60) : (date('H:i', $e['time']) == '00:00' ? gmdate('Ymd\THis\Z', $e['time'] + 24 * 60 * 60) : gmdate('Ymd\THis\Z', $e['end']))).(date('H:i', $e['time']) == '00:00' ? "\n".'X-MICROSOFT-CDO-ALLDAYEVENT:TRUE' : '')."
END:VEVENT\n";
        }

$output .= "END:VCALENDAR";

        $GLOBALS['controller_json'] = $output;
        $GLOBALS['prevent_space_replace'] = TRUE;
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=butlercal.ics');
    }

    public function getallcal() {
        $events = $this->events_model->get_events_by_category('all', 0, FALSE);
$output = "BEGIN:VCALENDAR
VERSION:2.0
METHOD:PUBLISH
CALSCALE:GREGORIAN
X-PUBLISHED-TTL:PT3H
PRODID:-//ButlerJCR//ButlerJCR//EN\n";

        foreach($events as $e) {
$output .= "BEGIN:VEVENT
SUMMARY:".$e['name']."
UID:".$e['id']."
DESCRIPTION:".str_replace(array("\n", "\r"), '', str_replace(",", "\,", str_replace("\"", "DQUOTE", str_replace(";", "\;", $e['description']))))."
LOCATION:".str_replace(array("\n", "\r"), '', str_replace(",", "\,", str_replace("\"", "DQUOTE", str_replace(";", "\;", $e['location']))))."
URL:".site_url('events/view_event/'.$e['id'])."
DTSTART:".gmdate('Ymd\THis\Z', $e['time'])."
DTEND:".($e['end'] < time() ? gmdate('Ymd\THis\Z', $e['time'] + 60 * 60) : (date('H:i', $e['time']) == '00:00' ? gmdate('Ymd\THis\Z', $e['time'] + 86400) : ($e['end'] > $e['time'] ? gmdate('Ymd\THis\Z', $e['end']) : gmdate('Ymd\THis\Z', $e['time'] + 3600)))).(date('H:i', $e['time']) == '00:00' ? "\n".'X-MICROSOFT-CDO-ALLDAYEVENT:TRUE' : '')."
END:VEVENT\n";
        }

$output .= "END:VCALENDAR";

        $GLOBALS['controller_json'] = $output;
        $GLOBALS['prevent_space_replace'] = TRUE;
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=butlercal.ics');
    }

    public function view_event($e_id = NULL, $errors = FALSE) {
        if(is_null($e_id)) {
            $e_id = $this->uri->rsegment(3);
        }
        if($e_id == FALSE) {
            $this->index(TRUE);
            return;
        }
        $event = $this->events_model->get_event($e_id);
        if($event == FALSE) {
            $this->index(TRUE);
            return;
        }
        foreach(array('projects_model','signup_model') as $m)
        $this->load->model($m);

        $this->load->view('events/view_event', array(
            'event' => $event,
            'posts' => $this->events_model->get_posts($e_id),
            'signups' => $this->signup_model->get_signups($e_id),
            'requests' => $this->projects_model->get_requests(FALSE, 'all', $e_id),
            'files' => $this->events_model->get_files($e_id),
            'photos' => $this->events_model->get_photos($e_id),
            'questionnaires' => $this->events_model->get_questionnaire_by_event($e_id)
        ));
    }

    public function add_event($skip = FALSE) {
        if(!logged_in()) {
            $this->index(TRUE);
            return;
        }
        $errors = FALSE;
        $upload_errors = '';
        if($skip == FALSE && ($this->input->post('add_event') !== FALSE or $this->input->post('add_another_event') !== FALSE) && validate_form_token('add_event')) {
            $this->set_event_validation();
            if($this->form_validation->run()) {    
                
                $upload_errors = $this->poster_upload();
                
                if($upload_errors == ''){
                    $e_id = $this->events_model->add_event();
                    if($this->input->post('add_event') !== FALSE) {
                        redirect('events/view_event/'.$e_id);
                        return;
                    }
                }
            }
            else $errors = TRUE;
        }
                
        $this->load->model('admin_model');
        $this->load->view('events/add_event', array(
            'errors' => $errors,
            'categories' => $this->events_model->get_categories(),
            'levels' => $this->admin_model->get_level_names(TRUE),
            'upload_errors' => $upload_errors
        ));
    }

    public function edit_event($e_id = NULL) {
        if(!logged_in()) {
            $this->index(TRUE);
            return;
        }
        if(is_null($e_id)) {
            $e_id = $this->uri->rsegment(3);
        }
        if($e_id == FALSE) {
            $this->index(TRUE);
            return;
        }
        $event = $this->events_model->get_event($e_id);
        if($event == FALSE) {
            $this->index(TRUE);
            return;
        }
        $errors = FALSE;
        $upload_errors = '';
        if($this->input->post('edit_event') != FALSE && validate_form_token('edit_event')) {
            $this->set_event_validation();
            if($this->form_validation->run()) {
                
                $upload_errors = $this->poster_upload();
                
                if($upload_errors === ''){
                    $this->events_model->edit_event($e_id);
                    redirect('events/view_event/'.$e_id);
                    return;
                }
            }else 
                $errors = TRUE;
        }
        $this->load->model('admin_model');
        $this->load->view('events/edit_event', array(
            'errors' => $errors,
            'categories' => $this->events_model->get_categories(),
            'e' => $event,
            'levels' => $this->admin_model->get_level_names(TRUE),
            'upload_errors' => $upload_errors
        ));
    }
    
    public function poster_upload(){
    
        $this->load->library('image_lib');
    
        $config = array(
            'upload_path'=>'./application/views/events/posters/',
            'allowed_types' => 'gif|jpg|png',
            'max_size' => '8191',
            'encrypt_name' => TRUE
        );
        $this->load->library('upload', $config);
        
        if (isset($_FILES['userfile']) and !empty($_FILES['userfile']['name'])){
            if(!$this->upload->do_upload()){
                return $this->upload->display_errors();
            }else{
            
                $data = $this->upload->data();
                
                $config['source_image']    = $data['full_path'];
                $config['maintain_ratio'] = TRUE;
                $config['master_dim'] = 'height';
                $config['image_library'] = 'gd2';
                $config['quality'] = '100%';
                
                $config['new_image'] = $data['file_path'].$data['raw_name'].'_800px'.$data['file_ext'];                
                $config['height'] = 800;
                $config['width'] = 1800;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                $this->image_lib->clear();
                
                $config['source_image']    = $config['new_image'];
                $config['new_image'] = $data['file_path'].$data['raw_name'].'_300px'.$data['file_ext'];
                $config['height'] = 300;
                $config['width'] = 1080;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                $this->image_lib->clear();
                
                $_POST['file'] = array('upload_data' => $data);
                unlink($data['full_path']);
            }
        }
        return '';
    }

    public function cancel_event() {
        if(!is_admin()) {
            $this->index(TRUE);
            return;
        }
        $e_id = $this->uri->rsegment(3);
        if($e_id == FALSE) {
            $this->index(TRUE);
            return;
        }
        $this->events_model->cancel_event($e_id);
        $this->index(TRUE);
    }

    public function add_post() {
        if(!logged_in()) {
            $this->view_event($this->input->post('event_id'));
            return;
        }
        $errors = FALSE;
        if(validate_form_token('news_post') && $this->input->post('save_post') != FALSE) {
            $this->form_validation->set_rules('title', 'Post Title', 'trim|max_length[50]|required|xss_clean');
            $this->form_validation->set_rules('content', 'Post Content', 'trim|required|xss_clean');
            $this->form_validation->set_rules('event_id', 'Event ID', 'required|integer');
            if($this->form_validation->run()) {
                $this->events_model->add_post();
            }
            else $errors = TRUE;
        }
        $this->view_event($this->input->post('event_id'), $errors);
    }

    public function delete_post() {
        $p_id = $this->uri->segment(3);
        if(!is_admin() or $p_id == FALSE) $this->index(TRUE);
        $post = $this->events_model->get_post($p_id);
        if(!empty($post)) {
            $this->events_model->delete_post($p_id);
            $this->view_event($post['event_id']);
        }
        else $this->index(TRUE);
    }

    public function download_photos() {
        if($e_id = $this->uri->segment(3)) {
            $photos = $this->events_model->get_photos($e_id);
            if(!empty($photos)) {
                $this->load->library('zip');
                $path = VIEW_PATH.'events/photos/event_'.$e_id.'/';
                foreach($photos as $p) {
                    $this->zip->read_file($path.$p['filename'].'_large.jpg');
                }
                $this->zip->download('event_'.$e_id.'_photos.zip');
                exit;
            }
        }
    }

    public function manage_photos($e_id = NULL) {
        if(!is_admin()) {
            $this->index();
            return;
        }
        if(is_null($e_id)) {
            $e_id = $this->uri->segment(3);
            if($e_id == FALSE) {
                $this->index();
                return;
            }
        }
        $photos = $this->events_model->get_photos($e_id);
        if(empty($photos)) {
            $this->view_event($e_id);
        }
        else {
            $errors = FALSE;
            if($this->input->post('description') != FALSE && validate_form_token('manage_photos')) {
                $this->form_validation->set_rules('description', 'Description', 'trim|xss_clean|max_length[200]');
                $this->form_validation->set_rules('image_id', 'Image ID error - contact the administrator', 'required|max_length[11]');
                if($this->form_validation->run()) {
                    $this->events_model->update_photo($e_id, $this->input->post('image_id'));
                }
                else {
                    $errors = TRUE;
                }
            }
            $this->load->view('events/manage_photos', array('errors' => $errors, 'e_id' => $e_id, 'photos' => $photos));
        }
    }

    public function delete_photo() {
        $e_id = $this->uri->segment(3);
        $p_id = $this->uri->segment(4);
        if($e_id == FALSE) {
            $this->index();
            return;
        }
        if($p_id == FALSE or !is_admin()) {
            $this->view_event($e_id);
            return;
        }
        $this->events_model->delete_photo($e_id, $p_id);
        $this->manage_photos($e_id);
    }

    public function add_photo() {
        $e_id = $this->uri->segment(3);
        if(!logged_in() or $e_id == FALSE) {
            $this->index(TRUE);
            return;
        }
        $errors = FALSE;
        $success = FALSE;
        $other_errors = array();
        if($this->input->post('add_photo') != FALSE && validate_form_token('add_photo')) {
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[200]|xss_clean');
            if($this->form_validation->run()) {
                $config = array(
                    'upload_path'    => VIEW_PATH.'events/photos/event_'.$e_id.'/',
                    'allowed_types'    => 'jpg|jpeg|png',
                    'file_name'        => rand_alphanumeric(10),
                    'overwrite'        => FALSE, // if file of same name already exists, overwrite it
                    'max_size'        => '8192', // 8MB
                    'max_width'        => '0', // No limit on width
                    'max_height'    => '0' // No limit on height
                );
                $this->load->library('upload', $config);
                if(!file_exists(VIEW_PATH.'events/photos/event_'.$e_id)) mkdir(VIEW_PATH.'events/photos/event_'.$e_id);
                if($this->upload->do_upload()) {
                    $image_data = $this->upload->data();
                    foreach(array(100 => 'thumb', 1500 => 'large') as $k => $v) {
                        $img = new Imagick($image_data['full_path']);
                        $img->resizeImage($k, 0, Imagick::FILTER_LANCZOS, 1);
                        $geo = $img->getImageGeometry();
                        if(strtolower($img->getImageFormat()) != 'jpeg') {
                            // change format to jpg if required
                            $img->setCompressionQuality(90);
                            $img->setImageFormat('jpeg');
                        }
                        // save resized file
                        $img->writeImage(VIEW_PATH.'events/photos/event_'.$e_id.'/'.$image_data['raw_name'].'_'.$v.'.jpg');
                    }
                    // delete original upload
                    unlink($image_data['full_path']);
                    $this->events_model->add_photo($e_id, $image_data['raw_name']);
                    $success = $image_data['client_name'].' Uploaded Successfully. You can upload another or go back to the event.';
                }
                else {
                    $errors = TRUE;
                    $other_errors = array($this->upload->display_errors());
                }
            }
            else $errors = TRUE;
        }
        $this->load->view('events/add_photo', array(
            'errors' => $errors,
            'other_errors' => $other_errors,
            'success' => $success,
            'e_id' => $e_id
        ));
    }

    public function delete_file() {
        $e_id = $this->uri->segment(3);
        $f_id = $this->uri->segment(4);
        if($e_id == FALSE) {
            $this->index();
            return;
        }
        if($f_id != FALSE && is_admin()) {
            $this->events_model->delete_file($e_id, $f_id);
        }
        $this->view_event($e_id);
    }

    public function add_file() {
        $e_id = $this->uri->segment(3);
        if(!logged_in() or $e_id == FALSE) {
            $this->index(TRUE);
            return;
        }
        $errors = FALSE;
        $success = FALSE;
        $other_errors = array();
        if($this->input->post('add_file') != FALSE && validate_form_token('add_file')) {
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[200]|xss_clean');
            if($this->form_validation->run()) {
                $config = array(
                    'upload_path'    => VIEW_PATH.'events/files/event_'.$e_id.'/',
                    'allowed_types'    => '*',
                    'overwrite'        => FALSE, // if file of same name already exists, overwrite it
                    'max_size'        => '8192', // 8MB
                    'max_width'        => '0', // No limit on width
                    'max_height'    => '0' // No limit on height
                );
                $this->load->library('upload', $config);
                if(!file_exists(VIEW_PATH.'events/files/event_'.$e_id)) mkdir(VIEW_PATH.'events/files/event_'.$e_id);
                if($this->upload->do_upload()) {
                    $file_data = $this->upload->data();
                    $this->events_model->add_file($e_id, $file_data['file_name']);
                    $success = $file_data['client_name'].' Uploaded Successfully. You can upload another file or go back to the event.';
                }
                else {
                    $errors = TRUE;
                    $other_errors = array($this->upload->display_errors());
                }
            }
            else $errors = TRUE;
        }
        $this->load->view('events/add_files', array(
            'errors' => $errors,
            'other_errors' => $other_errors,
            'success' => $success,
            'e_id' => $e_id
        ));
    }

    private function set_event_validation() {
        $this->form_validation->set_rules('name', 'Event name', 'trim|required|max_length[30]|xss_clean');
        $this->form_validation->set_rules('date', 'Date', 'required|trim|max_length[10]|xss_clean|callback_check_valid_date');
        $this->form_validation->set_rules('hour', 'Hour', 'required|trim|max_length[2]|integer|greater_than[-1]|less_than[24]');
        $this->form_validation->set_rules('minute', 'Minute', 'required|trim|max_length[2]|integer|greater_than[-1]|less_than[60]');
        $this->form_validation->set_rules('category','Category','trim|required|integer|max_length[3]');
        $this->form_validation->set_rules('description', 'Event Description', 'trim|xss_clean|max_length[1000]');
        $this->form_validation->set_rules('location', 'Location', 'trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('facebook_url', 'Facebook URL', 'trim|xss_clean|max_length[100]|callback_prep_domain');
        $this->form_validation->set_rules('twitter_handle', 'Twitter handle', 'trim|xss_clean|max_length[100]|callback_prep_domain');
    }

    function prep_domain($url) {
        $url = strtolower($url);
        if(empty($url)) return TRUE;
        if($url == 'https://www.facebook.com/') return '';
        if(strpos($url, 'twitter.com') !== FALSE) {
            $url = str_replace($url, 'https://twitter.com/', '');
            $url = str_replace($url, 'http://twitter.com/', '');
            $url = str_replace($url, 'https://www.twitter.com/', '');
            $url = str_replace($url, 'http://www.twitter.com/', '');
        }
        return $url;
    }

    function check_valid_date($date) {
        $date = explode("/", $date);
        if(!checkdate($date[1], $date[0], $date[2])) {
            $this->form_validation->set_message('check_valid_date', 'The date you have entered is not valid.');
            return FALSE;
        }
        else return TRUE;
    }
    
    function delete_poster(){
        $event_id = $this->input->post('event_id');
        if(has_level('any') && $this->events_model->remove_poster($event_id)){
            return true;
        }else{
            http_response_code(400);
        }
    }
    
    function event_guide(){
        $access_rights = has_level('any');
        $this->load->view('events/event_guide', array(
            'access_rights' => $access_rights
        ));
    }
}

/* End of file events.php */
/* Location: ./system/application/controllers/events.php */