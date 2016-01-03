<?php

class Details extends CI_Controller {

    function Details() {
        parent::__construct();
        $this->page_info = array(
            'id' => 15,
            'title' => 'User Details',
            'big_title' => '<span class="big-text-small">User </span><span class="big-text-medium">Details</span>',
            'description' => 'View and edit user details',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => TRUE,
            'css' => array('details/details', 'jcrop'),
            'js' => array('details/details', 'jcrop'),
            'keep_cache' => array('details'),
            'editable' => FALSE
        );
    }

    function index() {
        $this->profile();
    }

    function profile() {
        // get the user id
        $user_id = $this->uri->rsegment(3);
        if($user_id === FALSE OR $user_id == $this->session->userdata('id')) {
            // no user id specified or user viewing own profile
            // set blank status variables
            $errors = FALSE;
            $other_errors = array();
            $success = FALSE;
            // load form validation library for set_value function availability
            $this->load->library('form_validation');
            if(isset($_POST['change_details']) && validate_form_token('details')) {
                switch($_POST['change_details']) {
                    case 'optional_information' :
                        $this->form_validation->set_rules('prefname','Preferred Name','trim|ucfirst|xss_clean|max_length[50]');
                        $this->form_validation->set_rules('level_desc','Role Description','trim|xss_clean|textarea_to_db');
                        //$this->form_validation->set_rules('mobile','Mobile No','trim|xss_clean|max_length[20]');
                        //$this->form_validation->set_rules('availability','Gym Availability','trim|xss_clean|textarea_to_db');
                        if($this->form_validation->run()) {
                            $levels = $this->users_model->get_user_levels($this->session->userdata('id'));
                            foreach($levels as $l){
                                $info = textarea_to_db($this->input->post('level_'.$l['level_id']));
                                $this->users_model->update_level_description($l['level_id'], $info);
                            }
                            // check if user has updated anything
                            if($this->session->userdata('prefname') != $this->input->post('prefname') OR !empty($_POST['level_desc'])) {
                                // then save to database
                                $this->db->where('id', $this->session->userdata('id'));
                                foreach(array('prefname', 'level_desc') as $v) {
                                    if(isset($_POST[$v])) {
                                        $this->db->set($v, $_POST[$v]);
                                    }
                                }
                                $this->db->update('users');
                                $this->session->set_userdata('prefname', $_POST['prefname']);
                                $success = 'Information Updated';
                            }
                        }
                        else $errors = TRUE;
                        break;
                    case 'crop_photo' :
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
                        if($this->upload->do_upload()) {
                            $image_data = $this->upload->data();
                            if($image_data['image_width'] > 200 && $image_data['image_height'] > 200) {
                                // resize image
                                try{
                                    $img = new Imagick($image_data['full_path']);
                                    $geo = $img->getImageGeometry();
                                    $this->autoRotateImage($img, $image_data['full_path']);
                                    if($geo['width'] > 800){
                                        $img->resizeImage(800, 0, Imagick::FILTER_LANCZOS, 1);
                                    }
                                    $geo = $img->getImageGeometry();
                                    if(strtolower($img->getImageFormat()) != 'jpeg') {
                                        // change format to jpg if required
                                        $img->setCompressionQuality(90);
                                        $img->setImageFormat('jpeg');
                                    }

                                    // save resized file
                                    $img->writeImage(VIEW_PATH.'details/img/tmp/'.$this->session->userdata('uid').'.jpg');
                                    // delete original upload
                                    unlink($image_data['full_path']);
                                    // load cropping view
                                    $this->load->view('details/crop', array('dims' => $geo));
                                    return;
                                }catch(ImagickException $e){
                                    log_message('error', var_export($e, true));
                                }
                            }
                            else {
                                unlink($image_data['full_path']);
                                // Image is below 200 x 200 pixels square
                                $errors = TRUE;
                                $other_errors = array('Image is too small. It must be at least 200 pixels wide by 200 pixels high');
                            }
                        }
                        else {
                            $errors = TRUE;
                            $other_errors = array($this->upload->display_errors());
                        }
                        break;
                    default :
                        break;
                }
            }
            $this->load->view('details/details', array('errors' => $errors, 'other_errors' => $other_errors, 'success' => $success));
        }
        else {
            $user = $this->users_model->get_users($user_id, 'uid, firstname, prefname, surname, email, registeredon, visitcount, level_desc');
            $user['levels'] = $this->users_model->get_levels_of_user($user_id, TRUE);
            $this->load->view('details/profile', array('user' => $user));
        }
    }
    
