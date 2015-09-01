<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Durham Markets</h3>

<h2>Thank you! Your Durham Markets order has been placed.</h2>
<p>A copy of the confirmation email has been sent to the email address you provided.</p>
<p>Please remember to collect your items at 10am on Friday morning.</p>
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
