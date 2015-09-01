<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="width-33 content-left narrow-full inline-block">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Elections</h3>
		<div>
			<?php foreach($sections as $k => $s){ ?>
				<p><?php echo anchor('voting/index/'.$k, $s, $section==$k?'class="selected"':''); ?></p>
			<?php } ?>
			<p><a href="<?php echo site_url('archive/index/standing_orders'); ?>">Archive: Standing Orders</a></p>
			<p><a class="no-jsify" href="https://www.dur.ac.uk/student.elections/butler/">Vote in Butler JCR Elections</a></p>
			<p><a class="no-jsify" href="https://www.dur.ac.uk/student.elections/dsu/">Vote in DSU Elections</a></p>
		</div>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<p>If you would like more help or information then please get in contact:</p>
	<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(18,117))); ?>
	</div>

</div>
<div class="width-66 content-right narrow-full inline-block">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day"><?php echo $sections[$section]; ?></h3>
		<div>
			<?php echo editable_area('voting', 'content/'.$section, $access_rights); ?>
		</div>
	</div>
    <div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Contact</h3>
		<div>
			If you have any questions about elections at Josephine Butler College JCR, please contact <?php echo $contact; ?>.
		</div>
	</div>
</div>