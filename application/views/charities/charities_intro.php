<?php if(!isset($show_info) or $show_info){ ?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Information</h2>
		<div>
			<?php
				echo editable_area('charities', 'content/small_intro', $this->charities_model->charities_permissions());
			?>
		</div>
	</div>
<?php } ?>
<div class="jcr-box wotw-outer">
	<h2 class="wotw-day">Charity Comm. Meetings</h2>
	<div>
		<p>Charity Comm. Meetings run every Wednesday at 5pm, we would love you to come along!</p>
		<p>If you want the most up to date info about the meetings then please join our <a href="http://www.facebook.com/groups/315617568483945/">facebook group</a> and <a href="<?php
			$contact = $this->users_model->get_users_with_level(3, 'users.id,users.email');
			if(logged_in()){
				echo site_url('contact/'.$contact[0]['id']);
			}else{
				echo 'mailto:'.$contact[0]['email'];
			}
	?>">request</a> to be added to our mailing list.</p>
	</div>
</div>

