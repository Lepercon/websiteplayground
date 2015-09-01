<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Josephine Butler College JCR</h3>
		<div>
			 <?php echo editable_area('jcr', 'content', $access_rights); ?>
		</div>
	</div>
	<?php 
	if(logged_in()){ 
	 ?>
		<div class="jcr-box wotw-outer" id="feedback">
			<h3 class="wotw-day">Anonymous Requests/Feedback</h3>
			<div>
				<?php if(isset($_POST['feedback-success'])){?>
					<p>Thank you for your feedback!</p>
				<?php }else{ ?>
				<p>If you have any requests/feedback for the the JCR, please fill in the box below, we will take time to review any feedback you may have:</p>
				<?php 
					echo form_open('', 'class="jcr-form"');
					
					echo form_textarea(array('name'=>'feedback-requests', 'placeholder'=>'Requests/Feedback', 'style'=>'max-width:99%;width:99%;min-width:99%;'));
					echo form_submit('submit', 'Submit Feedback');
					echo form_close();
				}
				?>
			</div>
		</div>
	<?php
	} ?>
</div>
<div class="width-33 narrow-full content-right">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<p>If you would like more help or information then please get in contact:</p>
	<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(2,3,4,18,117))); ?>
	</div>
</div>
