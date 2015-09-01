<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils {

	function __construct() {
	}

	function redirect($location = null) {
		if(is_null($location)){
			$location = get_last_location();
		}
		else if(strpos($location, 'http') === FALSE) $location = str_replace('https://', 'http://', BASE_URL).$location;
		$CI =& get_instance();
		if($CI->input->is_ajax_request()) {// ajax
			echo json_encode(array('redirect' => $location)); // send some json here eventually
		}
		else { // non ajax
			header('Location: '.$location);
		}
		exit;
	}
}