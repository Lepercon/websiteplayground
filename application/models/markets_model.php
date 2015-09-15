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
}