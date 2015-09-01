<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

// CUSTOM
if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) $server_port = ':'.$_SERVER['SERVER_PORT'];
else $server_port = '';

$script_loc = dirname($_SERVER['SCRIPT_NAME']);
if($script_loc == '/') $script_loc = '';

define('HTTPS', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'));

define('BASE_URL', str_replace('\\', '/', 'http'.(HTTPS ? 's' : '').'://'.$_SERVER['SERVER_NAME'].$server_port.$script_loc.($server_port != '' ? '' : '/'))); // eg http://localhost:8888/
define('VIEW_PATH', BASE_PATH.'application/views/');// eg /home/nick/documents...../v1/application/views
define('VIEW_URL', BASE_URL.'application/views/'); // eg http://localhost:8888/application/views/

define('JCR_EMAIL', 'butler.jcr@durham.ac.uk');
define('WELFARE_EMAIL', 'butler.welfare@durham.ac.uk');

define('VERSION', '24062014');

/* End of file constants.php */
/* Location: ./application/config/constants.php */