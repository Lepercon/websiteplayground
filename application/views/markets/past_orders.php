<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
	<?php echo back_link('markets');?>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Past Placed Orders</h3>
		<?php
			
		foreach ($orders as $row)
		{
			echo "<div class='jcr-box wotw-outer'>";
			   echo "<h3 class='wotw-day'>Order Number: ".$row->order."</h3>";
			   echo "<p>Date Ordered: ".gmdate("l dS F Y", $row->time)."</p>";
			echo "</div>";
		}

		?>
	</div>
</div>