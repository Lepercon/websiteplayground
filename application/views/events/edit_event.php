<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events/view_event/'.$e['id']);
eval(error_code());
?>

<div class="jcr-box">
<h3>Edit Event</h3>
<p>* = required</p>
<?php echo form_open_multipart('events/edit_event/'.$e['id'], array('class' => 'jcr-form no-jsify')); ?>
<?php
	if($upload_errors != ''){
		echo '<div class="validation_errors">'.$upload_errors.'</div>'; 
	}
?>
<ul class="nolist">
	<?php $this->load->view('events/event_form', array('categories' => $categories, 'e' => $e, 'levels' => $levels)); ?>
	<li>
		<label class="narrow-hide"></label>
		<?php echo form_submit('edit_event', 'Save');
		echo token_ip('edit_event');?>
	</li>
</ul>
<?php echo form_close(); ?>
</div>