    function autoRotateImage($image, $fn) {
        try {
            $exif = exif_read_data($fn, 'IFD0');
            if(!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 3: 
                        $image->rotateimage("#000", 180); // rotate 180 degrees
                    break;
            
                    case 6:
                        $image->rotateimage("#000", 90); // rotate 90 degrees CW
                    break;
            
                    case 8: 
                        $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                    break;
                }
            }
            
            // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
            $image->stripImage();
        } catch (Exception $e) {
            log_message('error', 'Caught exception: '.$e->getMessage());
        }
    }

    function change_remember() {
        $rand = rand_alphanumeric(30);
        if($this->session->userdata('rand_exp') == 1) {
            // update cookie
            $this->input->set_cookie('username', $this->session->userdata('username'), 0);
            $this->input->set_cookie('rand', $rand, 0);

            // update session data
            $this->session->set_userdata('rand_exp', '0');

            // update database
            $this->db->set(array('rand_exp' => 0, 'rand' => $rand));
            $this->db->where('username', $this->session->userdata('username'));
            $this->db->update('users');
        } else {
            // update cookie
            $this->input->set_cookie('username', $this->session->userdata('username'), 8640000);
            $this->input->set_cookie('rand', $rand, 8640000);

            // update session data
            $this->session->set_userdata('rand_exp', '1');

            // update database
            $this->db->set(array('rand_exp' => 1, 'rand' => $rand));
            $this->db->where('username', $this->session->userdata('username'));
            $this->db->update('users');
        }

        // redirect to details page
        $this->utils->redirect('details');
    }

    function crop() {
        if(validate_form_token('details_crop')) {
            if(isset($_POST['x-large']) && isset($_POST['x-small'])) {
                try{
                    $img['large'] = new Imagick(VIEW_PATH.'details/img/tmp/'.$this->session->userdata('uid').'.jpg');
                    $img['medium'] = $img['large']->clone();
                    $img['small'] = $img['large']->clone();
                    $img['tiny'] = $img['large']->clone();
                    $min['large'] = 200;
                    $min['medium'] = 200;
                    $min['small'] = 100;
                    $min['tiny'] = 50;
    
                    // get img dims
                    $dims = $img['large']->getImageGeometry();
    
                    // validate dimensions for large and small
                    foreach(array('large','small') as $s) {
                        if($_POST['x-'.$s] > ($dims['width']-$min[$s]) OR $_POST['y-'.$s] > ($dims['height']-$min[$s]) OR $_POST['h-'.$s] < $min[$s] OR $_POST['w-'.$s] < $min[$s] ) {
                            $this->load->view('details/crop', array('dims' => $dims, 'error' => ucfirst($s).' crop dimensions not valid.'));
                            log_message('error', (($_POST['x-'.$s] > ($dims['width']-$min[$s]))?1:0).(($_POST['y-'.$s] > ($dims['height']-$min[$s]))?1:0).(($_POST['h-'.$s] < $min[$s])?1:0).(($_POST['w-'.$s] < $min[$s])));
                            return;
                        }
                    }
    
                    /////   we are now valid!  Go to work...
    
                    foreach(array('w','h','x','y') as $l){
                        $_POST[$l.'-tiny'] = $_POST[$l.'-small'];
                        $_POST[$l.'-medium'] = $_POST[$l.'-small'];
                    }
    
                    // crop to large, small and tiny
                    foreach(array('large','medium','small','tiny') as $s) {
                        if(!$img[$s]->cropImage($_POST['w-'.$s], $_POST['h-'.$s], $_POST['x-'.$s], $_POST['y-'.$s])) {
                            $this->load->view('details/crop', array('dims' => $dims, 'error' => ucfirst($s).' crop dimensions not valid.'));
                            return;
                        }
                        $img[$s]->resizeImage($min[$s], 0, Imagick::FILTER_LANCZOS, 1); // resize the image
                        $img[$s]->writeImage(VIEW_PATH.'details/img/users/'.$this->session->userdata('uid').'_'.$s.'.jpg'); // save the image
                    };
    
                    // remove the temporary image
                    unlink(VIEW_PATH.'details/img/tmp/'.$this->session->userdata('uid').'.jpg');
    
                    $this->load->view('details/details', array('errors' => FALSE, 'success' => 'Your photo has been uploaded successfully'));
                } catch (ImagickException $e) {
                    log_message('error', var_export($e, true));
                }
            }
            else {
                $this->load->view('details/crop', array('dims' => array('height' => '', 'width' => ''), 'error' => 'Please enable javascript for this page'));
            }
        }
        else $this->profile();
    }
}

/* End of file details.php */
/* Location: ./application/controllers/details.php */