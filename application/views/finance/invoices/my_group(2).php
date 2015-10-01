<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->model('finance_model');
$page_admin = ($this->finance_model->finance_permissions());?>

<h1>Finances</h1>
<h3>Invoicing System</h3>
<?php

    var_dump($group);
    var_dump($this_group);
    var_dump($members);

?>
