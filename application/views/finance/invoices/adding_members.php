<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->model('finance_model');
$user_id = $this->session->userdata('id');
$group_id = $this->uri->segment(3);

echo back_link('finance/invoices/my_group/'.$group_id);
?><h2 id="Title">Adding Members</h2><?php

    
    foreach($newmembers as $m){
        $member_id = $this->users_model->get_id_from_name($m);
        echo '<p>Name:<b>'.$m.'</b> id:<b>'.$member_id['id'].'</b> Result:<b>'.($res?'SUCCESS':'FAILED').'</b></p>';
    }
    
?>