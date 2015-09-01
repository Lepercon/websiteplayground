<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Durham Markets</h3>

<?php

$this->load->view('markets/nav', array('page_match' => 0));

eval(error_code());

echo form_open('markets/details'); ?>
	<ul class="nolist jcr-form">
		<li>
			<label>Name *</label><?php echo form_input(array(
				'name' => 'name',
				'value' => ($this->session->userdata('market_name') == FALSE ? ($errors != FALSE ? set_value('name') : (logged_in() ? user_pref_name($this->session->userdata('firstname'),$this->session->userdata('prefname'),$this->session->userdata('surname')) : '')) : $this->session->userdata('market_name')),
				'maxlength' => '100',
				'placeholder' => 'Name',
				'title' => 'Required Field. Enter your name.',
				'class' => 'input-help',
				'required' => 'required'
			)); ?>
		</li>
		<li>
			<label>Email *</label><?php echo form_input(array(
				'name' => 'email',
				'value' => ($this->session->userdata('market_email') == FALSE ? ($errors != FALSE ? set_value('email') : (logged_in() ? $this->session->userdata('email') : '')) : $this->session->userdata('market_email')),
				'maxlength' => '100',
				'placeholder' => 'Email',
				'title' => 'Required Field. Enter your email address.',
				'class' => 'input-help',
				'required' => 'required'
			)); ?>
		</li>
		<li>
			<label>Phone *</label><?php echo form_input(array(
				'name' => 'phone',
				'value' => ($this->session->userdata('market_phone') == FALSE ? ($errors != FALSE ? set_value('phone') : (logged_in() ? $this->users_model->get_mobile() : '')): $this->session->userdata('market_phone')),
				'maxlength' => '20',
				'placeholder' => 'Phone',
				'title' => 'Required Field. Enter your phone number.',
				'class' => 'input-help',
				'required' => 'required'
			));
			if(logged_in()) { ?><a href="<?php echo site_url('details/profile');?>" class="jcr-button inline-block" title="Store your mobile number for future visits"><span class="ui-icon ui-icon-person inline-block"></span>Profile</a> <?php } ?>
		</li>
		<li>
			<label>Delivery Day *</label><select name="delivery" required="required">
				<?php foreach(array('Friday') as $day){
					echo '<option value="'.$day.'" '.($errors != FALSE ? set_select('delivery', $day) : ($this->session->userdata('market_delivery') == $day ? 'selected="selected"' : '')).'>'.$day.'</option>';
				}?>
			</select>
		</li>
		<li>
			<label>College *</label><select name="college" required="required">
				<?php foreach(array('Josephine Butler', 'Ustinov') as $coll){
					echo '<option value="'.$coll.'" '.($errors != FALSE ? set_select('college', $coll) : ($this->session->userdata('market_college') == $coll ? 'selected="selected"' : '')).'>'.$coll.'</option>';
				}?>
			</select>
		</li>
		<li>
			<label class="narrow-hide"></label><?php echo form_submit('details', 'Continue'); ?>
		</li>
	</ul>
	<?php echo token_ip('market_order');
echo form_close(); ?>

	</div>
</div>
<div class="content-right width-33 narrow-full">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<?php $this->load->view('utilities/users_contact', array(
			'level_ids'=>array(3),
			'title_before'=>'If you would like more information then contact your ',
			'title_after'=>':',
			'title_level'=>'p'
		)); ?>
	</div>
</div>