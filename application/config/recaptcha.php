<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
The reCaptcha server keys and API locations

Obtain your own keys from:
http://www.recaptcha.net
*/
$config['recaptcha'] = array(
  'public' => '6Ld_Fb0SAAAAAESLGizNTQWmMEKhCyPp7bl1v6Jf',
  'private' => '6Ld_Fb0SAAAAAChfcntc7JTM7-QkuPh-Nhg3NW3a',
  'RECAPTCHA_API_SERVER' => 'http://api.recaptcha.net',
  'RECAPTCHA_API_SECURE_SERVER' => 'https://api-secure.recaptcha.net',
  'RECAPTCHA_VERIFY_SERVER' =>'api-verify.recaptcha.net',
  'RECAPTCHA_SIGNUP_URL' => 'https://www.google.com/recaptcha/admin/create',
  'theme' => 'white'
);
