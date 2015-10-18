<?php

class Photos extends CI_Controller {

    function Photos() {
        parent::__construct();
        $this->load->model('photos_model');
        $this->load->library('cart');
        $this->path = VIEW_PATH.'photos/images/';
        $this->url = VIEW_URL.'photos/images/';
        $this->statuses = array('Waiting for Payment', 'Order Paid', 'Order Complete');
        $this->page_info = array(
            'id' => 5,
            'title' => 'Media',
            'big_title' => '<span class="big-text-tiny">Photos & Videos</span>',
            'description' => 'Photos of us!',
            'requires_login' => FALSE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array('photos/photos'),
            'js' => array('photos/photos'),
            'keep_cache' => array('photos', 'photos/videos'),
            'editable' => FALSE
        );
    }
    
    function index(){
        $albums = $this->photos_model->get_albums();
        $this->load->view('photos/photos', array('albums'=>$albums, 'path'=>$this->url));
    }    
    
    function upload(){
                
        $a_id = $this->uri->segment(3);
        if($a_id === FALSE || !is_admin()){
            $this->index();
            return;
        }
        
        $album = $this->photos_model->get_album($a_id);
        if(empty($album)){
            $this->index();
            return;
        }
        
        $this->load->view('photos/image_upload', array('album'=>$album));
    }
    
    function upload_part(){
        if(!is_admin()){
            $this->index();
            return;
        }
        $this->photos_model->add_part($_POST['uid'], $_POST['n'], $_POST['string']);
    }
    
    function photo_complete(){
        
        $this->load->library('LZString');
        
        if(!is_admin()){
            $this->index();
            return;
        }
        
        $parts = $this->photos_model->get_parts($_POST['uid']);
        $string = '';
        foreach($parts as $p){
            $string .= $p['string'];
        }
        
        //$string = $this->lzstring->decompress($string);
        //log_message('error', var_export($string, true)); 
        
        $path = $this->path;
        $fn = uniqid();
        $this->photos_model->base64_to_jpeg($path.$fn.'.jpg',$string);
        $album = $this->photos_model->get_album($_POST['aid']);
        $this->photos_model->watermark_image($path.$fn.'.jpg', $album['name']);
        
        $img = new Imagick($path.$fn.'.jpg');        
        $img->resizeImage(350, 0, Imagick::FILTER_LANCZOS, 1);
        $img->writeImage($path.$fn.'_thumb.jpg');
        $this->photos_model->add_photo($fn.'.jpg', $fn.'_thumb.jpg', $_POST['aid']);
    }
    
    function album($a_id=NULL){
        
        if(is_null($a_id)){
            $a_id = $this->uri->segment(3);
            if($a_id === FALSE){
                $this->index();
                return;
            }
        }
        
        if(is_admin() && isset($_POST['publish'])){
            $this->photos_model->publish_photos($a_id);
        }
        
        if(is_admin() && isset($_POST['delete-unpublished'])){
            $this->photos_model->delete_unpublished($a_id, $this->path);
        }
        
        $album = $this->photos_model->get_album($a_id, true);
        if(empty($album)){
            $this->index();
            return;
        }
        
        $this->load->view('photos/album', array(
            'album'=>$album, 
            'url'=>$this->url,
            'path'=>$this->path,
            'users'=>$this->users_model->get_all_users('id, firstname, prefname, surname'),
            'sizes'=>$this->photos_model->get_photo_sizes()
        ));
    }
    
    function photo(){
    
        $p_id = $this->uri->segment(3);
        if($p_id === FALSE){
            $this->index();
            return;
        }
        
        $photo = $this->photos_model->get_photo($p_id);
        if(empty($photo)){
            $this->index();
            return;
        }
        $album = $this->photos_model->get_album($photo['album_id'], true);
        
        $this->load->view('photos/photo', array(
            'album'=>$album,
            'photo'=>$photo,
            'sizes'=>$this->photos_model->get_photo_sizes(),
            'path'=>$this->url
        ));
    }
    
    function rotate_photo(){
        $p_id = $this->input->post('photo_id');
        if($p_id === FALSE || !is_admin()){
            $this->index();
            return;
        }
        $photo = $this->photos_model->get_photo($p_id);
        $im = new Imagick($this->path.$photo['photo_name']);
        $im->rotateImage(new ImagickPixel('#00000000'), $this->input->post('angle'));
        $im->writeImage();
        $im = new Imagick($this->path.$photo['thumb_name']);
        $im->rotateImage(new ImagickPixel('#00000000'), $this->input->post('angle'));
        $im->writeImage();
    }
    
    function delete_photo(){
        $p_id = $this->input->post('photo_id');
        if($p_id === FALSE || !is_admin()){
            $this->index();
            return;
        }
        $photo = $this->photos_model->get_photo($p_id, FALSE);
        unlink($this->path.$photo['photo_name']);
        unlink($this->path.$photo['thumb_name']);
        $this->photos_model->delete_photo($p_id);
    }
    
    function delete_album(){
        $a_id = $this->input->post('a_id');
        if($a_id === FALSE || !is_admin()){
            $this->index();
            return;
        }
        $album = $this->photos_model->get_album($a_id, true);
        foreach($album['photos'] as $p){
            unlink($this->path.$p['photo_name']);
            unlink($this->path.$p['thumb_name']);
        }
        $this->photos_model->delete_album($a_id);
    }
    
