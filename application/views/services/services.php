<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Coffee Shop</h3>
		<div>
			 <?php echo editable_area('services', 'content', $access_rights); ?>
		</div>
	</div>
	<?php 
	if(logged_in()){ 
	 ?>
		<div class="jcr-box wotw-outer">
			<h3 class="wotw-day">Anonymous Coffee Shop Requests/Feedback</h3>
				<?php if(isset($_POST['feedback-success'])){?>
					<p>Thank you for your feedback!</p>
				<?php }else{ ?>
				<p>If you have any requests/feedback for the coffee shop, please fill in the box below:</p>
				<?php 
					echo form_open('', 'class="jcr-form"');
					
					echo form_textarea(array('name'=>'feedback-requests', 'placeholder'=>'Requests/Feedback', 'style'=>'max-width:99%;width:99%;min-width:99%;'));
					echo form_submit('submit', 'Submit Feedback');
					echo form_close();
				}
				?>
		</div>
	<?php
	} ?>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Staff</h3>
		<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(131,148))); ?>
	</div>
</div>
<div class="content-right width-33 narrow-full">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<p>If you would like to know more information then get in contact:</p>
		<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(17))); ?>
	</div>
</div>