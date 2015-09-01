<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('involved');
$token = token_ip('involved_manage_sections');
if(!empty($error)) echo '<div class="validation_errors">'.$error.'</div>';
?>
<div class="jcr-box">
	<h3>Edit section</h3>
	<?php echo form_open('involved/manage/'.$section['page'], array('class' => 'jcr-form')); ?>
		<ul class="nolist">
			<li>
				<label>Name</label><input type="text" class="input-help narrow-full" title="Under 50 characters, A-Z or 0-9 only" name="new-section" value="<?php echo $section['full']; ?>" />
			</li>
			<li>
				<label>Mailing list name</label><input type="text" class="input-help narrow-full" title="First part of mailing list email address, eg jb-bc@dur... would be jb-bc" name="mailing" value="<?php echo $section['mailing']; ?>" />
			</li>
			<li>
				<label>Role Leader</label><select name="associateexec" class="narrow-full"><?php echo build_options($levels, $section['associateexec']);?></select>
			</li>
			<li>
				<label>Membership cost</label><input type="text" class="input-help narrow-full" title="Membership cost for this activity. Can use words or numbers." name="cost" value="<?php echo $section['cost']; ?>" placeholder="Membership cost" />
			</li>
			<li>
				<label>Meeting schedule</label><input type="text" class="input-help narrow-full" title="Enter the time(s), day(s) and place(s) of meetings, eg Mondays at 8pm in the JCR" name="schedule" value="<?php echo $section['schedule']; ?>" placeholder="Meeting schedule" />
			</li>
			<li>
				<label class="narrow-hide"></label><?php echo form_submit('submitsave', 'Save');
				echo form_hidden('save', $section['id']);
				echo $token; ?>
			</li>
		</ul>
	<?php echo form_close(); ?>
</div>
<div class="jcr-box">
<h3>Add teams to this sport</h3>
	<?php echo form_open('involved/manage/'.$section['page'], array('class' => 'jcr-form')); ?>
		<ul class="nolist">
			<li>
				<label>Team Name</label><?php echo form_input(array(
					'name' => 'team_name',
					'value' => '',
					'maxlength' => '200',
					'required' => 'required',
					'class' => 'input-help narrow-full',
					'placeholder' => 'Team Name',
					'title' => 'Required field. Enter the name of the team (e.g: Josephine Butler A)'
				)); ?>
			</li>
			<li>
				<label>Team ID</label><?php echo form_input(array(
					'name' => 'team_id',
					'value' => '',
					'maxlength' => '11',
					'required' => 'required',
					'class' => 'input-help narrow-full',
					'placeholder' => 'Team ID',
					'title' => 'Required field. Enter the team ID as seen in the URL on the team durham website. Enter the numbers after ?team_id='
				)); ?>
			</li><li>
				<label>Competition ID</label><?php echo form_input(array(
					'name' => 'comp_id',
					'value' => '',
					'maxlength' => '11',
					'required' => 'required',
					'class' => 'input-help narrow-full',
					'placeholder' => 'Competition ID',
					'title' => 'Required field. Enter the competition ID as seen in the URL on the team durham website. Enter the numbers after ?comp_id='
				)); ?>
			</li>
			<li>
				<label class="narrow-hide"></label><?php echo form_submit('add_team', 'Add Team');
				echo form_hidden('teams', $section['id']);
				echo $token; ?>
			</li>
		</ul>
	<?php echo form_close(); ?>
</div>
<div class="jcr-box">
	<h3>Teams within this sport</h3>
	<?php if(!empty($teams)) {
		foreach($teams as $t) { ?>
			<div class="block">
				<p>
					<a href="<?php echo site_url('involved/delete_team/'.$t['id']); ?>" class="admin-delete-button no-jsify inline-block jcr-button" title="Delete Team"><span class="ui-icon ui-icon-close"></span></a><?php echo $t['team_name'].' Team ID: '.$t['team_id'].' Competition ID: '.$t['comp_id']; ?>
				</p>
			</div>
		<?php }
	}?>
</div>