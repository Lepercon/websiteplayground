<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<h2 style="margin-top:0;">Delivery Notifcations</h2>
<?php
	$CI =& get_instance();
	$CI->load->model('welfare_model');
	$notifications = $CI->welfare_model->get_notifications();
	
	if($notifications->num_rows() < 1) {
		echo 'There are no pending requests that require a delivery notification';
	}
	
	foreach ($notifications->result() as $row)
	{
	   echo '<div class="notification"><p>'.$row->anonymous_code.'</p>';	  
	   echo form_open('welfare/send_notification', array('class' => 'jcr-form'));
	   echo form_hidden('anonymouscode', $row->anonymous_code);
	   echo form_submit('send', 'Send Notification');
	   echo '</div>';
	   echo form_close();
	}
?>
