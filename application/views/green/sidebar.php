<div class="content-right width-33 narrow-full">
	<div class="jcr-box narrow-hide wotw-outer">
		<h3 class="wotw-day">Durham Market Deliveries</h3>
		<p>Order a delivery from Durham Markets to college.</p>
		<a href="<?php echo site_url('markets'); ?>" class="jcr-button inline-block" title="Order a delivery from Durham Markets to college">
			<span class="ui-icon ui-icon-cart inline-block"></span>Order
		</a>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Green Tip of the Day</h3>
		<p class="green"><?php echo $green_tip; ?></p>
		<?php if(is_admin() or has_level('Green Committee Rep')) { ?>
			<a href="<?php echo site_url('green/manage_tips'); ?>" class="jcr-button inline-block" title="Manage green tips">
				<span class="ui-icon ui-icon-gear inline-block"></span>Manage
			</a>
		<?php } ?>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Butler Bikes</h3>
		<p>Need a quicker, environmentally friendly way to get around the city? Why not sign up</p>
		<p>Find out more about Butler Bikes <a href="<?php echo site_url('green/butler_bikes'); ?>">here</a>.</p>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Come Along to Green Comm</h3>
		<p>Meetings are Thursday at 7pm in the JCR Lounge.</p>
		<p>Join the <a href="https://www.facebook.com/groups/432457273455632/">Facebook</a> to stay in the loop.</p>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Contact Porters</h3>
		<p>Notice a draught? Radiators not working or on when they needn't be?</p>
		<a href="http://www.dur.ac.uk/butler.college/local/current/housekeeping/" class="jcr-button inline-block" title="Report a problem to college porters">
			<span class="ui-icon ui-icon-mail-closed inline-block"></span>Contact
		</a>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Contact an Environment Rep</h3>
		<p>Any questions or suggestions about how we can make the college greener? Get in contact:</p>
		<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(58,163))); ?>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Winners of the Green Tourism Gold Award</h3>
		<img src="<?php echo VIEW_URL.'green/img/green_tourism_gold_award.png'?>" width="200px" height="261px" alt="Green Tourism Gold Award Logo"/>
	</div>
</div>