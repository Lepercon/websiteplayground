<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events'); ?>

<div class="jcr-box">
<h3>Add Event</h3>
<p>* = required</p>

<?php echo form_open_multipart('events/add_event', array('class' => 'jcr-form no-jsify')); ?>
<?php
	if($upload_errors != ''){
		echo '<div class="validation_errors">'.$upload_errors.'</div>'; 
	}
?>
<ul class="nolist">
	<?php $this->load->view('events/event_form', array('categories' => $categories, 'levels' => $levels)); ?>
	<li>
		<label class="narrow-hide"></label>
		<?php echo form_submit('add_event', 'Save');
		echo form_submit('add_another_event', 'Save and Add Another');
		echo token_ip('add_event'); ?>
	</li>
</ul>
<?php echo form_close(); ?>
</div>