<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('green');
?>

<div class="content-left width-66 narrow-full">
	<div class="jcr-box wotw-outer">
			<h2 class='wotw-day'>Butler Bikes</h2>
			<?php echo editable_area('green', 'butler_bikes_content', $access_rights);?>
	</div>
</div>
<?php $this->load->view('green/sidebar');?>