<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="page-navigation">
	<ul class="nolist">
		<?php
			$page = $this->uri->rsegment(2);
			$pages = array('index'=>"Who's Who?", 'history'=>'History');
			foreach($pages as $k=>$p){
		?>
				<li <?php echo ($k==$page?'class="current-page"':''); ?>>
					<?php echo anchor('whoswho/'.$k, $p); ?>
				</li>
		<?php } ?>
	</ul>
</div>
