<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(in_array($section, array('supplies', 'minutes', 'notifications'))){
	$this->load->view('welfare/'.$section);
}else{
	echo editable_area('welfare', 'content/'.$section, $access_rights);
}?>