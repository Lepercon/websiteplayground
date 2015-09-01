<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="in-nav" class="gym narrow-hide">
<ul class="nolist"><?php foreach($this->inv_pages as $p) echo '<li id="in-'.$p.'">'.anchor('involved/index/'.$p, ucfirst($p)).'</li>'; ?></ul>
</div>

<div class="content-left width-33 narrow-full">
	<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FHowlandsGym&amp;width=292&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:590px;" allowTransparency="true"></iframe>
</div>
<div id="gym_buddies" class="content-right width-66 narrow-full">
<?php if(logged_in()) { ?>
	<div id="accordion">
		<h3><a href="#">Howlands Gym</a></h3>
			<div>
				<p>Tell others when you can meet to use the gym located in the Howlands building. Join the Howlands Gym Facebook group and check-in when you're there.</p>
				<a href="<?php echo site_url('details/profile'); ?>" class="jcr-button inline-block" title="Add details of when you're free to use the Howlands gym">
					<span class="ui-icon ui-icon-plus inline-block"></span>Manage details
				</a>
			</div>
		<?php if(!empty($buddies)) :
			foreach($buddies as $b):
				echo '<h3><a href="#">Gym User: '.user_pref_name($b['firstname'],$b['prefname'],$b['surname']).'</a></h3>';?>
				<div>
					<div class="inline-block">
						<img src="<?php echo get_usr_img_src($b['uid'], 'tiny'); ?>" alt="Your Profile Photo" />
						<p><b>Mobile: </b><?php echo substr($b['mobile'],0,5).' '.substr($b['mobile'],5);?></p>
						<p><b>Available: </b><?php echo nl2br(preg_replace('/<br[\s]*\/?>/i','',$b['availability']));?></p>
						<?php if($b['id'] == $this->session->userdata('id')) { ?>
							<a href="<?php echo site_url('details/profile'); ?>" class="jcr-button inline-block" title="Edit your details">
								<span class="ui-icon ui-icon-pencil inline-block"></span>Edit
							</a>
						<?php } else { ?>
							<a href="<?php echo site_url('contact/user/'.$b['id']); ?>" class="jcr-button inline-block" title="Send an email">
								<span class="ui-icon ui-icon-mail-closed inline-block"></span>Contact
							</a>
						<?php } ?>
						<?php if(is_admin() OR ($b['id'] == $this->session->userdata('id'))) { ?>
							<a href="<?php echo site_url('involved/remove/'.$b['id']); ?>" class="jcr-button inline-block" title="Remove details">
								<span class="ui-icon ui-icon-trash inline-block"></span>Remove
							</a>
						<?php } ?>

					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
<?php } else { ?>
	<p>The gym is located in the Howlands Building and is shared between Josephine Butler and Ustinov Colleges. Login to find a gym buddy and let others know what times you would like to use the gym. You can also join the Howlands Gym Facebook Group and check-in when you're there.</p>
<?php } ?>
	<?php 
		$this->load->view('utilities/users_contact', array(
			'level_ids'=>array(62),
			'title_level'=>'h2',
			'title_before'=>'Your ',
			'title_after'=>':'
		)); 
	?>
</div>
