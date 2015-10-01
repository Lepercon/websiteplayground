<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
var_dump($_GET);
$confirm_params = array(
  'resource_id'    => $_GET['resource_id'],
  'resource_type'  => $_GET['resource_type'],
  'resource_uri'   => $_GET['resource_uri'],
  'signature'      => $_GET['signature']
);

// State is optional
if (isset($_GET['state'])) {
  $confirm_params['state'] = $_GET['state'];
}

$confirmed_resource = GoCardless::confirm_resource($confirm_params);

var_dump($confirmed_resource);