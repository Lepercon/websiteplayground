<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photos_model extends CI_Model {

	function Photos_model() {
		parent::__construct(); // Call the Model constructor
	}
	
	function get_album($a_id, $get_photos=FALSE){
		$this->db->where('id', $a_id);
		$album = $this->db->get('photo_albums')->row_array(0);
		if($get_photos){
			$this->db->where('album_id', $a_id);
			$this->db->where('published', 1);
			$this->db->join('users', 'users.id=photo.uploader');
			$this->db->select('photo.*, users.firstname, users.prefname, users.surname, users.uid');
			$photos = $this->db->get('photo')->result_array();
			foreach($photos as $p){
				$album['photos'][$p['id']] = $p;
			}
			foreach($album['photos'] as $p){
				$p_ids[] = $p['id'];
			}
			if(!empty($p_ids)){
				$this->db->where_in('photo_id', $p_ids);
				$this->db->order_by('surname, firstname');
				$this->db->join('users', 'users.id=photo_tags.user_id');
				$this->db->select('photo_tags.*, users.id as u_id, users.firstname, users.prefname, users.surname');
				$tags = $this->db->get('photo_tags')->result_array();
				foreach($tags as $t){
					$album['photos'][$t['photo_id']]['tags'][] = $t;
				}
			}
		}
		return $album;
	}
	
	function get_albums(){
		$this->db->order_by('date');
		$albums = $this->db->get('photo_albums')->result_array();
		$this->db->where('published', 1);
		$photos = $this->db->get('photo')->result_array();
		$al = array();
		foreach($albums as $a){
			$al[$a['id']] = $a;
		}
		foreach($photos as $p){
			$al[$p['album_id']]['photos'][] = $p;
		}
		return $al;
	}
	
	function get_photo($p_id, $only_published=TRUE){
		$this->db->where('id', $p_id);
		if($only_published){
			$this->db->where('published', 1);
		}
		return $this->db->get('photo')->row_array(0);
	}
	
	function get_slideshow_photos(){
		//Get photos of us!
		if(logged_in()){
			$this->db->limit(10);
			$this->db->join('photo_tags', 'photo_tags.photo_id=photo.id AND photo_tags.user_id='.$this->session->userdata('id'));
			$this->db->order_by('photo.timestamp DESC');
			$this->db->where('published', 1);
			$this->db->select('photo.*');
			$p1 = $this->db->get('photo')->result_array();
		}else{
			$p1 = array();
		}
		
		$this->db->limit(50);//50 most recent only
		$this->db->where('published', 1);
		$this->db->order_by('photo.timestamp DESC');
		$p2 = $this->db->get('photo')->result_array();
		shuffle($p2);//random selection
		$p2 = array_slice($p2, 0, 20 - sizeof($p1));//Cut down to just the right amount
		
		$photos = array_merge($p1, $p2);
		shuffle($photos);
		return $photos;
	}
	
	function new_album($name, $desc, $date){
		$data = array(
			'name'=>$name,
			'description'=>$desc,
			'date'=>$date,
			'date_modified'=>time(),
			'created_by'=>$this->session->userdata('id'),
			'modified_by'=>$this->session->userdata('id')
		);
		$this->db->insert('photo_albums', $data);
		return $this->db->insert_id();
	}
	
	function get_unpublished($a_id){
		$this->db->where('id', $a_id);
		$album = $this->db->get('photo_albums')->row_array(0);
		$this->db->where('album_id', $a_id);
		$this->db->where('published', 0);
		$album['photos'] = $this->db->get('photo')->result_array();
		return $album;
	}
	
	function publish_photos($a_id){
		$this->db->where('album_id', $a_id);
		$this->db->update('photo', array('published'=>1)); 
	}
	
	function update_album($a_id, $album_name, $date, $desc){
		$this->db->where('id', $a_id);
		$data = array(
			'name'=>$album_name,
			'description'=>$desc,
			'date'=>$date,
			'date_modified'=>time(),
			'modified_by'=>$this->session->userdata('id')
		);
		$this->db->update('photo_albums', $data);
	}
	
	/* Uploading */
	function add_part($uid, $n, $string){
		$this->db->insert('photo_parts', array(
			'uid'=>$uid,
			'n'=>$n,
			'string'=>$string
		));
	}
	
	function get_parts($uid){
		$this->db->where('uid', $uid);
		$this->db->order_by('n');
		$parts = $this->db->get('photo_parts')->result_array();
		$this->db->where('uid', $uid);
		$this->db->delete('photo_parts');
		return $parts;
	}
	
	function add_photo($fn, $tn, $a_id){
		$this->db->insert('photo', array(
			'album_id'=>$a_id,
			'photo_name'=>$fn,
			'thumb_name'=>$tn,
			'timestamp'=>time(),
			'uploader'=>$this->session->userdata('id'),
			'published'=>0
		));
	}
	
	function delete_photo($p_id){
		$this->db->where('id', $p_id);
		$this->db->delete('photo');
		$this->db->where('photo_id', $p_id);
		$this->db->delete('photo_tags');
	}
	
	function delete_unpublished($a_id, $path){
		$this->db->where('album_id', $a_id);
		$this->db->where('published', 0);
		$photos = $this->db->get('photo');
		$this->db->where('album_id', $a_id);
		$this->db->where('published', 0);
		$this->db->delete('photo');
		foreach($photos as $p){
			unlink($path.$photo['photo_name']);
			unlink($path.$photo['thumb_name']);
		}
	}
	
	function delete_album($a_id){
		$this->db->where('id', $a_id);
		$this->db->delete('photo_albums');
		$this->db->where('album_id', $a_id);
		$this->db->delete('photo');
	}
	
	function base64_to_jpeg($fn,$base64_string) {
	    $ifp = fopen($fn, "wb"); 
	    fwrite($ifp, base64_decode($base64_string)); 
	    fclose($ifp); 
	}
	
	function watermark_image($fn, $album_name){
		if(!$_POST['watermark']){
			return;
		}
		$type = $_POST['watermarktype'];//0 = Butler Logo & Name of Album, 1 = Gracehouse Logo, 2 = Custom Text
		
		if($type == 0){
			$img = new Imagick($fn);
		
			$d = $img->getImageGeometry();
			$fs = round($d['height']/20);
			
			/* Calculations */
			$ttl = round($d['width']-$fs*4);
			$ttt = round($d['height']-$fs*1.2);
			$tso = round($fs/15);
			$btl = round($d['width']-$fs*4);
			$btt = round($d['height']-$fs*.2);
			$bso = round($fs/20);
			
			/* Butler College */
			$draw = new ImagickDraw();
			$draw->setTextAlignment(Imagick::ALIGN_CENTER);
			$draw->setFillColor('black');
			$draw->setFont(VIEW_PATH.'photos/OpenSans.ttf');
			$draw->setFontSize($fs); 
			$draw->setFillOpacity(.8);
			$img->annotateImage($draw, $ttl + $tso, $ttt + $tso, 0, 'Butler College');
			$draw->setFillColor('#c80000');
			$draw->setFillOpacity(1);
			$img->annotateImage($draw, $ttl, $ttt, 0, 'Butler College');
			
			/* Album Name */
			$draw->setFontSize(round($fs*.6)); 
			$draw->setFillColor('black');
			$draw->setFillOpacity(.8);
			$img->annotateImage($draw, $btl + $bso, $btt + $bso, 0, $album_name);
			$draw->setFillColor('#c80000');
			$draw->setFillOpacity(1);
			$img->annotateImage($draw, $btl, $btt, 0, $album_name);
			
			/* Lines */
			$ls = round($d['width']-$fs*8);
			$le = round($d['width']-$fs*.1);
			$h1 = round($d['height']-$fs*.8);
			$h2 = round($d['height']-$fs*2.2);
			$lso = round($fs/30);
			
			$shadows = new ImagickDraw();
			$shadows->setStrokeOpacity(.8);
			$shadows->setStrokeColor('#101010');
		    $shadows->setStrokeWidth(round($fs/10));
		    $shadows->line($ls + $lso, $h1 + $lso, $le + $lso, $h1 + $lso);
		    $shadows->line($ls + $lso, $h2 + $lso, $le + $lso, $h2 + $lso);
		    $img->drawImage($shadows);
		    
		    $lines = new ImagickDraw();
		    $lines->setStrokeOpacity(1);
		    $lines->setStrokeColor('#eeb300');
		    $lines->setStrokeWidth(round($fs/10));
		    $lines->line($ls, $h1, $le, $h1);
		    $lines->line($ls, $h2, $le, $h2);
		    $img->drawImage($lines);
			
			log_message('done');
			$img->writeImage();
			
		}elseif($type == 1){
			
			$img = new Imagick($fn);
			$text = 'Please donate to Grace House to remove this watermark.';
			$d = $img->getImageGeometry();
			$fs = round($d['width']/60);
			$so = round($fs/20);
			
			$draw = new ImagickDraw();
			$draw->setTextAlignment(Imagick::ALIGN_RIGHT);
			$draw->setFillColor('black');
			$draw->setFont(VIEW_PATH.'photos/OpenSans.ttf');
			$draw->setFontSize($fs); 
			$draw->setFillOpacity(.8);
			$img->annotateImage($draw, round($d['width'] * .97)+$so, round($d['height'] - $d['width'] * .01)+$so, 0, $text);
			$draw->setFillColor('#c80000');
			$draw->setFillOpacity(1);
			$img->annotateImage($draw, round($d['width'] * .97), round($d['height'] - $d['width'] * .01), 0, $text);
			
			$logo = new Imagick(VIEW_PATH.'photos/gracehouselogowhite.png');
			$logo->resizeImage(round($d['width']*.4), 0, Imagick::FILTER_LANCZOS, 1);	
			$img->compositeImage($logo, Imagick::COMPOSITE_DEFAULT, round($d['width']*.53)+$so, round($d['height'] - $d['width']*.13)+$so);
			
			$logo = new Imagick(VIEW_PATH.'photos/gracehouselogo.png');
			$logo->resizeImage(round($d['width']*.4), 0, Imagick::FILTER_LANCZOS, 1);	
			$img->compositeImage($logo, Imagick::COMPOSITE_DEFAULT, round($d['width']*.53), round($d['height'] - $d['width']*.13));
			
			$img->writeImage();
						
		}elseif($type == 2){
		
			$img = new Imagick($fn);
			$text = $_POST['watermarktext'];
			$d = $img->getImageGeometry();
			$fs = round($d['width']/20);
			$so = round($fs/20);
			
			$draw = new ImagickDraw();
			$draw->setTextAlignment(Imagick::ALIGN_RIGHT);
			$draw->setFillColor('black');
			$draw->setFont(VIEW_PATH.'photos/OpenSans.ttf');
			$draw->setFontSize($fs); 
			$draw->setFillOpacity(.8);
			$img->annotateImage($draw, round($d['width'] * .97)+$so, round($d['height'] - $d['width'] * .01)+$so, 0, $text);
			$draw->setFillColor('#c80000');
			$draw->setFillOpacity(1);
			$img->annotateImage($draw, round($d['width'] * .97), round($d['height'] - $d['width'] * .01), 0, $text);
			$img->writeImage();
			
		}		
	}
	
	function add_tag($x, $y, $p_id, $u_id){
		$this->db->where('photo_id', $p_id);
		$this->db->where('user_id', $u_id);
		$tag = $this->db->get('photo_tags')->row_array(0);
		if(empty($tag)){
			$this->db->insert('photo_tags', array(
				'x'=>round($x,1), 
				'y'=>round($y,1), 
				'photo_id'=>$p_id,
				'user_id'=>$u_id,
				'created_by'=>$this->session->userdata('id'),
				'timestamp'=>time()
			));
		}else{
			$this->db->update('photo_tags', array('x'=>$x, 'y'=>$y, 'created_by'=>$this->session->userdata('id')), array('id'=>$tag['id']));
		}
	}
	
	function get_photo_sizes(){
		return $this->db->get('charity_sizes')->result_array();
	}
	
	function place_order(){

		$order_id = uniqid();
		$u_id = $this->session->userdata('id');
		$time = time();
		$status = 0;
		$data = array();
		
		$contents = $this->cart->contents();
		$total = 0;
		foreach($contents as $c){
			$data[] = array(
				'order_id' => $order_id,
				'order_by' => $u_id,
				'time' => $time,
				'photo_id' => $c['options']['photo_id'],
				'format' => $c['options']['type'],
				'price' => $c['price'],
				'qty' => $c['qty'],
				'status' => $status
			);
			$total += $c['price']*$c['qty'];
		}
		$this->db->insert_batch('photo_orders', $data);
		
		/* Add Invoice */
		$this->load->model('finance_model');		
		$member_ids = array($this->session->userdata('id'));
		$date = time();
		$name = 'Photo Purchase';
		$group_id = 1;
		$details = 'Photos purchased using the jcr website';
		$this->finance_model->add_members(1, $member_ids);
		$this->finance_model->add_invoice($member_ids, $date, $name, $total, $group_id, $details);
		
		/* Send Email */
		$this->load->library('email');
		$user = $this->users_model->get_users($u_id, 'firstname, surname, prefname, email');
			
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		$nl = '<br>';		
		
		$mail = 'Dear '.($user['prefname']==''?$user['firstname']:$user['prefname']).','.$nl.$nl;
		$mail .= 'Thank you very much for your photo order.'.$nl.$nl;
		$mail .= 'Full details on your order, including how to pay and how to collect your photos can be viewed by going to:';
		$mail .= ' <a href="'.site_url('photos').'">'.site_url('photos').'</a>, and clicking on "View My Orders"'.$nl.$nl;
		$mail .= 'Best Regards,'.$nl;
		$mail .= '<hr>This email was created by the Butler JCR Website (http://www.butlerjcr.com)';
		
		$this->email->to($users['email']);
		$this->email->cc('butler.jcr@durham.ac.uk');
		$this->email->bcc('samuel.stradling@durham.ac.uk');
		$this->email->from('butler.jcr@durham.ac.uk', 'Butler JCR');
		$this->email->message($mail);
		$this->email->subject('JCR Photo Orders');
		
		if(ENVIRONMENT != 'local'){
			$this->email->send();
		}
		
	}
	
	function get_my_orders(){
		$this->db->where('order_by', $this->session->userdata('id'));
		$this->db->select('photo_orders.*, photo.thumb_name');
		$this->db->order_by('time DESC');
		$this->db->join('photo', 'photo_orders.photo_id=photo.id');
		$orders = $this->db->get('photo_orders')->result_array();
		$or = array();
		foreach($orders as $o){
			$or[$o['order_id']][] = $o;
		}
		return $or;
	}
	
	function get_all_orders(){
		$this->db->select('photo_orders.*, photo.thumb_name, users.firstname, users.surname, users.prefname');
		$this->db->order_by('status');
		$this->db->join('photo', 'photo_orders.photo_id=photo.id');
		$this->db->join('users', 'photo_orders.order_by=users.id');
		$orders = $this->db->get('photo_orders')->result_array();
		$or = array();
		foreach($orders as $o){
			$or[$o['order_id']][] = $o;
		}
		return $or;
	}
	
	function update_order($order_id, $status){
		$this->db->where('order_id', $order_id);
		$this->db->update('photo_orders', array('status'=>$status));
	}
}


