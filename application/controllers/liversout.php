<?php

class Liversout extends CI_Controller {

    function Liversout()
    {
        parent::__construct();
        $this->page_info = array(
            'id' => 13,
            'title' => 'Livers Out',
            'big_title' => '<span class="big-text-small">livers </span><span class="big-text-medium">out</span>',
            'description' => 'Information for livers out',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => FALSE,
            'css' => array('liversout/liversout'),
            'js' => array('liversout/liversout'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
        $this->get_locations();
        if(validate_form_token('property_search')) {
            switch($_POST['searchtype']) {
                case 'postcode':
                    $this->utils->redirect('liversout/search/postcode/'.urlencode($_POST['postcode']));
                    break;
                case 'address':
                    $this->utils->redirect('liversout/search/address/'.urlencode($_POST['address']));
                    break;
                case 'area':
                    $this->utils->redirect('liversout/search/area/'.urlencode($_POST['area']));
                    break;
                default:
                    break;
            }
        }
        else {
            $this->load->model('events_model');
            $this->load->view('liversout/liversout', array(
                'events' => $this->events_model->get_events_by_category('Livers-Out', '3')
            ));
        }
    }

    function search() {
        $this->load->model('property_model');
        $type = $this->uri->rsegment(3);
        $parameter = urldecode($this->uri->rsegment(4));
        
        $sorting = array('Rating (High To Low)', 'Distance from Science Site', 'Distance from Elvet', 'Distance from College', 'Distance from Town', 'Price (High To Low)', 'Price (Low To High)');
        $sorts = array('rating DESC', 'time_science ASC', 'time_elvet ASC', 'time_college ASC', 'time_town ASC', 'price DESC', 'price ASC');
        
        if(isset($_POST['bedrooms']) && ($_POST['bedrooms'] !== '0')){
            $bedrooms = $_POST['bedrooms'];
        }else{
            $bedrooms = NULL;
        }
        log_message('error', 'bedrooms:'.$bedrooms);
        
        if(isset($_POST['sort-by'])){
            $sort_by = $sorts[$_POST['sort-by']];
        }else{
            $sort_by = $sorts[0];
        }
        
        if($type == 'postcode' && $parameter !== FALSE) $results = $this->property_model->search_by($parameter,'postcode', $sort_by, $bedrooms);
        elseif($type == 'address' && $parameter !== FALSE) $results = $this->property_model->search_address($parameter, $sort_by, $bedrooms);
        elseif($type == 'area' && $parameter !== FALSE && $parameter != 'all') $results = $this->property_model->search_by($parameter,'area', $sort_by, $bedrooms);
        else $results = $this->property_model->search_by('','postcode', $sort_by, $bedrooms);
        
        $bedrooms = array(0=>'All');
        foreach(range(1,7) as $r){
            $bedrooms[$r] = $r.' bedrooms';
        }
        $bedrooms['large'] = '8+ bedrooms';

        $this->load->view('liversout/search_results', array('results' => $results, 'sorting'=>$sorting, 'bedrooms'=>$bedrooms));
    }

    function view_property() {
        $p_id = $this->uri->rsegment(3);
        if($p_id === FALSE) {
            $this->index();
            return;
        }
        $errors = FALSE;
        $other_errors = array();
        $this->load->model('property_model');
        $this->load->library('form_validation');
        if(validate_form_token('add_photo')) {
            $this->form_validation->set_rules('caption','Photo Description','trim|xss_clean|max_length[100]');
            if($this->form_validation->run()) {
                $config['upload_path'] = VIEW_PATH.'liversout/img/property/';
                $config['allowed_types'] = 'jpg|jpeg';
                $config['max_size']    = '6048';
                $config['file_name'] = rand_alphanumeric(12).'.jpg';
                $config['max_filename']    = '20';
                $this->load->library('upload', $config);
                if(!$this->upload->do_upload()){
                    $errors = TRUE;
                    $other_errors[] = $this->upload->display_errors('', '');
                } else {
                    $data = $this->upload->data();
                    $img = new Imagick($data['full_path']);
                    $small_img = $img->clone();
                    $img->resizeImage(0, 800, Imagick::FILTER_LANCZOS, 1);
                    $img->writeImage($data['file_path'].$data['raw_name'].'.jpg');
                    $small_img->resizeImage(100, 0, Imagick::FILTER_LANCZOS, 1);
                    $small_img->writeImage($data['file_path'].$data['raw_name'].'_small.jpg');
                    $this->load->model('property_model');
                    $this->property_model->save_photo($p_id,$data['raw_name']);
                }
            }
            else $errors = TRUE;
        }
        $this->load->view('liversout/property_info', array(
            'p' => $this->property_model->get_property_from_id($p_id),
            'photos' => $this->property_model->get_photos_for_property($p_id),
            'review' => $this->property_model->get_reviews_for_property($p_id),
            'errors' => $errors,
            'other_errors' => $other_errors
        ));
    }

    function add_review() {
        $p_id = $this->uri->rsegment(3);
        if($p_id === FALSE) {
            $this->index();
            return;
        }
        $this->load->model('property_model');
        $p = $this->property_model->get_property_from_id($p_id);
        $this->load->library('form_validation');
        if(validate_form_token('add_review')) {
            $this->form_validation->set_rules('type','Property Type','required|trim|ucfirst|xss_clean|max_length[20]');
            $this->form_validation->set_rules('area','Property Area','required|trim|xss_clean|max_length[30]');
            $this->form_validation->set_rules('other_area','Name of Other Property Area','trim|xss_clean|max_length[30]'.($_POST['area']=='Other' ? '|required':''));
            $this->form_validation->set_rules('bedrooms','Number of Bedrooms','required|trim|integer|max_length[2]');
            $this->form_validation->set_rules('bathrooms','Number of Bathrooms','required|trim|integer|max_length[2]');
            $this->form_validation->set_rules('rent_cost','Weekly Rent','trim|numeric|max_length[10]|required');
            $this->form_validation->set_rules('bills_included','Weekly Bills Included','trim|max_length[1]|required');
            $this->form_validation->set_rules('bills_cost','Weekly Bills Amount','trim|numeric|max_length[10]'.($_POST['bills_included']=='0' ? '|required':''));
            $this->form_validation->set_rules('property_rating','Property Rating','trim|integer|required|max_length[1]');
            $this->form_validation->set_rules('living_area_rating','Living Area Rating','trim|integer|required|max_length[1]');
            $this->form_validation->set_rules('bedrooms_rating','Bedrooms Rating','trim|integer|required|max_length[1]');
            $this->form_validation->set_rules('bathrooms_rating','Bathrooms Rating','trim|integer|required|max_length[1]');
            $this->form_validation->set_rules('landlord_rating','Landlord Rating','trim|integer|required|max_length[1]');
            $this->form_validation->set_rules('landlord','Landlord Name','trim|required|xss_clean|max_length[50]');
            $this->form_validation->set_rules('landlord_responsive','Landlord Responsiveness','trim|xss_clean');
            $this->form_validation->set_rules('neighbours','Neighbour Problems','trim|xss_clean');
            $this->form_validation->set_rules('problems','Property Problems','trim|xss_clean');
            $this->form_validation->set_rules('comments','Additional Comments','trim|xss_clean');
            $this->form_validation->set_rules('recommend','Recommend to a Friend','trim|integer|required|max_length[1]');
            $this->form_validation->set_rules('allow_contact','Allow contact','trim|integer|required|max_length[1]');
            if($this->form_validation->run()) {
                $this->property_model->save_review($p);
                $this->utils->redirect('liversout/view_property/'.$p_id);
            }
            else $this->load->view('liversout/review_add', array('errors' => TRUE, 'p' => $p));
        }
        else $this->load->view('liversout/review_add', array('errors' => FALSE, 'p' => $p));
    }

    function add_property() {
        $this->load->library('form_validation');
        if(validate_form_token('add_property')) {
            $this->form_validation->set_rules('name','Property Name or Number','trim|required|ucwords|xss_clean|max_length[30]');
            $this->form_validation->set_rules('type','Property Type','trim|ucfirst|xss_clean|max_length[20]');
            $this->form_validation->set_rules('area','Property Area','trim|xss_clean|max_length[30]');
            $this->form_validation->set_rules('other_area','Name of Other Property Area','trim|xss_clean|max_length[30]'.($_POST['area']=='Other' ? '|required':''));
            $this->form_validation->set_rules('address1','Address Line 1','trim|xss_clean|required|max_length[30]|ucwords');
            $this->form_validation->set_rules('address2','Address Line 2','trim|xss_clean|max_length[30]|ucwords');
            $this->form_validation->set_rules('bedrooms','Number of Bedrooms','integer|max_length[2]');
            $this->form_validation->set_rules('bathrooms','Number of Bathrooms','integer|max_length[2]');
            $this->form_validation->set_rules('postcode','Postcode','trim|xss_clean|required|strtoupper|max_length[8]|min_length[6]');
            if($this->form_validation->run()) {
                $this->load->model('property_model');
                $check = $this->property_model->get_property_from_codes($_POST['name'],$_POST['postcode']);
                if($check !== FALSE) $this->load->view('liversout/property_add', array('errors' => TRUE, 'other_errors' => array('This property exists, please '.anchor('liversout/add_review/'.$check['id'], 'add a review'))));
                else {
                    if($_POST['area']=='Other') $_POST['area'] = $_POST['other_area'];
                    $this->property_model->add_property();
                    $p = $this->property_model->get_property_from_codes($_POST['name'],$_POST['postcode']);
                    $this->utils->redirect('liversout/add_review/'.$p['id']);
                }
            }
            else $this->load->view('liversout/property_add', array('errors' => TRUE));
        }
        else $this->load->view('liversout/property_add', array('errors' => FALSE));
    }

    function area() {
        $a_id = $this->uri->rsegment(3);
        if($a_id === FALSE) {
            $this->index();
            return;
        }
        $this->load->model('property_model');
        $this->load->library('page_edit_auth');
        $this->load->view('liversout/get_area', array(
            'access_rights' => $this->page_edit_auth->authenticate('liversout'),
            'page' => $a_id,
            'results' => $this->property_model->search_by(ucwords(str_replace("_"," ",$a_id)), 'area')
        ));
    }

    function tenancy_advice() {
        $this->load->library('page_edit_auth');
        $this->load->view('liversout/get_content', array('access_rights' => $this->page_edit_auth->authenticate('liversout'), 'page' => 'tenancy_advice'));
    }

    function proctors() {
        $this->load->library('page_edit_auth');
        $this->load->view('liversout/get_content', array('access_rights' => $this->page_edit_auth->authenticate('liversout'), 'page' => 'proctors'));
    }

    function house_hunting() {
        $this->load->library('page_edit_auth');
        $this->load->view('liversout/get_content', array('access_rights' => $this->page_edit_auth->authenticate('liversout'), 'page' => 'house_hunting'));
    }

    function resources() {
        $this->load->library('page_edit_auth');
        $this->load->view('liversout/get_content', array('access_rights' => $this->page_edit_auth->authenticate('liversout'), 'page' => 'resources'));
    }
    
    function get_locations(){
        $this->load->model('property_model');
        $this->property_model->get_all_locations();
        $this->property_model->get_all_times();
    }

}

/* End of file liversout.php */
/* Location: ./application/controllers/liversout.php */