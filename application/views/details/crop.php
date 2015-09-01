<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$token = token_ip('details_crop');

echo back_link('details');
?>

<?php if(isset($error)) echo '<h1>'.$error.'</h1>'; ?>

<h2>Crop your photo!</h2>
<div class="inline-block" id="crop-large">
	<h3>Large</h3>
	<p>This is your full sized profile image</p>
	<img alt="Large profile picture. If you can see this text instead of an image, then please contact the administrator." height="<?php echo $dims['height']; ?>" width="<?php echo $dims['width']; ?>" id="de-photo-large" src="<?php echo VIEW_URL; ?>details/img/tmp/<?php echo isset($uid)?$uid:$this->session->userdata('uid'); ?>.jpg" />
</div>
<div class="inline-block">
	<h3>Small</h3>
	<p>This is more of a 'profile icon'</p>
	<img alt="Small profile icon. If you can see this text instead of an image, then please contact the administrator." height="<?php echo $dims['height']; ?>" width="<?php echo $dims['width']; ?>" id="de-photo-small" src="<?php echo VIEW_URL; ?>details/img/tmp/<?php echo isset($uid)?$uid:$this->session->userdata('uid'); ?>.jpg" />
</div>
<?php echo form_open(isset($url)?$url:'details/crop', array('id' => 'de-crop', 'class' => 'no-jsify jcr-form'));
	echo form_submit('crop', 'Crop');
	echo $token;
	if(isset($u_id)){
		echo form_hidden(array('u_id'=>$u_id));
	}
echo form_close(); ?>