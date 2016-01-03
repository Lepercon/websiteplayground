<?php

class Archive extends CI_Controller {
    
    function Archive()
    {
        parent::__construct();
        $this->page_info = array(
            'id' => 14,
            'title' => 'Archive',
            'big_title' => NULL,
            'description' => 'Committee Documents and JCR Awards',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => TRUE,
            'css' => array('archive/archive'),
            'js' => array('archive/archive'),
            'keep_cache' => FALSE,
            'editable' => FALSE
        );
    }
    
    function index()
    {
        $this->load->model('archive_model');
        $this->load->library('page_edit_auth');
        
        $sections = $this->archive_model->get_sections();
        
        $section = $this->uri->rsegment(3);
        if($section === FALSE) $section = null;
        $section_id = $this->archive_model->get_section_id($section, $sections);
        $years = $this->archive_model->get_years($section_id);
        $docs = $this->archive_model->get_docs($years, $section_id);
        if(isset($_POST['ajax'])){
            $this->load->view('archive/doc_view', array(
                'section' => $section, 
                'years' => $years, 
                'docs' => $docs,
                'access_rights' => $this->page_edit_auth->authenticate('archive')
            ));
        }else{
            $this->load->view('archive/archive', array(
                'sections' => $sections, 
                'section' => $section, 
                'years' => $years, 
                'docs' => $docs,
                'access_rights' => $this->page_edit_auth->authenticate('archive')
            ));
        }
    }
    
    function manage()
    {
        if(!is_admin()) {
            $this->index();
            return;
        }
        $errors = FALSE;
        $other_errors = array();
        $success = FALSE;
        $this->load->model('archive_model');
        if(validate_form_token('archive_manage_sections')) {
            if(isset($_POST['new-section'])) {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('new-section','New Section Name','trim|required|max_length[50]|xss_clean');
                if($this->form_validation->run()) {
                    if($this->archive_model->add_section($_POST['new-section'])) $success = $_POST['new-section'].' Section Added';
                    else {
                        $other_errors[] = 'Section '.$_POST['new-section'].' Already Exists';
                        $errors = TRUE;
                    }
                }
                else $errors = TRUE;
            }
            else if(isset($_POST['delete'])) {
                $this->archive_model->delete_section($_POST['delete']);
                $success = 'Section deleted';
            }
        }
        $sections = $this->archive_model->get_sections();
        $this->load->view('archive/manage', array('sections' => $sections, 'errors' => $errors, 'other_errors' => $other_errors, 'success' => $success));
    }
    
    function finance(){
        
    }
    
    function add_new_doc() {
        $section = $this->uri->rsegment(3);
        if($section !== FALSE) {
            $this->db->where('short', $section);
            $tmp = $this->db->get('steering_pages')->row_array(0);
            if(empty($tmp)) $section_id = FALSE;
            else {
                $section_id = $tmp['id'];
                $full_section = $tmp['full'];
            }
        }
        if(!is_admin() OR $section === FALSE OR $section_id === FALSE) {
            $this->index();
            return;
        }
        $errors = FALSE;
        $other_errors = array();
        if(validate_form_token('archive_upload')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name','Name','trim|required|max_length[50]|xss_clean');
            foreach(array('day','month') as $v) $this->form_validation->set_rules($v,ucfirst($v),'trim|integer|max_length[2]');
            $this->form_validation->set_rules('year','Year','trim|required|integer|max_length[4]');
            if($this->form_validation->run()) {
                $config = array(
                    'upload_path'    => VIEW_PATH.'archive/doc/'.$section.'/',
                    'allowed_types' => '*',
                    'overwrite'        => FALSE, // if file of same name already exists, overwrite it
                    'remove_spaces'    => TRUE,
                    'max_filename'    => '50',
                    'max_size'        => '6144', // 6MB
                    'max_width'     => '0', // No limit on width
                    'max_height'    => '0' // No limit on height
                );
                $this->load->library('upload', $config);
                if($this->upload->do_upload('file')) {
                    $data = $this->upload->data();
                    foreach(array('name', 'year', 'month', 'day') as $var) if(isset($_POST[$var])) $insert[$var] = $_POST[$var];
                    $insert['doc_name'] = $data['file_name'];
                    $insert['section_id'] = $section_id;
                    $this->db->set($insert);
                    $this->db->insert('steering_docs');
                    $this->utils->redirect('archive/index/'.$section);
                    return;
                }
                else {
                    $errors = TRUE;
                    $other_errors = $this->upload->display_errors();
                }
            }
            else $errors = TRUE;
        }
        $this->load->view('archive/upload', array('section' => $full_section, 'short_section' => $section, 'errors' => $errors, 'other_errors' => $other_errors));
    }
    
    function delete_doc() {
        $docid = $this->uri->rsegment(3);
        if(is_admin() && $docid !== FALSE) {
            $this->db->where('id', $docid);
            $tmp1 = $this->db->get('steering_docs')->row_array(0);
            if(!empty($tmp1)) {
                $this->db->where('id', $tmp1['section_id']);
                $tmp2 = $this->db->get('steering_pages')->row_array(0);
                if(!empty($tmp2)) {
                    $this->db->where('id', $docid);
                    $this->db->delete('steering_docs');
                    unlink(VIEW_PATH.'archive/doc/'.$tmp2['short'].'/'.$tmp1['doc_name']);
                }
            }
        }
        $this->utils->redirect('archive');
    }
}

/* End of file archive.php */
/* Location: ./application/controllers/archive.php */