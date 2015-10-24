<?php

class Post_controller_constructor {

    var $ci;

    function Post_controller_constructor()
    {
        $this->ci =& get_instance();
        $this->ci->benchmark->mark('pre_controller_start');
        
        set_include_path(get_include_path() . PATH_SEPARATOR . '/application/libraries/drive/src');

        // ie6 redirect
        if($this->ci->agent->browser() == 'Internet Explorer' && intval($this->ci->agent->version()) < 7) {
            include(APPPATH.'errors/ie6.php');
            exit;
        }
        /*
        // if logged in, check session has not expired
        if(logged_in()) {
            // session has expired or tokens not equal, so logout
            if($this->ci->session->userdata('username') == FALSE or $this->ci->session->userdata('rand') == FALSE) $this->logout();
            $this->ci->db->where(array('username' => $this->ci->session->userdata('username'), 'rand' => $this->ci->session->userdata('rand')));
            $query = $this->ci->db->get('users');
            $result = $query->row_array(0);
            if($query->num_rows() == 0) {
                // not valid login, logout
                $this->logout();
            }
        }
        */

        include(APPPATH.'config/common_js_css.php');
        $this->ci->includes = $common;
        $this->ci->js_urls = $js_urls;
        $this->ci->css_urls = $css_urls;
    }

