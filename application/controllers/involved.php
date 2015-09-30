<?php

class Involved extends CI_Controller {

    var $inv_pages;

    function Involved()
    {
        parent::__construct();
        $this->inv_pages = array('committees','societies','sports','gym','information');
    }

    function index()
    {
        $page = $this->get_page();
        if($page == 'gym') {
            $this->gym();
            return;
        }
        if($page == 'information') {
            $this->information();
            return;
        }
        
        /* Poster Uploading */
        $config['upload_path'] = VIEW_PATH.'involved/img/posters/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['file_name'] = $this->uri->rsegment(4);
        $config['overwrite'] = TRUE;
        $this->load->library('upload', $config);
        if($this->upload->do_upload()){
            log_message('error', 'worked');
        }else{
            log_message('error', 'didn\'t work');
        }
        log_message('error', var_export($_FILES, true));
        
        /* Page construction */        
        $sections = $this->get_sections($page);
        $section = $this->uri->rsegment(4);
        $teams = array();
        if($section === FALSE OR !$this->check_section($section, $sections)) $section = null;
        if(!empty($section)) {
            $section_details = $this->get_section_by_name($section, $page);
            $teams = $this->get_teams($section_details['id']);
        }
        else $section_details = NULL;
        $this->load->library('page_edit_auth');
        $a_r = $this->page_edit_auth->authenticate('involved') || has_level($section_details['associateexec']);
        log_message('error', $a_r);
        if(isset($_POST['ajax'])) $this->load->view('involved/get_content', array(
            'page' => $page,
            'section' => $section,
            'details' => $section_details,
            'teams' => $teams,
            'access_rights' => $a_r
        ));
        else $this->load->view('involved/involved', array(
            'page' => $page,
            'sections' => $sections,
            'section' => $section,
            'access_rights' => $a_r,
            'details' => $section_details,
            'teams' => $teams
        ));
    }

    function gym() {
        if(logged_in()) {
            $this->load->view('involved/gym', array('buddies' => $this->get_buddy('all')));
        } else {
            $this->load->view('involved/gym');
        }
    }
    
    function poster(){
        
        $this->load->library('page_edit_auth');
        
        $page = $this->get_page();
        $sections = $this->get_sections($page);
        $section = $this->uri->rsegment(4);
        $details = $this->get_section_by_name($section, $page);
        $a_r = $this->page_edit_auth->authenticate('involved') || has_level($details['associateexec']);
        
        $this->load->view('involved/poster', array(
            'access_rights' => $a_r,
            'details' => $details,
            'page' => $page
        ));
    }

    function information() {
        $this->load->library('page_edit_auth');
        $this->load->view('involved/info_content', array(
            'access_rights' => $this->page_edit_auth->authenticate('involved'),
            'page' => 'information'
        ));
    }

    function subscribe() {
        if(logged_in()) $this->mailing_list(TRUE);
        $this->index();
    }

    function unsubscribe() {
        if(logged_in()) $this->mailing_list(FALSE);
        $this->index();
    }

    function remove() {
        $id = $this->uri->rsegment(3);
        if($id === FALSE OR ($id != $this->session->userdata('id') && !is_admin())) {
            $this->gym();
            return;
        }
        $this->db->where('user_id', $id);
        $this->db->update('users', array('availability', ''));
        $this->gym();
    }

    private function get_teams($page_id) {
        $this->db->where('involved_id', $page_id);
        $this->db->order_by('team_name');
        return $this->db->get('teamdurham')->result_array();
    }

    private function get_page() {
        $page = $this->uri->rsegment(3, $this->inv_pages[0]);
        if(!in_array($page, $this->inv_pages)) $page = $this->inv_pages[0];
        return $page;
    }

    private function get_sections($page) {
        $this->db->where('page', $page);
        $this->db->order_by('full ASC');
        return $this->db->get('involved_pages')->result_array();
    }

