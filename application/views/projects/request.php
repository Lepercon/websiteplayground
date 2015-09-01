<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$token = token_ip('projects');
echo back_link('projects');

eval(error_code()); ?>

<div class="jcr-box">
	<?php echo '<h2>'.$request['title'].'</h2>';
	echo '<p>Request added by '.$this->users_model->get_full_name($request['request_by']).' on '.date('l jS F Y \a\t G:i',$request['request_time']).'.</p>';
	echo '<p>'.$request['description'].'</p>';

	echo form_open('projects/view_request/'.$request['id'], array('class' => 'jcr-form')); ?>
		<ul class="nolist">
			<li>
				<label>Project Category</label><?php echo '<select name="category" class="input-help" title="Required Field. Select the category for this project. If this is changed, an email will be sent to the new category leader to notify them."><option></option>';
				foreach($categories as $c) echo '<option value="'.$c['id'].'" '.($request['category'] == $c['id'] ? 'selected="selected"' : set_select('category', $c['id'])).'>'.$c['name'].'</option>';
				echo '</select>';
				echo $token;
				echo form_hidden('old_category', $request['category']);
				echo form_submit('change_category', 'Change Category'); ?>
			</li>
		</ul>
	<?php echo form_close();

	echo form_open('projects/view_request/'.$request['id'], array('class' => 'jcr-form'));
	$dropdown = array(
		'requested' => '1. Request Made',
		'started' => '2. Work Started',
		'completed' => '3. Completed'
	); ?>
	<ul class="nolist">
		<li>
			<label>Project Progress</label><?php echo form_dropdown('progress', $dropdown, $request['progress'], 'class="input-help" title="Required Field. Select the progress of work on this project."');
			echo $token;
			echo form_submit('update_progress', 'Update Progress'); ?>
		</li>
	</ul>
	<?php echo form_close();
	if($event != FALSE) {
		echo '<p>This project is associated with the following event. '.anchor('events/view_event/'.$request['event'], 'Click here for all information relating to the event').'.</p>';
		$this->load->view('events/event_info', array('event' => $event));
	} ?>
</div>

<div class="jcr-box">
	<h2>Files and Photos</h2>
	<?php if(!empty($files)) {
		echo '<h3>Uploaded Files</h3>';
		foreach($files as $f) {
			$filetype = end(explode(".", strtolower($f['filename'])));
			if(!in_array($filetype, array('doc', 'docx', 'jpg', 'mp3', 'pdf', 'png', 'ppt', 'pptx', 'txt', 'xls', 'xlsx'), TRUE)) $filetype = 'default';
			echo '<h3><a target="_blank" class="no-jsify" href="'.VIEW_URL.'projects/files/project_'.$request['id'].'/'.$f['filename'].'"><img src="'.VIEW_URL.'common/editable/icons/doc_icons/'.$filetype.'.png" alt="'.$filetype.' file">Download '.$f['filename'].'</a></h3>';
			echo '<p>File added by '.$this->users_model->get_full_name($f['submitted_by']).' on '.date('l jS F Y \a\t G:i',$f['submitted_time']).'. '.$f['description'].' '.((is_admin() or $f['submitted_by'] == $this->session->userdata('id')) ? anchor('projects/delete_file/'.$f['id'], 'Delete this file') : '').'</p>';
			echo '<hr />';
		}
	}?>
	<h3>Upload a New File</h3>
	<?php echo form_open_multipart('projects/add_file/'.$request['id'], array('class' => 'no-jsify jcr-form')); ?>
		<ul class="nolist">
			<li>
				<label>File or photo to Upload</label><?php echo form_upload('userfile'); ?>
			</li>
			<li>
				<label>File description</label><?php echo form_textarea(array(
					'name' => 'description',
					'value' => ($errors ? set_value('description') : ''),
					'rows' => '6',
					'placeholder' => 'File Description',
					'title' => 'Required Field when uploading a file or photo. Give a description of the file or photo and the reason for uploading it',
					'class' => 'input-help'
				)); ?>
			</li>
			<li>
				<label></label><?php echo $token;
				echo form_submit('add_file', 'Add File'); ?>
			</li>
		</ul>
	<?php echo form_close();?>
</div>

<div class="jcr-box">
	<h2>Comments</h2>
	<?php foreach($comments as $c) {
		echo '<h3>'.((is_admin() or $c['submitted_by'] == $this->session->userdata('id')) ? '<a href="'.site_url('projects/delete_comment/'.$c['id']).'" class="jcr-button inline-block no-jsify admin-delete-button" title="Delete comment"><span class="ui-icon ui-icon-close"></span></a>' : '').'On '.date('l jS F Y \a\t G:i',$c['submitted_time']).', '.$this->users_model->get_full_name($c['submitted_by']).' commented:</h3>';
		echo '<p>'.nl2br($c['comment']).'</p>';
		echo '<hr />';
	}?>
	<h3>Add a New Comment</h3>
	<?php echo form_open('projects/add_comment/'.$request['id'], array('class' => 'jcr-form')); ?>
		<ul class="nolist">
			<li>
				<label>Comment</label><?php echo form_textarea(array(
					'name' => 'comment',
					'value' => ($errors ? set_value('comment') : ''),
					'rows' => '6',
					'placeholder' => 'Comment',
					'title' => 'Add a comment for the project',
					'class' => 'input-help'
				)); ?>
			</li>
			<li>
				<label></label><?php echo $token;
				echo form_submit('add_comment', 'Add Comment'); ?>
			</li>
		</ul>
	<?php echo form_close();?>
</div>