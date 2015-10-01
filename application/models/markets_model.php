<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Markets_model extends CI_Model {

    function Markets_model()
    {
        parent::__construct();
    }

    function get_items()
    {
        $this->db->order_by('name');
        return $this->db->get('vegetables')->result_array();
    }

	function get_meals()
	{
		$this->db->order_by('id', 'desc');
		return $this->db->get('meals')->result_array();
	}

	function get_meal_name($meal_id)
	{
		$this->db->where('id', $meal_id);
		return $this->db->get('meals')->result_array();
	}


	function get_favourites()
	{
		$this->db->select('item');
		$this->db->where('user', $this->session->userdata('id'));
		$orders = $this->db->get('market_orders')->result_array();
		$favourites = array();
		if(!empty($orders)) {
			foreach($orders as $o) {
				$favourites[] = $o['item'];
			}
		}
		return $favourites;
	}
	
	function get_past_orders(){
		$this->db->where('user', $this->session->userdata('id'));
		$this->db->group_by('order'); 
		$orders = $this->db->get('market_orders')->result();
		return $orders;
	}

    // Admin Functions

    function add_meal()
    {
        $submit['name'] = $this->input->post('meal');
        $this->db->set($submit);
        $this->db->insert('meals');
    }

    function delete_meal($item_id)
    {
        $this->db->where('id', $item_id);
        $this->db->delete('meals');
        unlink(VIEW_PATH.'markets/recipes/'.$item_id.'.pdf');
    }

    function add_item()
    {
        $submit['name'] = $this->input->post('item');
        $submit['category'] = $this->input->post('category');
        $submit['unit'] = $this->input->post('unit');
        $this->db->set($submit);
        $this->db->insert('vegetables');
    }

	function delete_item($item_id)
	{
		$this->db->where('id', $item_id);
		$this->db->delete('vegetables');
	}
	
	function get_orders(){
		$this->db->where('delivered', 0);
		$this->db->group_by('order');
		$this->db->join('users', 'users.id = market_orders.user');
		$orders = $this->db->get('market_orders')->result_array();
		return $orders;
	}
	
	function ready_order($order_number, $order_time){
		$this->db->where('delivered', 0);
		$this->db->where('order', $order_number);
		$this->db->where('meal', 0);
		$this->db->join('vegetables', 'vegetables.id = market_orders.item');
		$orders1 = $this->db->get('market_orders')->result_array();
		
		$this->db->where('delivered', 0);
		$this->db->where('order', $order_number);
		$this->db->where('meal', 1);
		$this->db->join('meals', 'meals.id = market_orders.item');
		$orders2 = $this->db->get('market_orders')->result_array();
		
		$orders = array_merge($orders1, $orders2);
		
		return $orders;
	}
	
	function orders_to_be_marked(){
		$this->db->where('delivered', 0);
		$orders = $this->db->get('market_orders')->result_array();
		
		return $orders;
	}
	
	function mark_orders_delivered(){
		$to_be_marked_orders = $this->orders_to_be_marked();
		
		foreach($to_be_marked_orders as $key => $order){
			if($order['repeats']>1){
				$to_be_marked_orders[$key]['repeats']=$order['repeats']-1;
			}
			else{
				$to_be_marked_orders[$key]['delivered'] = 1;
				$to_be_marked_orders[$key]['repeats']=0;
			}
			
		}
		$this->db->update_batch('market_orders', $to_be_marked_orders, 'id'); 
	}

}