    private function get_section($id) {
        $this->db->where('id', $id);
        return $this->db->get('involved_pages')->row_array(0);
    }

    private function get_section_by_name($name, $page) {
        $this->db->where(array('short' => $name, 'page' => $page));
        return $this->db->get('involved_pages')->row_array(0);
    }

    private function check_section($section, $sections) {
        foreach($sections as $s) if($s['short'] == $section) return TRUE;
        return FALSE;
    }

    function delete_team() {
        if(!is_admin()) {
            $this->index();
            return;
        }
        $team = $this->uri->rsegment(3);
        if($team == FALSE) {
            $this->index();
            return;
        } else {
            $this->db->where('id', $team);
            $teamdata = $this->db->get('teamdurham')->row_array(0);
            if(!empty($teamdata)) {
                $this->db->where('id', $team);
                $this->db->delete('teamdurham');
            }
        }
        $this->load->model('admin_model');
        $section = $this->get_section($teamdata['involved_id']);
        $this->load->view('involved/edit', array(
            'page' => $this->get_page(),
            'levels' => $this->admin_model->get_level_names(),
            'section' => $section,
            'teams' => $this->get_teams($section['id']),
            'error' => FALSE
        ));
    }

    function manage() {
        if(!is_admin()) {
            $this->index();
            return;
        }
        $this->load->model('admin_model');
        $levels = $this->admin_model->get_level_names();
        $page = $this->get_page();
        $error = FALSE;
        $success = FALSE;
        if(validate_form_token('involved_manage_sections')) {
            if(isset($_POST['new'])) {
                $short = url_title($_POST['new-section'], 'underscore', TRUE); // replace space with underscore, force lowercase
                if($short === '') $error = 'No name provided, or only used invalid characters';
                else if(strlen($_POST['new-section']) > 50) $error = 'Name is too long';
                else {
                    foreach(array('mailing','associateexec','cost','schedule') as $a) if(!isset($_POST[$a])) $_POST[$a] = '';
                    if($this->add_section($short, $_POST['new-section'], $page, $_POST['mailing'], $_POST['associateexec'], $_POST['cost'], $_POST['schedule'])) $success = 'Section added';
                    else $error = 'Section exists';
                }
            }
            else if(isset($_POST['save'])) {
                $short = url_title($_POST['new-section'], 'underscore', TRUE); // replace space with underscore, force lowercase
                if($short === '') $error = 'No name provided, or only used invalid characters';
                else if(strlen($_POST['new-section']) > 50) $error = 'Name is too long';
                else {
                    if(!isset($_POST['mailing'])) $_POST['mailing'] = '';
                    if(!isset($_POST['cost'])) $_POST['cost'] = '';
                    if(!isset($_POST['schedule'])) $_POST['schedule'] = '';
                    if($this->update_section($_POST['save'], $short, $_POST['new-section'], $page, $_POST['mailing'], $_POST['associateexec'], $_POST['cost'], $_POST['schedule'])) $success = 'Section updated';
                    else {
                        $error = 'Section exists';
                        $section = $this->get_section($_POST['save']);
                        $this->load->view('involved/edit', array(
                            'page' => $page,
                            'levels' => $levels,
                            'section' => $section,
                            'teams' => $this->get_teams($section['id']),
                            'error' => $error
                        ));
                        return;
                    }
                }
            }
            else if(isset($_POST['teams'])) {
                foreach(array('team_name', 'team_id', 'comp_id') as $v) {
                    if(empty($_POST[$v])) {
                        $error = 'All fields are required to add a new team.';
                    }
                }
                if($error == FALSE) $this->add_team($_POST['teams'], $_POST['team_name'], $_POST['team_id'], $_POST['comp_id']);
                $section = $this->get_section($_POST['teams']);
                $this->load->view('involved/edit', array(
                    'page' => $page,
                    'levels' => $levels,
                    'section' => $section,
                    'teams' => $this->get_teams($section['id']),
                    'error' => $error
                ));
                return;
            }
            else if(isset($_POST['edit']) && isset($_POST['section'])) {
                $section = $this->get_section($_POST['section']);
                $this->load->view('involved/edit', array(
                    'page' => $page,
                    'levels' => $levels,
                    'section' => $section,
                    'teams' => $this->get_teams($section['id'])
                ));
                return;
            }
            else if(isset($_POST['delete'])) {
                $this->delete_section($_POST['section'], $page);
                $success = 'Section deleted';
            }
        }
        $this->load->view('involved/manage', array(
            'page' => $page,
            'levels' => $levels,
            'sections' => $this->get_sections($page),
            'error' => $error,
            'success' => $success
        ));
    }

