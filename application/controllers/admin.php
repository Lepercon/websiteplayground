<?php

class Admin extends CI_Controller {

    var $edit_rights;

    function Admin() {
        parent::__construct();
        $this->load->model('admin_model');
        $this->page_info = array(
            'id' => 2,
            'title' => 'Administration Panel',
            'big_title' => '<span class="big-text-medium">Admin Panel</span>',
            'description' => 'Admin panel',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => TRUE,
            'css' => array('admin/admin', 'details/details', 'jcrop'),
            'js' => array('admin/admin', 'details/details', 'jcrop'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index($success = FALSE) {
        $this->load->view('admin/admin', array('success' => $success));
    }

    function help() {
        
        $this->load->helper('html');
    
        $help = $this->uri->rsegment(3);
        if($help === FALSE) {
            $this->load->view('admin/help');
        } else {
            if(in_array($help, array('photos', 'events', 'signup', 'editor', 'requests', 'admin', 'news', 'posters'))) {
                $this->check_edit_rights();
                $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => $help));
            } else {
                $this->load->view('admin/help');
            }
        }
    }
    
    /*** Deprecated functions ***/
    /* These functions are replaced by the single help function above */

    function help_photos() {
        $this->check_edit_rights();
        $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => 'photos'));
    }

    function help_events() {
        $this->check_edit_rights();
        $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => 'events'));
    }

    function help_signup() {
        $this->check_edit_rights();
        $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => 'signup'));
    }

    function help_editor() {
        $this->check_edit_rights();
        $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => 'editor'));
    }

    function help_requests() {
        $this->check_edit_rights();
        $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => 'requests'));
    }

    function help_admin() {
        $this->check_edit_rights();
        $this->load->view('admin/get_content', array('access_rights' => $this->edit_rights, 'page' => 'admin'));
    }
    
    /*** End Of Deprecated functions ***/

    function user_directory() {
        $this->load->view('admin/directory', array('users' => $this->users_model->get_users('all', 'uid, firstname, prefname, surname, email, current, year_group, status', NULL, 0, 'surname ASC, firstname ASC')));
    }

    function page_edit_rights() {
        $page = $this->uri->rsegment(3, FALSE);
        $success = FALSE;
        if($page != FALSE && !empty($_POST)) {
            $this->admin_model->save_page_edit_rights($page);
            $success = 'Edit rights changed';
        }
        $levels = $this->admin_model->get_level_names(FALSE, FALSE);
        $level_names = array();
        foreach($levels as $l) {
            $level_names[$l['id']] = $l['full'];
        }
        $edit_rights = $this->admin_model->get_page_edit_rights();
        include APPPATH.'config/pages.php';
        $page_rights = array();
        
        foreach($pages as $page) {
            if($page['editable'] == TRUE) {
                $page_rights[$page['id']] = array('allowed' => array(), 'notallowed' => array());
                foreach($levels as $l) {
                    $page_rights[$page['id']]['notallowed'][$l['id']] = $l['full'];
                }
                foreach($edit_rights as $e) {
                    if($e['page_id'] == $page['id']) {
                        $page_rights[$page['id']]['allowed'][$e['level_id']] = $level_names[$e['level_id']];
                        unset($page_rights[$page['id']]['notallowed'][$e['level_id']]);
                    }
                }
            }
        }
        $this->load->view('admin/page_edit_rights', array(
            'page_rights' => $page_rights,
            'pages' => $pages,
            'success' => $success
        ));
    }
    
    function delete_level() {
        $seg = $this->uri->rsegment(3);
        if($seg !== FALSE && is_numeric($seg)) {
            $this->admin_model->delete_level($seg);
        }
    }

    function levels() {
        $errors = FALSE;
        $other_errors = array();
        $success = FALSE;
        
        if(isset($_POST['full'])) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('full', 'Level Name', 'trim|required|max_length[50]|xss_clean');
            $this->form_validation->set_rules('full_access', 'Full Access', 'trim');
            $this->form_validation->set_rules('type', 'Type', 'trim|required');
            /**** END FORM VALIDATION ****/
            if($this->form_validation->run()) {
                if(!isset($_POST['full_access']) OR $_POST['full_access'] != 1) $_POST['full_access'] = 0;
                $this->db->set(array(
                        'full' => $_POST['full'],
                        'type' => $_POST['type'],
                        'full_access' => $_POST['full_access']
                ));
                if($this->db->insert('levels')) {
                    $success = $_POST['full'].' added';
                } else {
                    $other_errors = $_POST['full'].' level not added.  Please '.contact_wm().'.';
                    $errors = TRUE;
                }
            }
            else $errors = TRUE;
        } else if(isset($_POST['save'])) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Name', 'required');
            if($this->form_validation->run()) {
                $id = $this->input->post('id');
                $type = $this->input->post('type');
                $name = $this->input->post('name');
                $full_access = $this->input->post('full_access');
                $desc = textarea_to_db($this->input->post('desc'));
                $this->admin_model->update_level($id, $type, $name, $full_access, $desc);
            }else{ $errors = TRUE; }
        }
        
        $levels = $this->admin_model->get_level_names(TRUE);
        $all_users = $this->users_model->get_all_user_ids_and_names(TRUE);
        foreach($all_users as $u){
            $users[$u['id']] = $u['name'];
        }
        for($i=0;$i<sizeof($levels);$i++) {
            $levels[$i]['user'] = $this->users_model->get_users_with_level($levels[$i]['id'], 'users.id, CONCAT(users.firstname, \' \', users.surname) as name, level_list.id as lid, level_list.year, level_list.current', FALSE);
            
            /*foreach($all_users as $u) {
                if(in_array($u['id'], $level_list)){
                    $levels[$i]['user'][$u['id']] = $u['name'];
                }else{
                    $levels[$i]['notuser'][$u['id']] = $u['name'];
                }
            }*/
        }

        $this->load->view('admin/levels', array(
                'levels' => $levels,
                'errors' => $errors,
                'other_errors' => $other_errors,
                'success' => $success,
                'users' => $users
        ));
    }
    
    function level_add_user(){
        $level_id = $this->input->post('level_id');
        $u_id = $this->input->post('u_id');
        $year = $this->input->post('year');
        $current = $this->input->post('current');
        $this->admin_model->add_user_level($level_id, $u_id, $year, $current);
        if($current){
            $this->admin_model->email_user_new_level($level_id, $u_id);
        }
    }

    function level_remove_user(){
        $level_id = $this->input->post('l_id');
        $u_id = $this->input->post('u_id');
        $this->admin_model->remove_user_level($level_id, $u_id);
    }
    
    function level_change_status(){
        $level_id = $this->input->post('l_id');
        $u_id = $this->input->post('u_id');
        $new_status = $this->input->post('new_status');
        $this->admin_model->level_change_current($level_id, $u_id, $new_status);
    }

    function categories() {
        $errors = FALSE;

        // Delete a category
        if(isset($_POST['delete_category']) && validate_form_token('manage_categories')) {
            $this->db->where('category', $this->input->post('category_id'));
            $this->db->set('category', 'NULL', FALSE);
            $this->db->update('events');

            $this->db->where('category', $this->input->post('category_id'));
            $this->db->set('category', 'NULL', FALSE);
            $this->db->update('requests');

            $this->db->where('id', $this->input->post('category_id'));
            $this->db->delete('categories');
        }

        // Edit a category name or leader
        elseif(isset($_POST['edit_category']) && validate_form_token('manage_categories')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Category Name', 'required|max_length[50]');
            $this->form_validation->set_rules('leader', 'Category Leader', 'required');
            if($this->form_validation->run()) {
                $this->db->where('id', $this->input->post('category_id'));
                $this->db->set(array(
                    'name' => $this->input->post('name'),
                    'leader' => $this->input->post('leader'),
                    'short' => url_title($this->input->post('name'),'_',TRUE) //Creates URL safe version of category name
                ));
                $this->db->update('categories');
            }
            else $errors = TRUE;
        }

        elseif(isset($_POST['add_category']) && validate_form_token('manage_categories')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Category Name', 'required|max_length[50]|is_unique[categories.name]');
            $this->form_validation->set_rules('leader', 'Category Leader', 'required|greater_than[0]');
            if($this->form_validation->run()) {
                $this->db->set(array(
                    'name' => $this->input->post('name'),
                    'leader' => $this->input->post('leader'),
                    'short' => url_title($this->input->post('name'),'_',TRUE) //Creates URL safe version of category name
                ));
                $this->db->insert('categories');
            }
            else $errors = TRUE;
        }

        $levels = $this->admin_model->get_level_names(TRUE);
        $this->load->model('events_model');
        $categories = $this->events_model->get_categories();
        $this->load->view('admin/categories', array(
            'errors' => $errors,
            'categories' => $categories,
            'levels' => $levels
        ));
    }

    function sync() {
        $this->admin_model->sync_users();
        $this->index('Users synchronised with university database.');
    }

    function database() {
        $this->load->model('signup_model');
        $this->signup_model->delete_old_signups();
        $this->load->model('questionnaire_model');
        $this->questionnaire_model->delete_old_questionnaires();
        $this->index('Database and file system tidied.');
    }

    private function check_edit_rights() {
        $this->load->library('page_edit_auth');
        $this->edit_rights = $this->page_edit_auth->authenticate('admin');
    }
    
    function menu(){
        $this->check_edit_rights();
        
        if($this->edit_rights){
            $refresh = 0;
            if(isset($_POST['submit'])){
                $this->admin_model->update_menu();
                $refresh = 1;
            }
            
            $menu = $this->common_model->get_sorted_menu();
            $this->load->view('admin/menu', array(
                'menu' => $menu,
                'refresh' => $refresh
            ));
        }else{
            $this->index();
        }
        
    }
    
    function messages(){
        
        $this->check_edit_rights();
        $this->load->model(array('messages_model', 'events_model'));
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        if($this->edit_rights){
        
            $i = 1;
            $data = array();
            $passed = false;
            $error_rows = array();
            while(isset($_POST['event-'.$i])){
                $data[$i]['event_id'] = ($_POST['event-'.$i]=='NULL'?NULL:$_POST['event-'.$i]);
                $data[$i]['message'] = $_POST['message-'.$i];
                if(isset($_POST['have-start-'.$i])){
                    $date_string = str_replace('/', '-', $_POST['start-date-'.$i].' '.$_POST['start-hour-'.$i].':'.$_POST['start-minute-'.$i]);
                    $data[$i]['start'] = strtotime($date_string);
                }else{
                    $data[$i]['start'] = NULL;
                }
                if(isset($_POST['have-end-'.$i])){
                    $date_string = str_replace('/', '-', $_POST['end-date-'.$i].' '.$_POST['end-hour-'.$i].':'.$_POST['end-minute-'.$i]);
                    $data[$i]['expiry'] = strtotime($date_string);
                }else{
                    $data[$i]['expiry'] = NULL;
                }
                if(!is_null($data[$i]['start']) && !is_null($data[$i]['expiry']) && ($data[$i]['start'] > $data[$i]['expiry'])){
                    $error_rows[] = $i;
                    $_POST['date_error'] = '100';
                    $passed = false;
                }
                $i++;
            }
            
            $this->form_validation->set_message('less_than', 'One or more of your start dates is after its corresponding end date.');
            $this->form_validation->set_rules('date_error', 'Dates', 'less_than[5]');
            if(sizeof($data) > 0 && $this->form_validation->run()){
                $passed = true;
                $this->messages_model->update_messages($data);
            }
            
            if(isset($_POST['new-message-form'])){
                $id = $this->messages_model->new_message($_POST['new-message']);
            }else{
                $id = NULL;
            }
                    
            $events = $this->events_model->get_events_by_category();
            $event_list['NULL'] = 'None';
            foreach($events as $e){
                $event_list[$e['id']] = date('d/m/Y h:i', $e['time']).' - '.$e['name'];
            }
            
            $this->load->view('admin/messages', array(
                'messages' => $this->messages_model->get_all_messages(),
                'events' => $event_list,
                'run' => $passed,
                'data' => $data,
                'new_id' => $id
            ));
            
        }else{
            $this->index();
        }
        
    }
    
    function change_passwords(){
        $this->check_edit_rights();
        
        if($this->edit_rights){
        
            if(isset($_POST['submit']) && !empty($_POST['user-id'])){
                $password = $this->admin_model->reset_password();
                
                if($_POST['reset-type'] == 'email'){
                
                    $nl = '<br>';
                    $mail = 'Dear User,'.$nl.$nl;
                    $mail .= 'The password for the user <b>'.($password['prefname']==''?$password['firstname']:$password['prefname']).' '.$password['surname'];
                    $mail .= ' ('.$password['username'].')</b> has been reset.'.$nl.$nl;
                    $mail .= 'The new password is: <b>'.$password['password'].'</b>'.$nl.$nl;
                    $mail .= 'This password can be used to login to the <b><a href="'.site_url().'">Butler JCR Website</a></b>.'.$nl.$nl;
                    $mail .= 'Butler JCR.';
                    $this->load->library('email');
            
                    $config['wordwrap'] = FALSE;
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    
                    $this->email->to($_POST['email']);
                    $this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
                    $this->email->message($mail);
                    $this->email->subject('Password Reset');
                    
                    if(ENVIRONMENT != 'development'){
                        $this->email->send();
                    }else{
                        log_message('error', 'Email: '.$this->email->print_debugger());
                    }

                }
                
                $this->load->view('admin/display_password', array(
                    'user' => $password
                ));

            }
                        
            $users = $this->admin_model->get_ex_students();
        
            $this->load->view('admin/change_passwords', array(
                'users' => $users
            ));
        }
    }
    
    function user_photos(){
        
        if(isset($_POST['form_token'])){
            $user = $this->users_model->get_users($_POST['u_id']);
            
            $img['large'] = new Imagick(VIEW_PATH.'details/img/tmp/'.$user['uid'].'.jpg');
            $img['medium'] = $img['large']->clone();
            
            $img['large']->cropImage($_POST['w-large'], $_POST['h-large'], $_POST['x-large'], $_POST['y-large']);
            $img['medium']->cropImage($_POST['w-small'], $_POST['h-small'], $_POST['x-small'], $_POST['y-small']);
            $img['large']->resizeImage(200,0,Imagick::FILTER_LANCZOS,1);
            $img['medium']->resizeImage(200,200,Imagick::FILTER_LANCZOS,1);
            
            $img['small'] = $img['medium']->clone();
            $img['tiny'] = $img['medium']->clone();
            $img['small']->resizeImage(100,100,Imagick::FILTER_LANCZOS,1);
            $img['tiny']->resizeImage(50,50,Imagick::FILTER_LANCZOS,1);
            
            foreach($img as $t=>$i){
                $i->writeImage(VIEW_PATH.'details/img/users/'.$user['uid'].'_'.$t.'.jpg'); // save the image
            }
            $this->load->view('admin/user_photos_image', array(
                'user'=>$user
            ));
            $this->load->view('admin/user_photos', array(
                'users'=>$this->users_model->get_all_user_ids_and_names(TRUE),
                'noback'=>1
            ));
            return;
        }
        
        if(isset($_POST['upload-img'])){
            $config = array(
                'upload_path'    => VIEW_PATH.'details/img/tmp/',
                'allowed_types' => 'jpg|jpeg|png',
                'overwrite'        => TRUE, // if file of same name already exists, overwrite it
                'max_size'        => '8191', // 8MB
                'max_width'     => '0', // No limit on width
                'max_height'    => '0', // No limit on height
                'encrypt_name'  => TRUE
            );
            $this->load->library('upload', $config);
            if($this->upload->do_upload('file-upload')) {
                $image_data = $this->upload->data();
                $img = new Imagick($image_data['full_path']);
                $geo = $img->getImageGeometry();
                //$this->autoRotateImage($img, $image_data['full_path']);
                if($geo['width'] > 800){
                    $img->resizeImage(800, 0, Imagick::FILTER_LANCZOS, 1);
                }
                $geo = $img->getImageGeometry();
                if(strtolower($img->getImageFormat()) != 'jpeg') {
                    // change format to jpg if required
                    $img->setCompressionQuality(90);
                    $img->setImageFormat('jpeg');
                }
                $user = $this->users_model->get_users($_POST['user-id']);
                // save resized file
                $img->writeImage(VIEW_PATH.'details/img/tmp/'.$user['uid'].'.jpg');
                // delete original upload
                unlink($image_data['full_path']);
                
                $this->load->view('details/crop', array(
                    'dims' => $geo,
                    'uid' => $user['uid'],
                    'url' => 'admin/user_photos',
                    'u_id'=> $user['id']
                ));
                return;
            }else{
                $_POST['choose-name'] = 1;
                $_POST['user-id'] = $_POST['user-id'];
            }
        }
        
        if(isset($_POST['choose-name']) && !empty($_POST['user-id'])){
            $this->load->view('admin/user_photos_image', array(
                'user'=>$this->users_model->get_users($_POST['user-id'])
            ));
            return;
        }
        
        $this->load->view('admin/user_photos', array(
            'users'=>$this->users_model->get_all_user_ids_and_names(TRUE)
        ));
        
        
    }
    
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */