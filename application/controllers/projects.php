<?php

class Projects extends CI_Controller {

    function Projects() {
        parent::__construct();
        foreach(array('projects_model', 'events_model', 'users_model') as $m) $this->load->model($m);
        $this->load->library('form_validation');
        $this->page_info = array(
            'id' => 22,
            'title' => 'Projects',
            'big_title' => '<span class="big-text-medium">Projects</span>',
            'description' => 'A hub for requests and documentation within the JCR',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array(),
            'keep_cache' => FALSE,
            'editable' => FALSE
        );
    }
    
    function index($incomplete = FALSE, $category = 'all') {
        // show list of categories with number of requests in each
        $requests = $this->projects_model->get_requests($incomplete, $category);
        $categories = $this->events_model->get_categories();
        $category_names = array();
        foreach($categories as $c) {
            $category_names[$c['id']] = $c['name'];
        }
        $category_list = array();
        $all_requests = $this->projects_model->get_requests();
        foreach($all_requests as $r) {
            $category_list[] = $r['category'];
        }
        $category_count = array_count_values($category_list);
        $this->load->view('projects/projects', array(
            'requests' => $requests,
            'category_list' => $category_count,
            'category_names' => $category_names
        ));
    }

    function show_incomplete() {
        $category = $this->uri->rsegment(3);
        if($category != FALSE) {
            $this->index(TRUE, $category);
        }
        else $this->index(TRUE);
    }

    function show_category() {
        $category = $this->uri->rsegment(3);
        if($category != FALSE) {
            $this->index(FALSE, $category);
        }
        else $this->index();
    }

