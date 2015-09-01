<?php

class Game extends CI_Controller {

	function Game() {
		parent::__construct();
	}

	function index() {
		$this->load->model('events_model');
		$this->load->model('green_model');
		$this->load->view('game/game', array(
			'tip' => $this->green_model->get_tip(),
			'data' => $this->events_model->get_week()
		));
	}

	function snake() {
		$scores = $this->get_scores('snake');
		$this->tidy_scores('snake', $scores);
		$this->load->view('game/snake', array(
			'scores' => $scores
		));
	}

	function brick() {
		$scores = $this->get_scores('brick');
		$this->tidy_scores('brick', $scores);
		$this->load->view('game/brick', array(
			'scores' => $scores
		));
	}

	function missile() {
		$scores = $this->get_scores('missile');
		$this->tidy_scores('missile', $scores);
		$this->load->view('game/missile', array(
			'scores' => $scores
		));
	}

	function defend() {
		$scores = $this->get_scores('defend');
		$this->tidy_scores('defend', $scores);
		$this->load->view('game/defend', array(
			'scores' => $scores
		));
	}

	function submit_score() {
		if($this->input->is_ajax_request() && logged_in()) {
			$this->db->where('game', $this->input->post('game'));
			$this->db->where('user_id', $this->session->userdata('id'));
			$scores = $this->db->get('score')->row_array();
			if(empty($scores) OR $scores['score'] < $this->input->post('score')) {
				if(!empty($scores)) {
					$this->db->where('game', $this->input->post('game'));
					$this->db->where('user_id', $this->session->userdata('id'));
					$this->db->delete('score');
				}
				$data = array(
					'user_id' => $this->session->userdata('id'),
					'game' => $this->input->post('game'),
					'score' => $this->input->post('score'),
					'time' => time()
				);
				$this->db->insert('score', $data);
			}
		}
	}

	private function get_scores($game) {
		// Find high scores
		$this->db->where('game', $game);
		$this->db->order_by('score desc, time desc');
		$this->db->limit(10);
		return $this->db->get('score')->result_array();
	}

	private function tidy_scores($game, $scores) {
		// Table tidying loop
		$deleteLimit = 1000000;
		if(!empty($scores)) {
			foreach($scores as $s) {
				if($s['score'] < $deleteLimit) {
					$deleteLimit = $s['score'];
				}
			}
			$this->db->where('game', $game);
			$this->db->where('score <',$deleteLimit);
			$this->db->delete('score');
		}
	}
}

/* End of file game.php */
/* Location: ./application/controllers/game.php */