<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$common = array(
	'css' => array('common/common.min:v='.VERSION, 'common/font', 'common/snow.min'),

	'js' => array(
		'first' => array('jquery', 'history', 'jquery_ui'),
		'last' => array('common/common.min:v='.VERSION, 'common/heading')
	)
);

if(isset($_GET['altcss'])){
	$common['css'][0] = 'common/alternative.min';
}

$js_urls = array(
	'jquery' => (ENVIRONMENT == 'development' ? VIEW_URL.'common/local_cdn_resources/jquery.min.js' : 'http'.(HTTPS ? 's' : '').'://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'),
	'jquery_ui' => (ENVIRONMENT == 'development' ? VIEW_URL.'common/local_cdn_resources/jquery-ui.min.js' : 'http'.(HTTPS ? 's' : '').'://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'),
	'history' => VIEW_URL.'common/jquery.history.js?v='.VERSION,
	'jcrop' => VIEW_URL.'common/jcrop/jcrop.js?v=99'
);

$css_urls = array(
	'jcrop' => VIEW_URL.'common/jcrop/jcrop.css'
);