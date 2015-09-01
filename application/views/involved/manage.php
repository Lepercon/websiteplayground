<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$token = token_ip('involved_manage_sections');

echo back_link('involved');
if(!empty($error)) echo '<div class="validation_errors">'.$error.'</div>';
eval(success_code());
?>

<div class="jcr-box">
	<?php echo form_open('involved/manage/'.$page, array('name' => 'new', 'class' => 'jcr-form'));?>
		<h3>Add new</h3>
		<ul class="nolist">
			<li>
				<label>Name</label><input class="input-help" title="Under 50 characters, A-Z or 0-9 only" type="text" name="new-section" maxlength="50" placeholder="Section Name" />
			</li>
			<li>
				<label>Mailing list name</label><input class="input-help" title="First part of mailing list email address, eg jb-bc@dur... would be jb-bc" type="text" name="mailing" placeholder="Mailing List Name" />
			</li>
			<li>
				<label>Role Leader</label><select name="associateexec" class="narrow-full"><?php echo build_options($levels);?></select>
			</li>
			<li>
				<label>Membership cost</label><input class="input-help" title="Membership cost for this activity. Can use words or numbers." type="text" name="cost" placeholder="Membership cost" />
			</li>
			<li>
				<label>Meeting schedule</label><input class="input-help" title="Enter the time(s), day(s) and place(s) of meetings, eg Mondays at 8pm in the JCR" type="text" name="schedule" placeholder="Meeting schedule" />
			</li>
			<li>
				<label></label><input type="submit" name="new" value="Create new section" />
				<?php echo $token; ?>
			</li>
		</ul>
	<?php echo form_close(); ?>
</div>

<?php if(!empty($sections)) : ?>
<div class="jcr-box">
	<h3>Edit existing</h3>
	<?php echo form_open('involved/manage/'.$page, array('name' => 'edit', 'class' => 'jcr-form'));?>
		<ul class="nolist">
		<?php
		foreach($sections as $s) : ?>
			<li>
				<input type="radio" name="section" value="<?php echo $s['id']; ?>" /><?php echo ' '.$s['full']; ?>
			</li>
		<?php endforeach; ?>
			<li>
				<input type="submit" name="edit" value="Edit" />
			</li>
			<li>
				<input type="submit" name="delete" value="Delete" />
			</li>
		</ul>
		<?php echo $token; ?>
	<?php echo form_close(); ?>
</div>
<?php endif; ?>