    function add_request() {
        $errors = FALSE;
        if($this->input->post('add_request') != FALSE && validate_form_token('projects')) {
            $this->form_validation->set_rules('title','Title','trim|required|max_length[50]|xss_clean');
            $this->form_validation->set_rules('category','Category','trim|required|integer|max_length[3]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
            $this->form_validation->set_rules('event', 'Event ID', 'trim|integer');
            if($this->form_validation->run()) {
                $this->view_request($this->projects_model->add_request());
                return;
            }
            else $errors = TRUE;
        }
        $c_id = $this->uri->rsegment(3);
        if ($c_id == FALSE) $c_id = '';
        else $c_id = $this->events_model->get_category_id($c_id);
        $event = $this->uri->rsegment(4);
        if($event == FALSE) $event = $this->input->post('event');
        if($event == FALSE) $event = '';
        $this->load->view('projects/add_request', array('errors' => $errors, 'c_id' => $c_id, 'event' => $event, 'categories' => $this->events_model->get_categories()));
    }

    function view_request($request_id = FALSE, $errors = FALSE, $other_errors = array()) {
        if($request_id === FALSE) {
            $request_id = $this->uri->rsegment(3);
            if($request_id == FALSE) {
                $this->index();
                return;
            }
        }
        if(($this->input->post('progress') != FALSE) && validate_form_token('projects')) {
            $this->form_validation->set_rules('progress', 'Progress', 'trim|required|xss_clean');
            if($this->form_validation->run()) {
                $this->db->where('id', $request_id);
                $this->db->set('progress', $this->input->post('progress'));
                $this->db->update('requests');
            }
            else {
                $errors = TRUE;
            }
        }
        elseif(($this->input->post('change_category') != FALSE) && validate_form_token('projects')) {
            $this->form_validation->set_rules('category', 'Category', 'trim|required|xss_clean');
            if($this->form_validation->run()) {
                if($this->input->post('category') != $this->input->post('old_category')) {
                    $this->db->where('id', $request_id);
                    $this->db->set('category', $this->input->post('category'));
                    $this->db->update('requests');
                    $this->projects_model->email_category($this->input->post('category'), $request_id, 'reallocate', 'This project has been reallocated by '.user_pref_name($this->session->userdata('firstname'), $this->session->userdata('prefname'), $this->session->userdata('surname')));
                }
                else {
                    $other_errors = 'The project already belongs to that category';
                    $errors = TRUE;
                }
            }
            else $errors = TRUE;
        }
        $request = $this->projects_model->get_request($request_id);
        if($request === FALSE) $this->index();
        if($request['event'] != FALSE) $event = $this->events_model->get_event($request['event']);
        else $event = FALSE;
        $this->load->view('projects/request', array(
            'errors' => $errors,
            'other_errors' => $other_errors,
            'request' => $request,
            'files' => $this->projects_model->get_files($request_id),
            'comments' => $this->projects_model->get_comments($request_id),
            'categories' => $this->events_model->get_categories(),
            'event' => $event
        ));
    }

    function delete_comment() {
        $comment_id = $this->uri->rsegment(3);
        if($comment_id == FALSE) {
            $this->index();
            return;
        }
        $comment = $this->projects_model->get_comment($comment_id);
        if(is_admin() or $comment['submitted_by'] == $this->session->userdata('id')) {
            $this->projects_model->delete_comment($comment_id);
        }
        $this->view_request($comment['request_id']);
    }

    function delete_file() {
        $file_id = $this->uri->rsegment(3);
        if($file_id == FALSE) {
            $this->index();
            return;
        }
        $file = $this->projects_model->get_file($file_id);
        if(!empty($file) && $file['request_id'] != '' && $file['filename'] != '' &&(is_admin() or $file['submitted_by'] == $this->session->userdata('id'))) {
            if(file_exists(VIEW_PATH.'projects/files/project_'.$file['request_id'].'/'.$file['filename'])) {
                unlink(VIEW_PATH.'projects/files/project_'.$file['request_id'].'/'.$file['filename']);
            }
            $this->db->where('id', $file_id);
            $this->db->delete('project_files');
            $this->view_request($file['request_id']);
        }
        else $this->index();
    }

    function add_comment($request_id = FALSE) {
        if($request_id == FALSE) {
            $request_id = $this->uri->rsegment(3);
            if($request_id != FALSE) {
                $request = $this->projects_model->get_request($request_id);
                if($request == FALSE) {
                    $this->index();
                    return;
                }
            }
            else {
                $this->index();
                return;
            }
        }
        $errors = FALSE;
        if($this->input->post('add_comment') != FALSE && validate_form_token('projects')) {
            $this->form_validation->set_rules('comment', 'Comment', 'trim|required|xss_clean');
            if($this->form_validation->run()) {
                $this->projects_model->add_comment($request_id);
            }
        }
        $this->view_request($request_id);
    }

    function add_file($request_id = FALSE) {
        if($request_id == FALSE) {
            $request_id = $this->uri->rsegment(3);
            if($request_id != FALSE) {
                $request = $this->projects_model->get_request($request_id);
                if($request == FALSE) {
                    $this->index();
                    return;
                }
            }
            else {
                $this->index();
                return;
            }
        }
        $errors = FALSE;
        $other_errors = array();
        if($this->input->post('add_file') != FALSE && validate_form_token('projects')) {
            $this->form_validation->set_rules('description', 'File Description', 'trim|required|xss_clean');
            if($this->form_validation->run()) {
                $config = array(
                    'upload_path'    => VIEW_PATH.'projects/files/project_'.$request_id.'/',
                    'allowed_types'    => '*',
                    'overwrite'        => FALSE, // if file of same name already exists, overwrite it
                    'max_size'        => '8192', // 8MB
                    'max_width'        => '0', // No limit on width
                    'max_height'    => '0', // No limit on height
                    'remove_spaces'    => TRUE
                );
                if(!file_exists(VIEW_PATH.'projects/files/project_'.$request_id)) mkdir(VIEW_PATH.'projects/files/project_'.$request_id);
                $this->load->library('upload', $config);
                if($this->upload->do_upload()) {
                    $upload_data = $this->upload->data();
                    $this->projects_model->add_file($request_id, $upload_data['file_name']);
                }
                else {
                    $errors = TRUE;
                    $other_errors = array($this->upload->display_errors());
                }
            }
            else $errors = TRUE;
        }
        $this->view_request($request_id, $errors, $other_errors);
    }
}

/* End of file projects.php */
/* Location: .application/controllers/projects.php */