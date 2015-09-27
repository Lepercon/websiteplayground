<?php

class Post_controller {

	function __construct() {

	}

	function index() {
		$ci =& get_instance();
		if(isset($GLOBALS['controller_json'])) {
			$ci->output->set_output($GLOBALS['controller_json']);
		}
        $seg = $ci->uri->segment(2);
        if($seg == 'getcal' || $seg == 'getallcal'){
            $seg = TRUE;
        }
		if(!($ci->input->is_ajax_request() || ($ci->input->post('request')==='cron') || ($seg === TRUE))) { // not ajax
			
			if($ci->input->cookie('cookiepopup') == FALSE){
				$ci->load->view('common/cookie_popup');
			}
		
			$ci->load->view('common/foot', array('big_title' => (isset($ci->page_details['big_title']) ? $ci->page_details['big_title'] : 'HOME')));
			if(ENVIRONMENT === 'development') {
				$ci->output->enable_profiler(TRUE);
			}
		}
		// The controller may want to provide its own json in response to specific ajax requests.
		// This tests for that.  If it doesn't, encode as normal.  If it does, just echo what it has output.
		elseif($seg !== TRUE) { // ajax
			// get response data from global var
			$response_data = $GLOBALS['js_response_data'];
			$response_data['html'] = $GLOBALS['OUT']->get_output();
			$ci->output->set_output(json_encode($response_data));
		}

		if(!in_array($ci->router->fetch_class(), array('login')) && empty($GLOBALS['controller_json']) && !in_array($ci->uri->rsegment(2), array('cookie_prompt'))) {
			$ci->session->set_userdata('last_location', $ci->uri->uri_string());
		}
		
		$time = time(); 
        $u_id = $ci->session->userdata('id');
        $data = array();
        foreach ($ci->db->queries as $key => $query) { 
        	if(strpos($query, 'SELECT') === FALSE){
	            $data[$key] = array(
	            	'user_id' => $u_id,
	            	'query' => $query,
	            	'time' => $time
	            );
            }
        }
		$ci->db->insert_batch('database', $data);
	}
}

/* End of file post_controller.php */
/* Location: ./application/hooks/post_controller.php */