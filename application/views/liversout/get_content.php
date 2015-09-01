<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('liversout');
?>
<div class="width-33 content-left narrow-full">
<?php $this->load->view('liversout/liversout_contact'); ?>
</div>
<div class="width-66 content-right narrow-full">
<div class="jcr-box">
	<?php echo editable_area('liversout', 'content/'.$page, $access_rights); ?>
</div>
</div>