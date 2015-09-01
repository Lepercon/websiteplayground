<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$token = token_ip('details');
eval(success_code().error_code());?>

<div class="content-left width-66 narrow-full">
	<div class="jcr-box">
		<h2>Your Details</h2>
		<ul class="nolist">
		<?php foreach(array(
			'firstname' => 'First Name',
			'surname' => 'Surname',
			'email' => 'Email Address'
		) as $k => $v) echo '<li><p><b>'.$v.':</b> '.$this->session->userdata($k).'</p></li>';?>
		<li><p>If any of these are incorrect, please <?php echo contact_wm(); ?>.</p></li>
		</ul>
	</div>
	<div class="jcr-box">
		<h2>Remember My Login</h2>
		<?php if($this->session->userdata('rand_exp') == '1') {
			$remember = TRUE;
		} else {
			$remember = FALSE;
		} ?>
			<ul class="nolist">
				<li><p>After closing the JCR website, your device will <?php echo ($remember ? 'remember' : 'forget'); ?> you.</p></li>
				<li><p>To make your device <?php echo ($remember ? 'forget' : 'remember'); ?> your JCR account details on your next visit, <?php echo anchor('details/change_remember', 'click here')?></p></li>
			</ul>
	</div>
	<div class="jcr-box">
		<h2>Your Information</h2>
		<?php echo form_open('details/profile', array('class' => 'jcr-form no-jsify')); ?>
			<ul class="nolist">
				<li>
					<p>Preferred First Name</p>
				</li>
				<li>
					<?php echo form_input(array(
						'name' => 'prefname',
						'value' => set_value('prefname', $this->session->userdata('prefname')),
						'placeholder' => 'Preferred First Name',
						'maxlength' => '50',
						'class' => 'input-help',
						'title' => 'Enter the firstname you would prefer to see used on the JCR website'
					)); ?>
				</li>
				<!--<li>
					<p>Mobile No.</p>
				</li>
				<li>
					<?php echo form_input(array(
						'name' => 'mobile',
						'value' => set_value('mobile', $this->users_model->get_mobile()),
						'placeholder' => 'Mobile No.',
						'maxlength' => '15',
						'class' => 'input-help',
						'title' => 'Enter your mobile number to have it automatically entered on the market order form. Entering gym availability below will also mean your mobile number is included on the gym buddy system.'
					)); ?>
				</li>_-->
				<?php 
					if(has_level('any')) {
				?>
						<li>
							<p>Description of you:</p>
						</li>
						<li>
							<?php echo form_textarea(array(
								'name' => 'level_desc',
								'rows' => '8',
								'value' => db_to_textarea(set_value('level_desc', $this->users_model->get_level_desc())),
								'placeholder' => 'Describe yourself...',
								'class' => 'input-help',
								'title' => 'Give a description of yourself. This will be publicly visible next to your profile picture in the Who\'s Who section',
							)); ?>
						</li>
						<li>
							<h3>Describe your roles:</h3>
							<p style="font-size:12px;"><i>Note: This description is shared with everybody in this role, so don't make it too specific to you.</i></p>
						</li>
				<?php 
					}
					$levels = $this->users_model->get_user_levels($this->session->userdata('id'));
					foreach($levels as $l){ 
				?>
						<li>
							<p>Description of <b><?php echo $l['full']; ?></b>:</p>
						</li>
						<li>
							<?php echo form_textarea(array(
								'name' => 'level_'.$l['level_id'],
								'rows' => '8',
								'value' => db_to_textarea($l['description']),
								'placeholder' => 'The '.$l['full'].' is responsible for...',
								'class' => 'input-help',
								'title' => 'Give a description of '.$l['full'].'. This will be publicly visible next to your profile picture in the Who\'s Who section',
								'class' => 'level-desc'
							)); ?>
						</li>
				<?php } ?>
				<!--<li>
					<p>Gym Availability for the Gym Buddies System</p>
				</li>
				<li>
					<?php echo form_textarea(array(
						'name' => 'availability',
						'rows' => '8',
						'value' => db_to_textarea(set_value('availability', $this->users_model->get_gym_availability())),
						'title' => 'If you use the gym in the Howlands, tell others when you are able to meet to use it.',
						'class' => 'input-help',
						'placeholder' => 'Gym Availability'
					)); ?>
				</li>-->
				<li>
					<?php echo form_submit(array('value' => 'Save'));?>
				</li>
			</ul>
			<?php echo form_hidden('change_details', 'optional_information');
			echo $token;
		echo form_close(); ?>
	</div>
</div>
<div class="content-right width-33 narrow-full">
	<div class="jcr-box center">
		<h2>Profile Picture</h2>
		<?php echo form_open_multipart('details/profile', array('class' => 'no-jsify jcr-form')); ?>
			<ul class="nolist">
				<li>
					<?php echo '<img src="'.get_usr_img_src($this->session->userdata('uid'),'large').'?'.rand().'" />'; ?>
				</li>
				<li>
					<?php echo form_upload('userfile'); ?>
				</li>
				<li>
					<p>Choose a new profile photo; at least 200 pixels wide and high but less than 8MB, JPG or PNG</p>
				</li>
				<li>
					<?php echo form_submit(array('value' => 'Upload')); ?>
				</li>
			</ul>
			<?php echo form_hidden('change_details', 'crop_photo');
			echo $token;
		echo form_close(); ?>
	</div>
</div>