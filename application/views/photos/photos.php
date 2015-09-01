<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php 
	$this->load->view('photos/validation');?>
<div class="basket-wrapper">
	<div class="width-50 inline-block content-left">
		<h2>Photo Albums</h2>
	</div>
	<div class="width-50 inline-block content-right basket-preview">
		<h2>Loading Basket...</h2>
	</div>
	<div class="spacer" style="clear: both;"></div>
</div>
<?php echo editable_area('photos', 'content/intro', is_admin()); ?>
<p class="padded">
	<a href="<?php echo site_url('photos/orders'); ?>" class="jcr-button"><span class="ui-icon ui-icon-script"></span> View My Orders</a>
<?php if(is_admin()){ ?>
	<a href="<?php echo site_url('photos/placed'); ?>" class="jcr-button"><span class="ui-icon ui-icon-document"></span>View All Orders</a>
	<a href="<?php echo site_url('photos/add'); ?>" class="jcr-button"><span class="ui-icon ui-icon-plus"></span>New Album</a>
<?php } ?>
</p>
<div id="album-view">
<?php 
	foreach($albums as $a){
?>
		<div class="album-container">
			
			<a href="<?php echo site_url('photos/album/'.$a['id']); ?>">
				<div class="thumb" style="background-image:url(<?php echo isset($a['photos'])?$path.$a['photos'][rand(0,sizeof($a['photos'])-1)]['thumb_name']:''; ?>)"></div>
			<a href="<?php echo site_url('photos/album/'.$a['id']); ?>">
				<h3><?php echo $a['name'].' ('.(isset($a['photos'])?sizeof($a['photos']):'0').')'; ?></h3>
			</a>
			
			<p><?php echo date('jS M Y', $a['date']); ?></p>
			
		</div>
<?php
	} 
?>
</div>
