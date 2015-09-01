<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('photos/album/'.$album['id']);?>

<h3><?php echo $album['name'].' ('.(isset($album['photos'])?sizeof($album['photos']):0).') - '.($published?'Edit Album':'Unpublished Photos'); ?></h3>
<p><?php echo $album['description']; ?></p>
<p><i><?php echo date('jS M Y', $album['date']); ?></i></p>

<?php
	if($published){
		//var_dump($_POST);
		echo form_open('', 'class="jcr-form"');
		
		echo '<p>'.form_label('Album Name:', 'album-name').form_input(array(
			'name'=>'album-name', 
			'placeholder'=>'Album Name', 
			'value'=>$album['name']
		)).'</p>';
		
		echo '<p>'.form_label('Date:', 'date').form_input(array(
			'name'=>'date', 
			'placeholder'=>date('d/m/Y'), 
			'class'=>'datepicker', 
			'value'=>date('d/m/Y',$album['date'])
		)).'</p>';
		
		echo '<p><label for="album-desc" style="vertical-align: top!important;">Album Description:</label>'.form_textarea(array(
			'name'=>'album-desc', 
			'placeholder'=>'Album Description', 
			'value'=>$album['description']
		)).'</p>';
		echo form_hidden(array('a_id'=>$album['id']));
		echo form_label().form_submit('update', 'Update Album');
		echo form_close();
	}
?>

<?php if(!$published){ 
	echo form_open('photos/album/'.$album['id'], 'class="jcr-form"');
	echo form_submit('publish', 'Publish Photos');
	echo form_close();
	echo form_open('photos/album/'.$album['id'], 'class="jcr-form"');
	echo form_submit('delete-unpublished', 'Delete Photos');
	echo form_close();
} ?>

<h2>Edit Photos</h2>
<div id="photo-view">
<?php 
	foreach($album['photos'] as $p){ 
?>
		<div class="photo-container">
			<a href="<?php echo site_url('photos/photo/'.$p['id']); ?>" class="photo-link no-jsify">
				<div class="thumb photo-thumb" image="<?php echo $url.$p['photo_name']; ?>" style="background-image:url(<?php echo $url.$p['thumb_name']; ?>)"></div>
			</a>
			<div class="controlls">
				<a href="#" class="edit-rotate no-jsify alt"><span class="ui-icon ui-icon-arrowreturnthick-1-w flip-icon"></span></a>&nbsp;
				<a href="#" class="edit-rotate no-jsify"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span></a>&nbsp;
				<a href="#" class="edit-delete no-jsify"><span class="ui-icon ui-icon-trash"></span></a>
				<span class="photo-id"><?php echo $p['id']; ?></span>
			</div>
		</div>
<?php
	}
?>
</div>

<?php if(!$published){ 
	echo form_open('photos/album/'.$album['id'], 'class="jcr-form"');
	echo form_submit('publish', 'Publish Photos');
	echo form_close();
	echo form_open('photos/album/'.$album['id'], 'class="jcr-form"');
	echo form_submit('delete-unpublished', 'Delete Photos');
	echo form_close();
}else{ ?>
	<a href="<?php echo site_url('photos/album/'.$album['id']); ?>" class="jcr-button">Done</a>
<?php } ?>


<span id="album-name"><?php echo $album['name']; ?></span>
<span id="album-id"><?php echo $album['id']; ?></span>
<span id="spinner-link"><?php echo VIEW_URL.'photos/spinner.gif'; ?></span>