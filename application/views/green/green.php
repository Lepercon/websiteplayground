<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content-left width-66 narrow-full">
	<div class="jcr-box wotw-outer">
			    <h2 class='wotw-day'>The Environment</h2>
				<?php echo editable_area('green', 'content', $access_rights);?>
	</div>
	<?php
	if(!empty($events)) { ?>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Next Green Events</h2>
			<div>
			<?php foreach($events as $e) {
				
				$this->load->view('events/event_info', array('event' => $e));
			} ?>
			</div>
		</div>
	<?php } ?>
	<?php if(logged_in()){ ?>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Meeting Minutes</h2>
			<div>
			<?php
				$this->load->view('utilities/file_browser', array(
					'admin' => $access_rights,
					'section' => 'green',
					'path' => 'green/minutes'
				));
			?>
			</div>
		</div>
	<?php } ?>
</div>
<?php $this->load->view('green/sidebar');?>