    function index()
    {
        if(isset($_GET['_escaped_fragment_'])) {
            $this->ci->uri->uri_string = $_GET['_escaped_fragment_'];
        }
        //log_message('error', 'Hook Started ##'.($_SERVER['HTTPS']));
        // log in the user
        if(!logged_in()) {
            $this->ci->load->library('login');
            $login_errors = $this->ci->output->get_output();
            $this->ci->output->set_output('');
        }

        include APPPATH.'config/pages.php';

        // get the class name
        $class = $this->ci->router->fetch_class();

        $page = (isset($this->ci->page_info) ? $this->ci->page_info : $pages['default']);
        $this->ci->page_details = $page;
        $title = (is_null($page['title']) ? $page['full'] : $page['title']);
        $this->ci->page_details['big_title'] = (is_null($page['big_title']) ? $title : $page['big_title']);
        
        $this->ci->common_model->update_page_count();
        
        if($this->ci->uri->segment(1) == 'details' && $this->ci->uri->segment(2) == 'profile'){
            $this->ci->input->set_cookie('profile-prompt', 'shown', 604800*2);
            $show_profile_prompt = FALSE;
        }else{
            $cookie = $this->ci->input->cookie('profile-prompt');
            if($cookie == 'shown' || !logged_in()){
                $show_profile_prompt = FALSE;
            }else{
                $show_profile_prompt = !file_exists(VIEW_PATH.'details/img/users/'.$this->ci->session->userdata('uid').'_tiny.jpg');
            }
        }
        if($this->ci->input->cookie('cookiepopup') == FALSE){
            $show_profile_prompt = FALSE;
        }
        
        $persistant = $this->ci->input->cookie('servey-prompt');
        $temp = $this->ci->input->cookie('servey-temp');
        $complete = $this->ci->input->cookie('servey-complete');
        
        $show_survey = FALSE;
        if($complete != 'complete'){
            if($persistant == 'set'){
                if($temp != 'set'){
                    $show_survey = TRUE;
                }
            }else{
                $this->ci->input->set_cookie('servey-prompt', 'set', 60 * 60 * 24 * 365);
                $this->ci->input->set_cookie('servey-temp', 'set', 60 * 60 * 24 * 7 * 2);
            }
        }
        if(isset($_POST['surveycomplete'])){
            $this->ci->input->set_cookie('servey-temp', 'set', 60 * 60 * 24 * 7 * 2);
            $this->ci->input->set_cookie('servey-complete', 'complete', (60 * 60 * 24) * (($_POST['surveycomplete']=='done')?365:60));
        }

        // check if ajax request
        if($this->ci->input->is_ajax_request() || ($this->ci->input->post('request')==='cron')) {
            // if ajax
            $response_data['big_title'] = $this->ci->page_details['big_title'];
            $response_data['title'] = $title;
            $response_data['short'] = $class;
            if(is_array($page['keep_cache'])) {
                if(in_array(substr($this->ci->uri->uri_string(), 1), $page['keep_cache'])) $response_data['keep_cache'] = TRUE;
                else $response_data['keep_cache'] = FALSE;
            }
            else $response_data['keep_cache'] = $page['keep_cache'];
        }else{
            // if not ajax load head
            $this->ci->db->order_by('full ASC');
            $archive_sections = $this->ci->db->get('steering_pages')->result_array();
            $this->ci->load->view('common/head', array(
                'page' => $page,
                'js_links' => $this->get_js_includes($page['js']),
                'css_links' => $this->get_css_includes($page['css']),
                'class' => $class,
                'pages' => $pages,
                'sections' => $archive_sections,
                'show_prompt'=>$show_profile_prompt,
                'show_survey'=>$show_survey
            ));
        }

        // add any login errors to the output
        if(!empty($login_errors)) {
            $this->ci->output->append_output($login_errors);
            $GLOBALS['skip_controller'] = TRUE;
        }

        // check if controller requires login
        else if($page['requires_login']) {
            if(!$this->ci->session->userdata('logged_in')) {
                cshow_error('Please login to view this page', 401, 'Access denied');
                $GLOBALS['skip_controller'] = TRUE;
            }else if((!$this->ci->session->userdata('full_access') && $page['id'] == 2) || (!$page['allow_non-butler'] && $this->ci->session->userdata('college') !== 'butler')) {// user is logged in, but are they authorised to view the admin page?
                cshow_error('Sorry, Non Butler Students do not have permission to access this page.', 401, 'Access denied');
                $GLOBALS['skip_controller'] = TRUE;
            }
        }
        
        if(!isset($GLOBALS['skip_controller']) && $page['require-secure'] && !HTTPS && ENVIRONMENT != 'development'){
            cshow_error('You need to access this page using a secure connection, please click <a href="'.str_replace('http:', 'https:', site_url($this->ci->uri->uri_string())).'">here</a>.', 401, 'Access denied');
            $GLOBALS['skip_controller'] = TRUE;
        }
        
        if($this->ci->uri->segment(1) === 'confirm_email'){
            $success = $this->ci->users_model->confirm_email();
            $this->ci->load->view('utilities/success', array('success'=>$success));
            $GLOBALS['skip_controller'] = TRUE;
        }
        
        if(logged_in() && ENVIRONMENT !== 'development' && $this->ci->uri->segment(2) !== 'logout' && (!$this->ci->session->userdata('confirmed_email') || $this->ci->session->userdata('temporary_password'))){
            
            $this->ci->users_model->update_session();
            
            if($this->ci->input->post('account_resend_email')){
                $this->ci->users_model->send_email();
            }
    
            if($this->ci->input->post('account_update_email')){
                $this->ci->users_model->remove_email();
            }
            
            if($this->ci->session->userdata('temporary_password')){
                
                $this->ci->load->helper(array('form', 'url'));
                $this->ci->load->library('form_validation');
                
                $this->ci->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]|min_length[5]');
                $this->ci->form_validation->set_rules('email', 'Email', 'required|valid_email');
                $this->ci->form_validation->set_message('matches', 'Your passwords do not match');
                if(isset($_POST['email']) && (strpos($_POST['email'], '@durham.ac.uk') || strpos($_POST['email'], '@dur.ac.uk'))){
                    $this->ci->form_validation->set_rules('email', 'Email', 'max_length[5]');
                    $this->ci->form_validation->set_message('max_length', 'You must use a non @durham.ac.uk email address');
                } 
                
                if ($this->ci->form_validation->run() == FALSE){
                    $this->ci->load->view('utilities/password_change');
                }else{
                    $this->ci->users_model->change_password();
                    $this->ci->load->view('utilities/password_changed');
                }
                $GLOBALS['skip_controller'] = TRUE;
            }
            
            //ALTER TABLE `users` ADD `confirmed_email` TINYINT(1) NOT NULL DEFAULT '0' AFTER `custom_email`;
            if(!$this->ci->session->userdata('confirmed_email')){
                
                $this->ci->load->helper(array('form', 'url'));
                $this->ci->load->library('form_validation');
                
                if(isset($_POST['email'])){
                    $this->ci->form_validation->set_rules('email', 'Email', 'required|valid_email');
                    if(isset($_POST['email']) && (strpos($_POST['email'], '@durham.ac.uk') || strpos($_POST['email'], '@dur.ac.uk'))){
                        $this->ci->form_validation->set_rules('email', 'Email', 'max_length[5]');
                        $this->ci->form_validation->set_message('max_length', 'You must use a non @durham.ac.uk email address');
                    } 
                }
                
                if ($this->ci->form_validation->run() == FALSE){
                    $email = $this->ci->session->userdata('custom_email');
                    if(empty($email)){
                        $this->ci->load->view('utilities/email_change');
                    }else{
                        $this->ci->load->view('utilities/email_changed');
                    }
                }else{
                    $this->ci->users_model->update_email();
                    $this->ci->load->view('utilities/email_changed');
                }
                $GLOBALS['skip_controller'] = TRUE;
    
            }
        
        }

        if($this->ci->input->is_ajax_request()) {
            if(!isset($GLOBALS['skip_controller'])) {
                $response_data['css'] = $this->get_css_includes($page['css']);
                $response_data['js'] = $this->get_js_includes($page['js']);
            }
            $GLOBALS['js_response_data'] = $response_data;
        }

