<?php

class Markets extends CI_Controller {

    function Markets() {
        parent::__construct();
        $this->load->model('markets_model');
        $this->page_info = array(
            'id' => 23,
            'title' => 'Markets',
            'big_title' => '<span class="big-text-small">Durham Markets</span>',
            'description' => 'Request deliveries from Durham Market',
            'requires_login' => TRUE,
            'allow_non-butler' => TRUE,
            'require-secure' => FALSE,
            'css' => array(),
            'js' => array('markets/markets'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
    }

    function index() {
        $this->load->library('page_edit_auth');
        $this->load->view('markets/markets', array(
            'access_rights' => $this->page_edit_auth->authenticate('markets')
        ));
    }

    function details($errors = FALSE) {
        if(validate_form_token('market_order') && $this->input->post('details') != FALSE) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[100]|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[100]|xss_clean|valid_email');
            $this->form_validation->set_rules('phone', 'Phone', 'required|trim|max_length[20]|xss_clean');
            $this->form_validation->set_rules('delivery', 'Delivery day', 'required|trim|xss_clean');
            $this->form_validation->set_rules('college', 'College', 'required|trim|xss_clean');
            if($this->form_validation->run()) {
                $market_session = array(
                    'market_name' => $this->input->post('name'),
                    'market_email' => $this->input->post('email'),
                    'market_phone' => $this->input->post('phone'),
                    'market_delivery' => $this->input->post('delivery'),
                    'market_college' => $this->input->post('college')
                );
                $this->session->set_userdata($market_session);
                $this->meals();
                return;
            } else {
                $errors = TRUE;
            }
        }
        $this->load->view('markets/details', array(
            'errors' => $errors
        ));
    }

    function meals($errors = false) {
        if($this->session->userdata('market_name') === false or
        $this->session->userdata('market_email') === false or
        $this->session->userdata('market_phone') === false or
        $this->session->userdata('market_delivery') === false or
        $this->session->userdata('market_college') === false) {
            $this->details();
            return;
        } else {
            if(validate_form_token('market_order') && $this->input->post('meals') != false) {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('meal', 'Meal option', 'required|trim');
                $this->form_validation->set_rules('vegetarians', 'Number of vegetarians', 'required|integer');
                if($this->form_validation->run()) {
                    $this->session->set_userdata(array(
                        'market_meal' => $this->input->post('meal'),
                        'market_vegetarians' => $this->input->post('vegetarians')
                    ));
                    $this->groceries();
                    return;
                } else {
                    $errors = true;
                }
            }
            $this->load->view('markets/meals', array(
                'meals' => $this->markets_model->get_meals(),
                'errors' => $errors
            ));
        }
    }

	function groceries($errors = false) {
		if($this->session->userdata('market_name') === false or
		$this->session->userdata('market_email') === false or
		$this->session->userdata('market_phone') === false or
		$this->session->userdata('market_delivery') === false or
		$this->session->userdata('market_college') === false) {
			$this->details();
			return;
		} else if($this->session->userdata('market_meal') === false or
		$this->session->userdata('market_vegetarians') === false) {
			$this->meals();
			return;
		} else {
			$this->load->library('cart');
			
			if(validate_form_token('market_order') && $this->input->post('groceries') != false) {
				$this->load->library('form_validation');
				$this->form_validation->set_rules('spend', 'Spending cap', 'required|numeric');
				if($this->form_validation->run()) {
					$this->session->set_userdata(array(
						'market_spend' => $this->input->post('spend')
					));
					$groceries = array();
					foreach($_POST['veg'] as $k => $v) {
						if(!empty($v['amount'])) {
							$groceries[] = array(
								'id' => $v['id'],
								'qty' => 1,
								'price' => 1,
								'name' => $v['name'],
								'unit' => $v['unit'],
								'amount' => $v['amount']
							);
						}
					}
					$this->cart->destroy();
					if(!empty($groceries)) {
						$this->cart->insert($groceries);
					}
					$this->confirm();
					return;
				} else {
					$errors = true;
				}
			}
			$vegetables = $this->markets_model->get_items();
			$meal_name = $this->markets_model->get_meal_name($this->session->userdata('market_meal'));
			$categories = array();
			foreach($vegetables as $v) {
				if(!in_array($v['category'], $categories)) {
					$categories[] = $v['category'];
				}
			}
			sort($categories);
			$cart = $this->cart->contents();
			if(!empty($cart)) {
				$cart_made = array();
				foreach($cart as $c) {
					$cart_made[$c['id']] = $c['amount'];
				}
				$cart = $cart_made;
				unset($cart_made);
			}
			$favourites = array();
			if(logged_in()) {
				$favourites = $this->markets_model->get_favourites();
			}
			$this->load->view('markets/groceries', array(
				'vegetables' => $vegetables,
				'categories' => $categories,
				'meal_name' => $meal_name[0],
				'cart' => $cart,
				'errors' => $errors,
				'favourites' => $favourites
			));
		}
	}

	function confirm() {
		if($this->session->userdata('market_name') === false or
		$this->session->userdata('market_email') === false or
		$this->session->userdata('market_phone') === false or
		$this->session->userdata('market_delivery') === false or
		$this->session->userdata('market_college') === false) {
			$this->details();
			return;
		} else if($this->session->userdata('market_meal') === false or
		$this->session->userdata('market_vegetarians') === false) {
			$this->meals();
			return;
		} else if($this->session->userdata('market_spend') === false) {
			$this->groceries();
			return;
		} else {
			$meal_name = $this->markets_model->get_meal_name($this->session->userdata('market_meal'));
			$this->load->library('cart');
			$this->load->view('markets/confirm', array('meal_name' =>$meal_name[0]));
		}
	}

	function order() {
		if($this->session->userdata('market_name') === false or
		$this->session->userdata('market_email') === false or
		$this->session->userdata('market_phone') === false or
		$this->session->userdata('market_delivery') === false or
		$this->session->userdata('market_college') === false or
		$this->session->userdata('market_meal') === false or
		$this->session->userdata('market_vegetarians') === false or
		$this->session->userdata('market_spend') === false) {
			$this->confirm();
			return;
		} else {
			$data = array();
			$session_variables = array('name', 'email', 'phone', 'delivery', 'college', 'meal', 'vegetarians', 'spend');
			foreach($session_variables as $v) {
				$data[$v] = $this->session->userdata('market_'.$v);
			}
			$data['veg'] = array();
			
			//creates order number
			$ordernumber = random_string('alnum', 10);

			// Message is on one line to avoid formatting issues
			$message = 'A Durham Markets order has been placed by '.$data['name'].' ({unwrap}'.$data['email'].'{/unwrap}), with phone number '.$data['phone'].'.'."\r\n";
			// send email for order
			$this->load->library('email');
			$this->email->from($data['email'], $data['name']);
			if(logged_in()) {
				$message .= 'This came from the Butler JCR account of '.user_pref_name($this->session->userdata('firstname'),$this->session->userdata('prefname'),$this->session->userdata('surname')).' ({unwrap}'.$this->session->userdata('email').'{/unwrap}).'."\r\n";
			} else {
				$message .= 'The user was not logged in when they submitted the order.'."\r\n";
			}
			
			//$this->email->to('rupert.maspero@durham.ac.uk');
			$this->email->to($data['email'], $data['name']);

			$message .= 'Delivery is requested on '.$data['delivery'].' to '.$data['college'].'. Order details follow:'."\r\n\r\n";

			$this->email->subject('Durham Markets '. $ordernumber.'  - Butler JCR website');
			
			//meal pack info
			$get_meal_name = $this->markets_model->get_meal_name($this->session->userdata('market_meal'));
			$meal_name = $get_meal_name[0];
			$message .= 'Meal pack: '.$meal_name['name']."\r\n";
			$message .= 'No. of vegetarians: '.$data['vegetarians']."\r\n\r\n";
			$message .= 'Fruit and veg spending cap: '.number_format($data['spend'], 2, '.', ',')."\r\n";

            $this->load->library('cart');
            $cart = $this->cart->contents();
            if(!empty($cart)) {
                foreach($cart as $c) {
                    $message .= $c['amount'].' '.$c['unit'].' of '.$c['name']."\r\n";
                }
            } else {
                $message .= 'No fruit or veg ordered';
            }
            $message .= "\r\n_____________________\r\n";
            $message .= 'This email was created by the Butler JCR Website ({unwrap}http://www.butlerjcr.com{/unwrap})';
            $this->email->message($message);
            $this->email->send();

			//move to model??
			
			$insert = array();
			$user_id = $this->session->userdata('id');
			$unix_date = time();
				
			if(logged_in() && !empty($cart)) {
				foreach($cart as $c) {
					$insert[] = array(
						'order' => $ordernumber,
						'item' => $c['id'],
						'qty' => $c['amount'],
						'user' => $user_id,
						'time' => $unix_date,
						'meal' => 0,
						'cap' => $data['spend']
					);
				}
				$this->db->insert_batch('market_orders', $insert);
			}
			if(logged_in() && !empty($data)) {
					
				if($meal_name['name'] != 'No Meal'){
					
					$meal = intval($data['meal']);
					
					if($meal_name['name'] == '10 Week Veg Box £40'){
						$insert = array(
						'order' => $ordernumber,
						'item' => $meal,
						'qty' => 1,
						'user' => $user_id,
						'time' => $unix_date,
						'meal' => 1,
						'repeats' => 10,
						'cap' => $data['spend']
						);	
					}
					elseif($meal_name['name'] == '5 Week Veg Box £20'){
						$insert = array(
						'order' => $ordernumber,
						'item' => $meal,
						'qty' => 1,
						'user' => $user_id,
						'time' => $unix_date,
						'meal' => 1,
						'repeats' => 5,
						'cap' => $data['spend']
						);	
					}
					else{
						$insert = array(
						'order' => $ordernumber,
						'item' => $meal,
						'qty' => 1,
						'user' => $user_id,
						'time' => $unix_date,
						'meal' => 1,
						'veg' => $data['vegetarians'],
						'cap' => $data['spend']
						);	
					}
					
				$this->db->insert('market_orders', $insert);
				}
				
			}
			
			// show confirmation page
			$this->load->view('markets/success');

			// destroy cart information
			$this->cart->destroy();
			foreach($session_variables as $v) {
				$this->session->unset_userdata('market_'.$v);
			}
		}
	}

    function manage() {
        if(!(is_admin() or has_level('Green Committee Rep'))) {
            $this->index();
            return;
        }
        $errors = FALSE;
        if(validate_form_token('add_item')) {
            $this->load->library('form_validation');
            if(isset($_POST['add_item'])) {
                $this->form_validation->set_rules('item', 'Item name', 'trim|required|max_length[200]');
                $this->form_validation->set_rules('category', 'Category', 'required|trim|max_length[100]');
                $this->form_validation->set_rules('unit', 'Unit', 'required|trim|max_length[20]');
                if($this->form_validation->run()) {
                    $this->markets_model->add_item();
                }
                else $errors = TRUE;
            }
            elseif(isset($_POST['add_meal'])) {
                $this->form_validation->set_rules('meal', 'Meal name', 'trim|required|max_length[200]');
                if($this->form_validation->run()) {
                    $this->markets_model->add_meal();
                }
                else $errors = TRUE;
            }
        }
        $items = $this->markets_model->get_items();
        $meals = $this->markets_model->get_meals();
        $this->load->view('markets/manage', array('items' => $items, 'meals' => $meals, 'errors' => $errors));
    }

    function delete_item()
    {
        if(!(is_admin() or has_level('Green Committee Rep'))) {
            $this->index();
            return;
        }
        $d = $this->uri->rsegment(3);
        if($d !== FALSE && is_numeric($d)) {
            $this->markets_model->delete_item($d);
        }
        $this->manage();
    }

    function delete_meal()
    {
        if(!(is_admin() or has_level('Green Committee Rep'))) {
            $this->index();
            return;
        }
        $d = $this->uri->rsegment(3);
        if($d !== FALSE && is_numeric($d)) {
            $this->markets_model->delete_meal($d);
        }
        $this->manage();
    }

	function add_recipe()
	{
		if(!(is_admin() or has_level('Green Committee Rep'))) {
			$this->index();
			return;
		}
		$m = $this->uri->rsegment(3);
		if($m !== FALSE && is_numeric($m)) {
			if(validate_form_token('add_item')) {
				$config = array(
					'upload_path'	=> VIEW_PATH.'markets/recipes/',
					'allowed_types'	=> 'pdf',
					'overwrite'		=> TRUE, // if file of same name already exists, overwrite it
					'max_size'		=> '8192', // 8MB
					'file_name'		=> $m,
					'max_width'		=> '0', // No limit on width
					'max_height'	=> '0' // No limit on height
				);
				$this->load->library('upload', $config);
				$this->upload->do_upload();
			}
		}
		$this->manage();
	}
	
	function past_orders() {
		$past_orders = array(
			'orders' => $this->markets_model->get_past_orders()
		);
		$this->load->view('markets/past_orders', $past_orders);
	}
	
	function this_weeks_orders() {
		$due_orders = $this->markets_model->get_orders();
		$orders = array();
		foreach ($due_orders as $row)
		{
			$orders[] = $this->markets_model->ready_order($row['order'], $row['time']);
			
		}
		$send = array(
			'orders' => $due_orders,
			'order_content' => $orders
		);
		$this->load->view('markets/this_weeks_orders', $send);
	}
	
	function delivered() {
		$this->markets_model->mark_orders_delivered();
		$this->this_weeks_orders();
	}

}