    function add(){
        if(!is_admin()){
            $this->index();
            return;
        }
        if($this->input->post('create') !== FALSE){
            $name = $this->input->post('album-name');
            $desc = $this->input->post('album-desc');
            $date = explode('/', $this->input->post('date'));
            if(sizeof($date) == 3){
                $date = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
            }else{
                $date = time();
            }
            if(!empty($name)){
                $id = $this->photos_model->new_album($name, $desc, $date);
                $this->album($id);
                return;
            }else{
                $_POST['error'] = 'Album Name is reqired';
            }
        }
        $this->load->view('photos/new_album');
    }
    
    function unpublished(){
        if(!is_admin()){
            $this->index();
            return;
        }
        $a_id = $this->uri->segment(3);
        $album = $this->photos_model->get_unpublished($a_id);
        $this->edit($album);
    }
    
    function edit($album){
        if(!is_admin()){
            $this->index();
            return;
        }
        
        if(isset($_POST['update'])){
            $date = explode('/', $this->input->post('date'));
            $date = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
            $this->photos_model->update_album($_POST['a_id'], $_POST['album-name'], $date, $_POST['album-desc']);
        }
        
        if(!is_array($album)){
            $a_id = $this->uri->segment(3);
            $album = $this->photos_model->get_album($a_id, TRUE);
            $published = 1;
        }else{
            $published = 0;
        }
        $this->load->view('photos/edit', array(
            'album' => $album,
            'published' => $published,
            'path'=>$this->path,
            'url'=>$this->url
        ));
    }
    
    function add_tag(){
        
        if(!logged_in()){
            return;
        }
        $x = $this->input->post('x');
        $y = $this->input->post('y');
        $p_id = $this->input->post('p_id');
        $u_id = $this->input->post('u_id');
        $this->photos_model->add_tag($x, $y, $p_id, $u_id);    
    }
    
    function basket(){
        if(isset($_POST['update'])){
            $this->load->view('photos/basket_preview'); 
        }else{
            $this->index();
        }
    }
    
    function add_basket(){
        
        $p_id = $this->input->post('photo_id');
        $type = $this->input->post('type');
        $prices = $this->photos_model->get_photo_sizes();
        $price = 0;
        $t = array();
        foreach($prices as $p){
            if($p['id'] == $type){
                $price = $p['price'];
                $t = $p;
            }
        }
        
        $photo = $this->photos_model->get_photo($p_id);
        $data = array(
            'id'      => 'photo_'.$p_id.'_'.$type,
            'qty'     => 1,
            'price'   => $price,
            'name'    => 'Photo '.$p_id,
            'options' => array(
                'photo_id'=>$p_id,
                'type'=>$t['description'],
                'type-id'=>$t['id'],
                'thumb-name'=>$photo['thumb_name'],
            )
        );
        $this->cart->insert($data);
    }
    
    function update_basket(){
        $data = array();
        foreach($_POST['ids'] as $k=>$i){
            $options = $this->cart->product_options($_POST['ids'][$k]);
            if($options['type'] == 'Digital' && $_POST['vals'][$k] > 1){
                $_POST['vals'][$k] = 1;
            }
            $data[] = array(
               'rowid' => $_POST['ids'][$k],
               'qty'   => $_POST['vals'][$k]
            );
        }
        $this->cart->update($data); 
    }
    
    function checkout(){
        $this->load->view('photos/checkout');
    }
    
    function orders(){
        if(logged_in()){
            $orders = $this->photos_model->get_my_orders();
            $this->load->view('photos/my_orders', array(
                'orders'=>$orders,
                'statuses'=>$this->statuses
            ));
        }else{
            $_POST['order'] = array('success'=>FALSE, 'message'=>'You need to login to view your orders.');
            $this->index();
        }
    }
    
    function placed(){
        $orders = $this->photos_model->get_all_orders();
        $this->load->view('photos/all_orders', array(
            'orders'=>$orders,
            'statuses'=>$this->statuses
        ));
    }
    
    function change_status(){
        if(is_admin()){
            $this->photos_model->update_order($_POST['order_id'], $_POST['status']);
        }
    }
    
    function order(){
        if(logged_in()){
            $basket = $this->cart->contents();
            if(empty($basket)){
                $_POST['order'] = array('success'=>FALSE, 'message'=>'Please add something to your basket before checking out.');
                $this->checkout();
            }else{
                $this->photos_model->place_order();
                $this->cart->destroy();
                $_POST['order'] = array('success'=>TRUE, 'message'=>'Order Placed!');
                $this->orders();
            }
        }else{
            $_POST['order'] = array('success'=>FALSE, 'message'=>'You need to login to place your order.');
            $this->checkout();
        }
    }
    
    /*function videos() {
        $this->load->view('photos/videos');
    }
    
    function index() {
        $this->load->model('events_model');
        $range = $this->events_model->get_date_range();
        if($range == FALSE) {
            $this->load->view('photos/photos', array('year' => date('Y'), 'events' => '', 'range' => ''));
        } else {
            $now = date('Y');
            $uri = $this->uri->rsegment(3, $now);
            if($uri == $now && time() < mktime(0, 0, 0, 8, 1, $now)) {
                $start = $now - 1;
            } elseif ($range['max'] < $uri) {
                $start = date('Y', $range['max']);
                if(time() < mktime(0, 0, 0, 8, 1, $now)) {
                    $start = $start - 1;
                }
            } elseif($range['min'] > $uri) {
                $start = date('Y', $range['min']);
            } else {
                $start = $uri;
            }
            $all_events = $this->events_model->get_photos_by_academic_year($start);
        
            $this->load->view('photos/photos', array(
                'range' => $range,
                'year' => $start,
                'events' => $all_events,
            ));
        }
    }*/

    
}

/* End of file photos.php */
/* Location: ./system/application/controllers/photos.php */