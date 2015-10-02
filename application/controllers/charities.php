<?php class Charities extends CI_Controller {

	function Charities() {
		parent::__construct();
		$this->load->model('charities_model');
		$this->load->library('cart');
	}

	function index() {
		$this->load->library('page_edit_auth');
		$this->load->model('events_model');
		$this->load->view('charities/charities',array(
			'access_rights' => $this->page_edit_auth->authenticate('charities'),
			'events' => $this->events_model->get_events_by_category(15, '3')
		));
	}

	function orders($errors = FALSE, $other_errors = array()) {
		$this->load->library('page_edit_auth');
		$albums = $this->charities_model->get_albums();
		if(!empty($albums)) {
			foreach($albums as &$a) {
				$files = glob(VIEW_PATH.'charities/photos/album_'.$a['id'].'/*.png');
				$file = array_rand($files);
				$a['thumb'] = substr($files[$file], strrpos($files[$file], '/') + 1);
			}
		}
		$this->load->view('charities/orders',array(
			'access_rights' => $this->page_edit_auth->authenticate('charities'),
			'albums' => $albums,
			'sizes' => $this->charities_model->get_sizes(),
			'errors' => $errors,
			'other_errors' => $other_errors
		));
	}

	function view_photo() {
		$p_id = $this->uri->segment(3);
		if($p_id == FALSE) {
			$this->index(TRUE);
			return;
		}
		$photo = $this->charities_model->get_photo($p_id);
		$photos = $this->charities_model->get_photos($photo['album_id']);
		$current = 0;
		$length = count($photos);
		foreach($photos as $k => $p) {
			if($p['id'] == $p_id) {
				$current = $k;
			}
		}
		if($current == $length - 1) {
			$next = $photos[0]['id'];
		} else {
			$next = $photos[$current + 1]['id'];
		}
		if($current == 0) {
			$prev = $photos[$length - 1]['id'];
		} else {
			$prev = $photos[$current - 1]['id'];
		}
		$album = $this->charities_model->get_album($photo['album_id']);
		$this->load->view('charities/view_photo',array(
			'photo' => $photo,
			'album' => $album,
			'sizes' => $this->charities_model->get_sizes(),
			'next' => $next,
			'prev' => $prev
		));
	}

	function view_album($a = FALSE) {
		if($a !== FALSE){
			$a_id = $a;
		}else{
			$a_id = $this->uri->segment(3);
		}
		if($a_id == FALSE) {
			$this->index();
			return;
		}
		$this->load->library('page_edit_auth');
		$this->load->view('charities/order_photos',array(
			'photos' => $this->charities_model->get_photos($a_id),
			'album' => $this->charities_model->get_album($a_id),
			'sizes' => $this->charities_model->get_sizes(),
			'access_rights' => $this->page_edit_auth->authenticate('charities')
		));
	}

	function edit_album() {
		$p_id = $this->uri->segment(3);
		if(!has_level('any') OR $p_id == FALSE) {
			$this->index(TRUE);
			return;
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Album Title', 'required|trim|max_length[50]|xss_clean');
		$this->form_validation->set_rules('description', 'Album Description', 'trim|xss_clean');
		$this->form_validation->set_rules('event_time', 'Event time', 'required|trim|max_length[10]|xss_clean');
		if($this->form_validation->run()) {
			$date = explode('/', $this->input->post('event_time'));
			$this->db->where('id', $p_id);
			$this->db->set(array(
					'title' => $this->input->post('title'),
					'description' => $this->input->post('description'),
					'event_time' => mktime(0,0,0,$date[1],$date[0],$date[2])
			));
			$this->db->update('charity_albums');
			$this->view_album($p_id);
		} else {
			$this->upload($p_id, TRUE);
		}
	}

	function add_album() {
		if(!has_level('any')) {
			$this->index();
			return;
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Album Title', 'required|trim|max_length[50]|xss_clean');
		$this->form_validation->set_rules('description', 'Album Description', 'trim|xss_clean');
		$this->form_validation->set_rules('event_time', 'Event time', 'required|trim|max_length[10]|xss_clean');
		if($this->form_validation->run()) {
			$date = explode('/', $this->input->post('event_time'));
			$this->db->set(array(
					'title' => $this->input->post('title'),
					'description' => $this->input->post('description'),
					'event_time' => mktime(0,0,0,$date[1],$date[0],$date[2]),
					'created_by' => $this->session->userdata('id'),
					'created_time' => time()
			));
			$this->db->insert('charity_albums');
			$this->upload($this->db->insert_id());
		} else {
			$this->orders(TRUE);
		}
	}

	function add_photo() {
		$p_id = $this->uri->segment(3);
		if($p_id == FALSE) {
			$this->index();
			return;
		}
		$sizes = $this->charities_model->get_sizes();
		$photo_db = $this->charities_model->get_photo($p_id);
		if(!empty($_POST['format'])) {
			foreach ($_POST['format'] as $f){
				$photo = array(
					'id' => $photo_db['id'],
					'qty' => ($f == '1') ? 1 : $this->input->post('quantity'),
					'price' => $this->charities_model->get_price($sizes, $f),
					'name' => $photo_db['id'],
					'options' => array('format'=>$f)
				);

				$this->cart->insert($photo);
			}
		}
		$this->view_album($photo_db['album_id']);
	}

	function remove_from_basket() {
		$p_id = $this->uri->segment(3);
		if($p_id != FALSE) {
			$this->cart->update(array(
				'rowid' => $p_id,
				'qty' => 0
			));
		}
	}

	function delete_album() {
		$a_id = $this->uri->segment(3);
		if($a_id == FALSE OR !is_numeric($a_id)) {
			$this->index();
			return;
		}

		$this->load->library('page_edit_auth');
		$access_rights = $this->page_edit_auth->authenticate('charities');

		$album = $this->charities_model->get_album($a_id);
		if($album['created_by'] == $this->session->userdata('id') OR $access_rights > 0) {
			$photos = $this->charities_model->get_photos($a_id);
			foreach($photos as $p) {
				// Delete file
				unlink(VIEW_PATH.'charities/photos/album_'.$p['album_id'].'/'.$p['filename'].'.png');

				// Delete from db
				$this->charities_model->delete_photo($p['id']);
			}
			$this->charities_model->delete_album($a_id);
			$this->orders();
		} else {
			$this->view_album($a_id);
		}
	}

	function delete_photo() {
		$p_id = $this->uri->segment(3);
		if($p_id == FALSE) {
			$this->index();
			return;
		}

		// Get photo information for album id and filename
		$photo_db = $this->charities_model->get_photo($p_id);

		$album = $this->charities_model->get_album($photo_db['album_id']);

		$this->load->library('page_edit_auth');
		$access_rights = $this->page_edit_auth->authenticate('charities');

		if($access_rights > 0 OR $photo_db['uploaded_by'] == $this->session->userdata('id') OR $album['created_by'] == $this->session->userdata('id')) {
			// Delete file
			unlink(VIEW_PATH.'charities/photos/album_'.$photo_db['album_id'].'/'.$photo_db['filename'].'.png');

			// Delete from db
			$this->charities_model->delete_photo($p_id);
		}

		$this->view_album($photo_db['album_id']);
	}

	function submit_form() {
		if(isset($_POST['clear'])){
			$this->cart->destroy();
			$this->orders();
		}elseif(isset($_POST['checkout'])){
			$this->basket();
		}elseif(isset($_POST['update'])){
			$data = array();
			foreach($_POST as $post => $value){
				if(strpos($post, 'product_') === 0){
					$options = $this->cart->product_options(str_replace('product_', '', $post));
					if($options['format'] == '1') {
						$value = 1;
					}
					$data[] = array(
						'rowid' => str_replace('product_', '', $post),
						'qty' => $value
					);
				}
			}
			$this->cart->update($data);
			$a_id = $this->uri->segment(3);
			if(is_numeric($a_id)) {
				$this->view_album($a_id);
			} elseif($a_id == 'orders') {
				$this->orders();
			} elseif($a_id == 'basket') {
				$this->basket();
			} else {
				$this->index();
			}
		}else{
			$this->index();
		}
	}

	function sizes($errors = FALSE) {
		$this->load->library('page_edit_auth');
		$access_rights = $this->page_edit_auth->authenticate('charities');
		if($access_rights > 0) {
			if(isset($_POST['add'])) {
				$this->load->library('form_validation');
				$this->form_validation->set_rules('description', 'Size', 'trim|required|max_length[50]');
				$this->form_validation->set_rules('price', 'Price', 'trim|required|max_length[7]|numeric');
				if($this->form_validation->run()) {
					$this->charities_model->add_size();
				} else {
					$errors = TRUE;
				}
			} elseif(isset($_POST['update'])) {
				$this->load->library('form_validation');
				$this->form_validation->set_rules('description', 'Size', 'trim|required|max_length[50]');
				$this->form_validation->set_rules('price', 'Price', 'trim|required|max_length[7]|numeric');
				$this->form_validation->set_rules('id', 'ID', 'trim|required|max_length[11]|integer');
				if($this->form_validation->run()) {
					$this->charities_model->update_size();
				} else {
					$errors = TRUE;
				}
			} elseif(isset($_POST['delete'])) {
				$this->load->library('form_validation');
				$this->form_validation->set_rules('id', 'ID', 'trim|required|max_length[11]|integer');
				if($this->form_validation->run() && $this->input->post('id') !== '1') {
					$this->charities_model->delete_size();
				} else {
					$errors = TRUE;
				}
			}
			$this->load->view('charities/sizes', array(
				'sizes' => $this->charities_model->get_sizes(),
				'errors' => $errors
			));
		} else {
			$this->index();
		}
	}

	function basket() {
		$this->load->library('page_edit_auth');
		$this->load->view('charities/basket_checkout', array(
			'access_rights' => $this->page_edit_auth->authenticate('charities'),
			'albums'=>$this->charities_model->get_albums(),
			'sizes' => $this->charities_model->get_sizes()
		));
	}

	function checkout($errors = FALSE) {
		$other_errors = array();
		$sizes = $this->charities_model->get_sizes();
		if($this->cart->total_items() > 0) {
			$order_time = time();
			$order_id = rand_alphanumeric(11);
			$message = 'Thank you for your charity photo order. All profit from your order will be donated to Grace House Hospice.<br /><br />';
			foreach($this->cart->contents() as $items) {
				$description = '';
				foreach($this->cart->product_options($items['rowid']) as $v) {
					foreach($sizes as $s) {
						if($s['id'] == $v) {
							$description = $s['description'];
						}
					}
				}
				$submit = array(
					'order_id' => $order_id,
					'order_by' => $this->session->userdata('id'),
					'order_time' => $order_time,
					'item_id' => $items['name'],
					'item_format' => $description,
					'item_price' => $items['price'],
					'item_qty' => $items['qty'],
					'status' => 'submitted',
				);
				$this->db->insert('charity_orders', $submit);
				$message .= $items['qty'].' '.$description.' copy of photo '.$items['name'].' at &#163;'.$items['price'].' each.<br />';
			}
			$message .= '<br />Total: &#163;'.$this->cart->total();

			$this->load->library('email');

			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$this->email->initialize($config);

			$this->email->from($this->session->userdata('email'), user_pref_name($this->session->userdata('firstname'), $this->session->userdata('prefname'), $this->session->userdata('surname')));
			$this->email->to($this->session->userdata('email'));
			$cc = array('butler.jcr@durham.ac.uk');
			$users = $this->users_model->get_users_with_level(57, 'users.email');
			foreach($users as $u) {
				$cc[] = $u['email'];
			}
			$this->email->cc($cc);
			$this->email->subject('Charity Photo Order '.$order_time.$this->session->userdata('id'));
			$this->email->message($message);
			if(ENVIRONMENT !== 'development') {
				$this->email->send();
			}
			$this->cart->destroy();
		} else {
			$errors = TRUE;
			$other_errors[] = 'There are no items in your basket.';
		}
		if($errors !== FALSE) {
			$this->orders($errors, $other_errors);
		} else {
			$this->load->library('page_edit_auth');
			$this->load->view('charities/success', array(
				'access_rights' => $this->page_edit_auth->authenticate('charities')
			));
		}
	}

	function upload($album = FALSE, $errors = FALSE) {
		if($album !== FALSE) {
			$a_id = $album;
		} else {
			$a_id = $this->uri->segment(3);
		}
		if(!has_level('any') OR $a_id == FALSE) {
			$this->index();
			return;
		}
		$album = $this->charities_model->get_album($a_id);
		$this->load->view('charities/upload', array('album' => $album, 'errors' => $errors));
	}

	function upload_image() {
		if(!has_level('any')) {
			$this->index();
			return;
		}
		if(!file_exists(VIEW_PATH.'charities/photos/album_'.$_POST['album'])) {
			mkdir(VIEW_PATH.'charities/photos/album_'.$_POST['album']);
		}
		$name = uniqid();
		while(file_exists(VIEW_PATH.'charities/photos/album_'.$_POST['album'].'/'.$name.'.png')) {
			$name = uniqid();
		}
		$img = $_POST['img'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		file_put_contents(VIEW_PATH.'charities/photos/album_'.$_POST['album'].'/'.$name.'.png', base64_decode($img));

		$this->db->set(array(
			'album_id' => $_POST['album'],
			'filename' => $name,
			'uploaded_by' => $this->session->userdata('id'),
			'uploaded_time' => time()
		));
		$this->db->insert('charity_photos');
	}

	function list_orders() {
		$this->load->library('page_edit_auth');
		$access_rights = $this->page_edit_auth->authenticate('charities');
		if($access_rights > 0) {
			$this->load->view('charities/list_orders', array(
				'orders' => $this->charities_model->get_orders()
			));
		} else {
			$this->index();
		}
	}

	function update_status() {
		$this->load->library('page_edit_auth');
		$access_rights = $this->page_edit_auth->authenticate('charities');
		if($access_rights > 0) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('order_id', 'Order ID', 'required|trim|xss_clean');
			$this->form_validation->set_rules('status', 'Status', 'required|trim|max_length[20]|xss_clean');
			if($this->form_validation->run()) {
				$this->db->where('order_id', $this->input->post('order_id'));
				$this->db->set('status', $this->input->post('status'));
				$this->db->update('charity_orders');
			}
		}
	}

	function delete_order() {
		$this->load->library('page_edit_auth');
		$access_rights = $this->page_edit_auth->authenticate('charities');
		if($access_rights > 0 && $this->uri->segment(3) != FALSE) {
			$this->db->where('order_id', $this->uri->segment(3));
			$this->db->delete('charity_orders');
		}
	}
    
    function dare_night(){
    
    	if(logged_in()){
			$this->load->model('events_model');
			
			$id = $this->uri->rsegment(3);
			$u_id = $this->session->userdata('id');
			if($id === FALSE){
				$dares = $this->charities_model->get_dare_night_events();
				for($i=0;$i<sizeof($dares);$i++){
					$dares[$i]['event_info'] = $this->events_model->get_event($dares[$i]['event_id']);
				}
				
				$this->load->view('charities/dare_night/dare_night', array(
					'dare_nights'=>$dares
				));
			}else{
				$tab = 'information';
				$post = $this->input->post();
				$message = '';
				$success = true;
				if($post !== FALSE){
					switch($post['submit_type']){
						case 'team_info':
							$tab = 'team';					
							$message = 'Your Team Member Information has been Successfully Submitted!';
							for($i=1;$i<=6;$i++){
								if($post['member-'.$i.'-hidden'] == ''){
									$post['member-'.$i.'-hidden'] = NULL;
								}
							}
							$this->charities_model->dare_update_team($post['team_id'], $post['teamnanme'], $post['member-1-hidden'], $post['member-2-hidden'], $post['member-3-hidden'], $post['member-4-hidden'], $post['member-5-hidden'], $post['member-6-hidden']);
							break;
						case 'dare_evidence':
					    	$config = array(
					    		'upload_path'=>'./application/views/charities/dare_night/images/',
								'allowed_types'=>'jpeg|jpg|png|gif',
								'max_size'=>5000,
								'encrypt_name'=>TRUE
							);
							$this->load->library('upload', $config);
							$tab = 'dare-'.$post['dare_num'];
							if($this->upload->do_upload('userfile'.$post['dare_num'])){
								$data = $this->upload->data();
								$message = 'Your file has been successfully uploaded.';
								$this->charities_model->dare_add_file($post['team_id'], $post['dare_num'], $data['file_name'], $post['details']);							
							}else{
								$message = $this->upload->display_errors();
								$success = false;
							}
							$this->charities_model->dare_update_details($post['team_id'], $post['dare_num'], $post['details']);
							break;
						case 'dare_evidence_details':
							$tab = 'dare-'.$post['dare_num'];
							$message = 'Your details have been updated.';
							$this->charities_model->dare_update_details($post['team_id'], $post['dare_num'], $post['details']);
							break;
						case 'dare_delete_photo':
							$tab = 'dare-'.$post['dare_num'];
							$message = 'Your photo has been removed.';				
							$this->charities_model->remove_photo($post['team_id'], $post['dare_num']);
							break;
						case 'darenight_submit_confirm':
							if($post['dare-night-submit-confirm'] === 'Confirm Submission'){
								$this->charities_model->confirm_dare($post['team_id']);
							}else{
								$this->charities_model->confirm_dare($post['team_id'], 0);
							}
							break;
					}
				}
				$dare = $this->charities_model->get_dare_night($id);
				$team = $this->charities_model->get_dare_night_entry($id, $u_id);
				if(isset($team[0]) && $this->uri->segment(4) !== FALSE){
					$entry_no = $this->uri->segment(4);
					foreach($team as $t){
						if($t['id'] === $entry_no){
							$team = $t;
							break;
						}
					}
				}
				
				if($post['submit_type'] == 'submit_all'){
					$this->load->view('charities/dare_night/review_dare', array(
						'info'=>$dare,
						'event'=>$this->events_model->get_event($dare['event_id']),
						'team'=>$team
					));
					return;
				}
				
				$submissions = array();
				$admin = $this->charities_model->charities_permissions();
				if($admin){
					$submissions = $this->charities_model->get_dare_submissions($dare['id'], true);
				}
				
				$this->load->view('charities/dare_night/view_dare', array(
					'info'=>$dare,
					'event'=>$this->events_model->get_event($dare['event_id']),
					'u_id'=>$u_id,
					'users'=>$this->users_model->get_all_user_ids_and_names(),
					'post'=>$post,
					'team'=>$team,
					'tab'=>$tab,
					'message'=>$message,
					'success'=>$success,
					'admin'=>$admin,
					'submissions'=>$submissions
				));
			}
		}else{
			$this->load->view('home/login_prompt');
		}
	}
	
	function view_submission(){
		if($this->charities_model->charities_permissions()){
			
			$this->load->model('events_model');
			
			$team_no = $this->uri->segment(3);
			$team = $this->charities_model->get_darenight_team($team_no, true);
			$info = $this->charities_model->get_dare_night($team['darenight_event_id']);
			
			$this->load->view('charities/dare_night/review_dare', array(
				'info'=>$info,
				'team'=>$team,
				'event'=>$this->events_model->get_event($info['event_id']),
				'admin'=>true,
				'submissions'=>$this->charities_model->get_dare_submissions($info['id'], true)
			));
		}
	}

}

/* End of file charities.php */
/* Location: ./application/controllers/charities.php */