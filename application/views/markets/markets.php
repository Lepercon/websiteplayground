<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="width-66 narrow-full content-left">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Durham Markets</h3>
		<?php
		if(is_admin()) { ?>
			<a href="<?php echo site_url('markets/manage'); ?>" class="jcr-button inline-block" title="Admin: Manage list of available items">
				<span class="ui-icon ui-icon-gear inline-block"></span>Manage
			</a>
		<?php } ?>
		
		<?php echo editable_area('markets', 'content', $access_rights); ?>
		<a href="<?php echo site_url('markets/details'); ?>" class="jcr-button inline-block" title="Begin your Durham markets order by entering your details">
			<span class="ui-icon ui-icon-arrowthick-1-e inline-block"></span>Enter Details
		</a>	
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