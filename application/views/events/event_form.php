<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<li>
	<label>Event Name *</label><?php echo form_input(array(
		'name' => 'name',
		'value' => isset($e) ? $e['name'] : (isset($errors) ? set_value('name') : ''),
		'maxlength' => '30',
		'placeholder' => 'Event Name',
		'title' => 'Required Field. Maximum 30 characters. Enter a name for the event.',
		'class' => 'input-help narrow-full',
		'required' => 'required'
	)); ?>
</li>
<li>
	<label>Start Date *</label><?php echo form_input(array(
		'name' => 'date',
		'value' => (isset($e) ? date("d/m/Y", $e['time']) : (isset($errors) ? set_value('date') : $this->input->get('start') === FALSE ? '' : str_replace('-','/',$this->input->get('start')))),
		'placeholder' => 'DD/MM/YYYY',
		'maxlength' => '10',
		'class' => 'datepicker input-help narrow-full',
		'title' => 'Required field. Please select the event date from the dropdown calendar. If no calendar shows then please enable javascript and try again or enter the date in DD/MM/YYYY format.',
		'required' => 'required'
	)); ?>
</li>
<li>
	<label>Start Time *</label><?php $hour = array();
	$minute = array();
	for($i = 0; $i <= 23; $i++) $hour[$i] = sprintf('%02d', $i);
	for($i = 0; $i <= 55; $i+=5) $minute[$i] = sprintf('%02d', $i);
	echo form_dropdown('hour', $hour, (isset($e) ? date("G", $e['time']) : (isset($errors) ? set_value('hour') : '')), 'class="input-help" required="required" title="Select the time of the event. A start time of 00:00 will set an all day event."');
	echo ':'.form_dropdown('minute', $minute, (isset($e) ? date("i", $e['time']) : (isset($errors) ? set_value('minute') : '')), 'class="input-help" required="required" title="Select the time of the event. A start time of 00:00 will set an all day event."'); ?>
</li>
<?php
	if(isset($e)) {
		if(date('H:i', $e['time']) == '00:00') {
			$e['end'] = $e['time'] + 86400;
		} elseif ($e['end'] <= $e['time']) {
			$e['end'] = $e['time'] + 3600;
		}
	}
?>
<li>
	<label>Finish Date</label><?php echo form_input(array(
		'name' => 'end_date',
		'value' => (isset($e) ? date("d/m/Y", $e['end']) : (isset($errors) ? set_value('end_date') : $this->input->get('finish') === FALSE ? '' : str_replace('-','/',$this->input->get('finish')))),
		'placeholder' => 'DD/MM/YYYY',
		'maxlength' => '10',
		'class' => 'datepicker input-help narrow-full',
		'title' => 'Optional field. Please select the event end date from the dropdown calendar. If no calendar shows then please enable javascript and try again or enter the date in DD/MM/YYYY format.'
	)); ?>
</li>
<li>
	<label>Finish Time</label><?php $hour = array();
	$minute = array();
	for($i = 0; $i <= 23; $i++) $hour[$i] = sprintf('%02d', $i);
	for($i = 0; $i <= 55; $i+=5) $minute[$i] = sprintf('%02d', $i);
	echo form_dropdown('end_hour', $hour, (isset($e) ? date("G", $e['end']) : (isset($errors) ? set_value('end_hour') : '')), 'class="input-help" title="Select the end time of the event."');
	echo ':'.form_dropdown('end_minute', $minute, (isset($e) ? date("i", $e['end']) : (isset($errors) ? set_value('end_minute') : '')), 'class="input-help" title="Select the end time of the event."'); ?>
</li>
<li>
	<label>Category *</label><select name="category" class="input-help narrow-full" required="required" title="Select a category for the event.">
		<option></option>
		<?php foreach($categories as $c) echo '<option value="'.$c['id'].'" '.((isset($e) && $e['category'] == $c['id']) ? 'selected="selected"' : set_select('category', $c['id'])).'>'.$c['name'].'</option>'; ?>
	</select><?php if(is_admin()){ ?>
	<a class="jcr-button inline-block" title="Manage Event Categories" href="<?php echo site_url('admin/categories'); ?>">
		<span class="inline-block ui-icon ui-icon-gear"></span>Manage
	</a>
	<?php }?>
</li>
<li>
	<label>Description</label><?php echo form_textarea(array(
		'name' => 'description',
		'value' => isset($e) ? $e['description'] : (isset($errors) ? set_value('description') : ''),
		'rows' => '4',
		'maxlength' => '1000',
		'placeholder' => 'Event Description',
		'title' => 'Optional. Enter a description for the event.',
		'class' => 'input-help narrow-full'
	)); ?>
</li>
<li>
	<label>Location</label><?php echo form_input(array(
		'name' => 'location',
		'value' => isset($e) ? $e['location'] : (isset($errors) ? set_value('location') : ''),
		'maxlength' => '50',
		'placeholder' => 'Event Location',
		'title' => 'Optional. Enter the location of the event.',
		'class' => 'input-help narrow-full'
	)); ?>
</li>
<li>
	<label>Facebook URL</label><?php echo form_input(array(
		'name' => 'facebook_url',
		'value' => isset($e) ? $e['facebook_url'] : (isset($errors) ? set_value('facebook_url') : ''),
		'maxlength' => '100',
		'placeholder' => 'Facebook URL',
		'title' => 'Optional. Give the facebook URL for the event or for the group organising it. Maximum 100 characters.',
		'class' => 'input-help narrow-full'
	)); ?>
</li>
<li>
	<label>Twitter Handle</label><?php echo form_input(array(
		'name' => 'twitter_handle',
		'value' => isset($e) ? $e['twitter_handle'] : (isset($errors) ? set_value('twitter_handle', '@butlerjcr') : ''),
		'maxlength' => '100',
		'placeholder' => 'Twitter Hashtag or Handle',
		'title' => 'Optional. Give the Twitter Hashtag (e.g: #butler'.date('Y').') or Handle (e.g: @butlerjcr) for the event or for the group organising it. Maximum 100 characters.',
		'class' => 'input-help narrow-full'
	)); ?>
</li>
<li>
	<label>Hidden Event</label><?php echo form_checkbox(array(
		'name' => 'hidden',
		'class' => 'input-help narrow-full',
		'value' => '1',
		'checked' => isset($e) ? $e['hidden'] : (isset($errors) ? set_checkbox('hidden', '1', FALSE) : ''),
	)); ?>
</li>
<li>
	<label>Hide Event Poster on Homepage</label><?php echo form_checkbox(array(
		'name' => 'event_poster_hidden',
		'class' => 'input-help narrow-full',
		'value' => '1',
		'checked' => isset($e) ? $e['event_poster_hidden'] : (isset($errors) ? set_checkbox('event_poster_hidden', '1', FALSE) : ''),
	)); ?>
</li>
<li>
	<label>Event Poster</label>
<?php
	if(!isset($e) or is_null($e['event_poster'])){ 
		echo form_upload(array(
			'name' => 'userfile',
		)); 
?>
		<br><label></label><font style="font-size:small;">Posters will be displayed on the homepage at a height of 300 pixels, max file size: 6mb.</font>
<?php
	}else{
?>
		<font style="font-size:small;">There is already a poster for this event, if you would like to upload a new one you'll need to remove the old one.</font>
<?php
	}
?></li>