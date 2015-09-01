<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup/event/'.$e_id);
echo print_link(); ?>
<h3>List of Seat Changes</h3>
<?php if(!empty($movements)) { ?>
	<ul>
	<?php foreach($movements as $m) {
		echo '<li><b>'.$m['movement_by'].'</b>: '.$m['movement_of'].'</li>';
	} ?>
	</ul>
<?php } else { ?>
	<p>There have been no seat swaps yet.</p>
<?php }?>