<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<div class="content-left width-66 narrow-full">
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Welfare at Josephine Butler College</h2>
		<div>
		<?php 
			$this->load->view('welfare/get_content', array(
				'access_rights' => $access_rights,
				'section' => $section
			));
				
			if(!empty($events)) { ?>
				<h2 class="bold-header">Next Welfare Events</h2>
				<?php foreach($events as $event) {
					$this->load->view('events/event_info', array('event' => $event));
				}
			} ?>
		</div>
	</div>
	<div class="jcr-box wotw-outer inline-block" style="width:99%;">
		<h3 class="wotw-day">Anonymous Welfare Requests/Feedback</h3>
		<div>
			<?php if(isset($_POST['feedback-success'])){?>
				<p>Thank you for your feedback!</p>
			<?php }else{ ?>
			<p>If you have any requests/feedback for the welfare team, please fill in the box below. If you would like to contact the welfare officer for any other reason please use the link on the right hand side of this page.</p>
			<?php 
				echo form_open('', 'class="jcr-form"');
				
				echo form_textarea(array('name'=>'feedback-requests', 'placeholder'=>'Requests/Feedback', 'style'=>'max-width:99%;width:99%;min-width:99%;'));
				echo form_submit('submit', 'Submit Feedback');
				echo form_close();
			}
			?>
		</div>
	</div>
</div>
<div class="content-right width-33 narrow-full">
	<div class="jcr-box wotw-outer navigation-pane">
		<h2 class="wotw-day">Infomation</h2>
		<div>
			<?php echo anchor('welfare', '<h2 '.($section=='intro'?'class="selected"':'').'>Introduction</h2>', 'class="page-link"'); ?>
			<p>An introduction from your welfare team</p>
			
			<?php echo anchor('welfare/index/supplies', '<h2 '.($section=='supplies'?'class="selected"':'').'>Free supplies form</h2>', 'class="page-link"'); ?>
			<p>Use this form to anonymously request welfare resources</p>
		
			<?php echo anchor('contact/user/welfare', '<h2>Contact welfare</h2>'); ?>
			<p>Use the contact form to email the Welfare Officer</p>
		
			<?php echo anchor('welfare/index/drop_in', '<h2 '.($section=='drop_in'?'class="selected"':'').'>Pop-Ins</h2>', 'class="page-link"'); ?>
			<p>Welfare events this week, including pop-in hour times, locations and staffing</p>
		
    		<?php echo anchor('welfare/index/campaigns', '<h2 '.($section=='campaigns'?'class="selected"':'').'>Campaigns</h2>', 'class="page-link"'); ?>
			<p>Information about our Welfare Campaigns.</p>

    		<?php echo anchor('welfare/index/university', '<h2 '.($section=='university'?'class="selected"':'').'>University services</h2>', 'class="page-link"'); ?>
			<p>University welfare services.</p>
			
			<?php echo anchor('welfare/index/town', '<h2 '.($section=='town'?'class="selected"':'').'>Supplies In Town</h2>', 'class="page-link"'); ?>
			<p>Welfare services in and around town.</p>

			<?php echo anchor('welfare/index/minutes', '<h2 '.($section=='minutes'?'class="selected"':'').'>Meeting Minutes</h2>', 'class="page-link"'); ?>
			<p>Minutes from Welfare Comm Meetings.</p>
		</div>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<p>If you would like more help or information then please get in contact:</p>
		<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(11,12))); ?>
	</div>
</div>