    private function add_section($short, $full, $page, $mailing, $exec, $cost, $schedule) {
        $this->db->where(array('short' => $short, 'page' => $page));
        if($this->db->get('involved_pages')->num_rows() > 0) return FALSE;
        $this->db->set(array('short' => $short, 'full' => $full, 'page' => $page));
        if(!empty($mailing)) $this->db->set('mailing', $mailing);
        if(!empty($exec)) $this->db->set('associateexec',$exec);
        if(!empty($cost)) $this->db->set('cost',$cost);
        if(!empty($schedule)) $this->db->set('schedule',$schedule);
        $this->db->insert('involved_pages');
        file_put_contents(VIEW_PATH.'involved/content/'.$page.'/'.$short.'.php', '');
        return TRUE;
    }

    private function update_section($existing_id, $short, $full, $page, $mailing, $exec, $cost, $schedule) {
        $this->db->where(array('short' => $short, 'page' => $page, 'id !=' => $existing_id));
        if($this->db->get('involved_pages')->num_rows() > 0) return FALSE;
        $existing = $this->get_section($existing_id);
        $this->db->set(array('short' => $short, 'full' => $full, 'page' => $page));
        if(!empty($mailing)) $this->db->set('mailing', $mailing);
        if(!empty($exec)) $this->db->set('associateexec',$exec);
        if(!empty($cost)) $this->db->set('cost',$cost);
        if(!empty($schedule)) $this->db->set('schedule',$schedule);
        $this->db->where('id', $existing_id);
        $this->db->update('involved_pages');
        rename(VIEW_PATH.'involved/content/'.$page.'/'.$existing['short'].'.php', VIEW_PATH.'involved/content/'.$page.'/'.$short.'.php');
        return TRUE;
    }

    private function delete_section($id, $page) {
        $this->db->where('id', $id);
        $section = $this->db->get('involved_pages')->row_array(0);
        $file = VIEW_PATH.'involved/content/'.$page.'/'.$section['short'].'.php';
        if(file_exists($file)) unlink($file);
        $this->db->where('id', $id);
        $this->db->delete('involved_pages');
    }

    private function add_team($page, $teamname, $teamid, $compid) {
        $this->db->set('team_name', $teamname);
        $this->db->set('team_id', $teamid);
        $this->db->set('comp_id', $compid);
        $this->db->set('involved_id', $page);
        $this->db->insert('teamdurham');
    }

    private function mailing_list($subscribe) {
        $details = $this->get_section_by_name($this->uri->rsegment(4), $this->get_page());
        $this->load->library('email');
        $this->email->to('majordomo@durham.ac.uk');
        $this->email->from($this->session->userdata('email'));
        $this->email->subject('');
        $this->email->message(($subscribe ? '' : 'un').'subscribe '.$details['mailing']);
        $this->email->send();
    }

    private function get_buddy($id = 'all') {
        $this->db->select('id, firstname, prefname, surname, uid, email, mobile, availability');
        $this->db->where('availability >', '');
        $this->db->where('current', 1);
        if($id !== 'all') {
            $this->db->where('user_id', $id);
            return $this->db->get('users')->row_array(0);
        } else {
            return $this->db->get('users')->result_array();
        }
    }
}

/* End of file involved.php */
/* Location: ./application/controllers/involved.php */