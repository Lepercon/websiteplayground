<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class projects_model extends CI_Model {
    
    function projects_model() {
        parent::__construct();
    }
    
    function add_request() {
        foreach(array('title', 'category', 'description', 'event') as $field) $submit[$field] = $this->input->post($field);
        $submit['request_by'] = $this->session->userdata('id');
        $submit['request_time'] = time();
        $this->db->set($submit);
        $this->db->insert('requests');
        $insert_id = $this->db->insert_id();
        $this->email_category($submit['category'], $insert_id, 'add_request', $submit['description'], $submit['title']);
        return $insert_id;
    }

    function add_comment($request_id) {
        $submit['comment'] = $this->input->post('comment');
        $submit['request_id'] = $request_id;
        $submit['submitted_by'] = $this->session->userdata('id');
        $submit['submitted_time'] = time();
        $this->db->set($submit);
        $this->db->insert('project_comments');
        $request = $this->get_request($request_id);
        $this->email_category($request['category'], $request_id, 'add_comment', $submit['comment']);
    }

    function add_file($request_id, $filename) {
        $submit['filename'] = $filename;
        $submit['description'] = $this->input->post('description');
        $submit['request_id'] = $request_id;
        $submit['submitted_by'] = $this->session->userdata('id');
        $submit['submitted_time'] = time();
        $this->db->set($submit);
        $this->db->insert('project_files');
        $request = $this->get_request($request_id);
        $this->email_category($request['category'], $request_id, 'add_file', $submit['description'], 'New file at: {unwrap}'.VIEW_URL.'projects/files/'.$request_id.'/'.$filename.'{/unwrap}');
    }

    function get_requests($incomplete = FALSE, $category = 'all', $event_id = 'all') {
        $this->db->order_by('request_time DESC');
        if($incomplete) $this->db->where('progress !=', 'completed');
        if($category != 'all') $this->db->where('category', $category);
        if($event_id != 'all') $this->db->where('event', $event_id);
        return $this->db->get('requests')->result_array();
    }

    function get_request($request_id) {
        $this->db->where('id', $request_id);
        $this->db->order_by('request_time DESC');
        $request = $this->db->get('requests')->row_array(0);
        if(empty($request)) return FALSE;
        $this->db->where('id', $request['category']);
        $request['category_name'] = return_array_value('name', $this->db->get('categories')->row_array(0));
        return $request;
    }

    function get_files($request_id) {
        $this->db->where('request_id', $request_id);
        $this->db->order_by('submitted_time DESC');
        return $this->db->get('project_files')->result_array();
    }

    function get_comments($request_id) {
        $this->db->where('request_id', $request_id);
        $this->db->order_by('submitted_time DESC');
        return $this->db->get('project_comments')->result_array();
    }

    function get_file($file_id) {
        $this->db->where('id', $file_id);
        return $this->db->get('project_files')->row_array(0);
    }

    function get_comment($comment_id) {
        $this->db->where('id', $comment_id);
        return $this->db->get('project_comments')->row_array(0);
    }

    function delete_comment($comment_id) {
        $this->db->where('id', $comment_id);
        $this->db->delete('project_comments');
    }

    function email_category($category_id, $request_id, $notification, $description = '', $title = '') {
        // find level id used for role representing the category, e.g: get webmaster role for website category
        $this->db->select('id, name, leader');
        $this->db->where('id', $category_id);
        $category = $this->db->get('categories')->row_array(0);
        
        $emails = array();
        // get email addresses of all users in that role
        $users = $this->users_model->get_users_with_level($category['leader'], 'email');
        if(!empty($users)) {
            foreach($users as $u) $emails[] = $u['email'];
        }
        
        // find email addresses of all users who are associated with the selected project by file or comment
        if($notification != 'add_request') {
            $this->db->select('email');
            $this->db->from('users');
            $this->db->join('project_comments', 'project_comments.submitted_by = users.id', 'inner');
            $this->db->where('project_comments.request_id', $request_id);
            $users = $this->db->get()->result_array();
            if(!empty($users)) {
                foreach($users as $u) $emails[] = $u['email'];
            }
            
            $this->db->select('email');
            $this->db->from('users');
            $this->db->join('project_files', 'project_files.submitted_by = users.id', 'inner');
            $this->db->where('project_files.request_id', $request_id);
            $users = $this->db->get()->result_array();
            if(!empty($users)) {
                foreach($users as $u) $emails[] = $u['email'];
            }
        }

        // remove duplicate email addresses
        $emails = array_unique($emails);

        if(!empty($emails)) {
            // determine subject and message content
            switch($notification) {
                case 'add_request':
                    $subject = 'Request added to';
                break;
                case 'add_comment':
                    $subject = 'Comment added to';
                break;
                case 'add_file':
                    $subject = 'File added to';
                break;
                case 'reallocate':
                    $subject = 'Request reallocated to';
                break;
                default:
                    $subject = '';
            }

            // get user's preferred name
            $username = user_pref_name($this->session->userdata('firstname'), $this->session->userdata('prefname'), $this->session->userdata('surname'));
        
            // generate and send email
            
            if(count($emails) > 20) {
                log_message('error', 'More than 20 recipients for email');
            }
            else {
                $this->load->library('email');
                $this->email->to($emails);
                $this->email->from($this->session->userdata('email'), $username);
                $this->email->subject($subject.' '.strtolower($category['name']));
                $this->email->message($subject.' '.strtolower($category['name']).' by '.$username.':'."\r\n".$title."\r\n".$description."\r\n\r\n".' Visit {unwrap}'.site_url('projects/view_request/'.$request_id).'{/unwrap} to view this request in context.');
                $this->email->send();
            }
        }
    }
}