<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events/view_event/'.$e_id);

eval(error_code().success_code()); ?>
<h2>Add a photo</h2>
<p>The photo file size must be less than 8MB in jpg or png format. Please ensure you have the right to upload the photo.</p>
<?php echo form_open_multipart('events/add_photo/'.$e_id, array('class' => 'no-jsify jcr-form')); ?>
<ul class="nolist">
	<li>
		<label>Photo</label><?php echo form_upload('userfile'); ?>
	</li>
	<li>
		<label>Photo Description</label><?php echo form_input(array(
			'name' => 'description',
			'maxlength' => '200',
			'value' => $errors ? set_value('title') : '',
			'placeholder' => 'Photo Description',
			'class' => 'input-help',
			'title' => 'Optional Field. Give a description of the photo. Maximum 200 characters'
		)); ?>
	</li>
	<li>
		<label></label><?php echo form_submit('add_photo', 'Add Photo'); ?>
	</li>
</ul>

<?php
echo token_ip('add_photo');
echo form_close(); ?>