        $this->ci->benchmark->mark('pre_controller_end');
    }


    /**** HEAD ****/

    private function get_css_includes($other_css, $inc_common = TRUE)
    {
        // Define the common CSS includes.  No Extension.  No leading slash.
        $css = $this->ci->includes['css'];

        // if some other css is required add it to the list
        if(!empty($other_css)) {
            // Merge in the page-specific includes
            $css = array_merge($css, $other_css);
        }
        return $this->validate_css_files($css);
    }

    private function validate_css_files($css) {
        // create a blank css_links array
        //log_message('error', var_export($css, true));
        $css_links = array();
        // loop through all defined css includes and check they exist.  Some may have browser-specific versions.
        foreach($css as $k => $style_sheet) {
            // if the key is a string it means the script is an array of js files that should only be included if the user has the level in the key
            if(is_string($k) && !has_level($k)) continue;

            // value is array, so evaluate and merge
            if(is_array($style_sheet)) {
                $css_links = array_merge($css_links, $this->validate_css_files($style_sheet));
                continue;
            }

            // if already a full url
            if(strpos($style_sheet, '//') !== FALSE) {
                $css_links[] = $style_sheet;
                continue;
            }

            // get query string
            if(strpos($style_sheet, ':') !== FALSE) {
                $exp = explode(':', $style_sheet);
                $style_sheet = $exp[0];
                $query_string = $exp[1];
            }

            // keyword such as 'jquery'
            if(isset($this->ci->css_urls[$style_sheet])) {
                if(is_array($this->ci->css_urls[$style_sheet])) $css_links = array_merge($css_links, $this->ci->css_urls[$style_sheet]);
                else $css_links[] = $this->ci->css_urls[$style_sheet];
            }
            // standard include
            else {
                if(file_exists(VIEW_PATH.$style_sheet.'.min.css')){
                    $css_links[] = VIEW_URL.$style_sheet.'.min.css';
                }elseif(file_exists(VIEW_PATH.$style_sheet.'.css')){
                    $css_links[] = VIEW_URL.$style_sheet.'.css';
                }else{
                    //log_message('error', 'File Not Found: ('.VIEW_PATH.$style_sheet.'.css)');
                }
            }

            if(!empty($query_string)) {
                $last = array_pop($css_links);
                $css_links[] = $last.'?'.$query_string;
            }
        }
        //log_message('error', var_export($css_links, true));
        return $css_links;
    }

    private function get_js_includes($other_js)
    {
        if(!empty($other_js)) {
            // Merge in the page-specific includes
            $js = array_merge(array_merge($this->ci->includes['js']['first'], $other_js), $this->ci->includes['js']['last']);
        }
        // no additional js, just merge common
        else $js = array_merge($this->ci->includes['js']['first'], $this->ci->includes['js']['last']);
        // validate js and return it
        return $this->validate_js_files($js);
    }

    private function validate_js_files($js) {
        // create a blank js_links array
        $js_files = array();
        // loop through all defined js includes and check they exist.
        foreach($js as $k => $script) {
            // if the key is a string it means the script is an array of js files that should only be included if the user has the level in the key
            if(is_string($k) && !has_level($k)) continue;

            // value is array, so evaluate and merge
            if(is_array($script)) {
                $js_files = array_merge($js_files, $this->validate_js_files($script));
                continue;
            }

            // if already a full url
            if(strpos($script, '//') !== FALSE) {
                $js_files[] = $script;
                continue;
            }

            // get query string
            if(strpos($script, ':') !== FALSE) {
                $exp = explode(':', $script);
                $script = $exp[0];
                $query_string = $exp[1];
            }

            // keyword
            if(isset($this->ci->js_urls[$script])) {
                if(is_array($this->ci->js_urls[$script])) $js_files = array_merge($js_files, $this->ci->js_urls[$script]);
                else $js_files[] = $this->ci->js_urls[$script];
            }
            // standard include
            else {
                if(file_exists(VIEW_PATH.$script.'.js')){
                    $js_files[] = VIEW_URL.$script.'.js';
                }/*elseif(file_exists(VIEW_PATH.$script.'.js')){
                    $js_files[] = VIEW_URL.$script.'.js';
                }*/
            }

            if(!empty($query_string)) {
                $last = sizeof($js_files) - 1;
                $js_files[$last] .= '?'.$query_string;
            }
        }

        return $js_files;
    }

    private function logout() {
        $last_location = $this->ci->uri->uri_string();
        $this->ci->session->sess_destroy();
        $this->ci->input->set_cookie('username', '', '');
        $this->ci->input->set_cookie('rand', '', '');
        if($this->ci->input->is_ajax_request()) {
            //ajax
            echo json_encode(array('redirect' => BASE_URL.$last_location));
        }
        else header('Location: '.BASE_URL.$last_location);
        exit;
    }
}

/* End of file post_controller_constructor.php */
/* Location: ./application/hooks/post_controller_constructor.php */