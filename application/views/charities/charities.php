<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="content-left width-66 narrow-full">
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Charities</h2>
		<div>
			<?php echo editable_area('charities', 'content/charities', $access_rights);?>
			<p><a href="<?php echo site_url('charities/dare_night'); ?>">Submit your Dare Night entry.</a></p>
			<p><a href="<?php echo site_url('charities/orders'); ?>">View and order photos from events</a></p>
		</div>
	</div>
	<?php if(logged_in()){ ?>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Meeting Minutes</h2>
			<div>
			<?php
				$this->load->view('utilities/file_browser', array(
					'admin' => $access_rights,
					'section' => 'charities',
					'path' => 'charities/minutes'
				));
			?>
			</div>
		</div>
	<?php } ?>
</div>
<div class="content-right width-33 narrow-full">
<div class="jcr-box wotw-outer">
	<h2 class="wotw-day">Links</h2>
	<div>
		<p><a href="<?php echo site_url('charities/dare_night'); ?>">Submit your Dare Night entry.</a></p>
		<p><a href="<?php echo site_url('charities/orders'); ?>">View and order photos from events</a></p>
	</div>
</div>
<h2 class="bold-header">Upcoming Charity Events:</h2>
	<?php foreach($events as $e) {
		$this->load->view('events/event_info', array('event' => $e));
	} ?>
<?php
	$this->load->view('charities/charities_intro', array('show_info'=>FALSE));
?>
<?php
	$this->load->view('charities/charities_contact');
?>
</div>