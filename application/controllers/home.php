<?php class Home extends CI_Controller {

    function Home() {
        parent::__construct();
        $this->page_info = array(
            'id' => 1,
            'title' => 'Home',
            'big_title' => NULL,
            'description' => 'The Josephine Butler college JCR is the student run body of the newest college in Durham University, taking in roughly 230 new students each year.',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('home/home'),
            'js' => array('home/home'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
        $this->load->model('events_model');
        $this->load->model('green_model');
        $this->load->helper('html');
        $this->load->library('page_edit_auth');
        $this->load->model('photos_model');
        $this->load->view('home/home', array(
            'tip' => $this->green_model->get_tip(),
            'data' => $this->events_model->get_week(),
            'news' => $this->events_model->get_posts('all', 5),
            'access_rights' => $this->page_edit_auth->authenticate('home'),
            'posters' => $this->events_model->get_event_posters()
        ));
    }

    function error_404() {
        log_message('error', $_SERVER['REQUEST_URI']);
        $this->load->view('home/error_404');
    }

    function logout() {
        $this->session->sess_destroy();
        $this->input->set_cookie('username', '', '');
        $this->input->set_cookie('rand', '', '');
        if($this->input->is_ajax_request()) {
            //ajax
            echo json_encode(array('redirect' => str_replace('https://', 'http://', BASE_URL)));
        }
        else header('Location: '.str_replace('https://', 'http://', BASE_URL));
        exit;
    }

    function cookies() {
        $this->load->view('home/cookies');
    }

    function info() {
        $this->load->view('home/info');
    }
    
    function banner(){
        $this->load->helper('html');
        $this->load->model(array('events_model', 'messages_model'));
        $this->load->view('home/banner_view', array(
            'posters' => $this->events_model->get_event_posters(15),
            'messages' => $this->messages_model->get_messages()
        ));
    }
    
    function get_time(){
        $this->load->view('home/time');
    }
    
    function touchdevice(){
        $this->load->view('home/touchdevice');
    }
    
    function cookie_prompt(){
        $this->input->set_cookie('cookiepopup', 'shown-popup', 31536000);
    }
    
    function new_status(){
        if(has_level('any')){
            
            $this->load->model('events_model');
            $this->load->helper('smiley');
            
            $_POST['event_id'] = NULL;
            $post = $this->events_model->add_post();
            $post['content'] = parse_smileys($post['content'], VIEW_URL.'common/smileys/');
            $this->load->view('events/post', array('post' => $post, 'show_link' => FALSE));
        }
    }
    
    function christmas(){
        $this->load->view('home/christmas');
        
        $this->load->library('email');

        $this->email->from($this->session->userdata('email'), $this->session->userdata('firstname').' '.$this->session->userdata('surname'));
        $this->email->to('samuel.stradling@durham.ac.uk'); 

        $this->email->subject('I Clicked A Snowflake');
        $this->email->message('I Clicked A Snowflake!');    

        $this->email->send();
    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */