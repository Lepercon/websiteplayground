<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="content-left width-66 narrow-full">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">International</h3>
		<div>
			<?php echo editable_area('international', 'content', $access_rights); ?>
		</div>
	</div>
</div>
<div class="content-right width-33 narrow-full">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<p>If you would like to know more information then get in contact:</p>
		<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(137))); ?>
	</div>
</div>