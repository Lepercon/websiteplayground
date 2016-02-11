<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
	<?php echo back_link('markets');?>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Orders For This Week</h3>
<?php
		if(is_admin() && !empty($orders)) { ?>
			<a href="<?php echo site_url('markets/delivered'); ?>" class="jcr-button inline-block" title="Admin: Mark orders a delivered.">
					Mark Delivered<span class="ui-icon ui-icon-check inline-block"></span>
			</a>
		<?php } 
			
		if(empty($orders)){
			echo "<p>There are no orders</p>";
		}
			
			
		?>
<?php
	
		$i=0;
		foreach ($orders as $row)
		{
			echo "<div class='jcr-box wotw-outer'>";
			  echo "<h3 class='wotw-day'>Order Number: ".$row['order']."</h3>";
			  echo "<p>Order For: ".$row['firstname']." ".$row['surname']."";
			  echo "<p>Date Ordered: ".gmdate("l dS F Y", $row["time"])."</p>";
			  echo "<p>Cap: £".number_format($row["cap"], 2, '.', ',')."</p>";
			  
			  echo "<table>";
			  echo "<tr><th>Item</th><th>Qty</th><th>Weeks Ordered For</th><th>No. Veggies</th></tr>";
				  	foreach ($order_content[$i] as $row)
					{
						echo "<tr>";
						echo "<td>".$row['name']."</td>";
						echo "<td>".$row['qty']."</td>";
						echo "<td>".$row['repeats']."</td>";
						echo "<td>".$row['veg']."</td>";
						echo "</tr>";
					}
				echo "</table>";
			  
			$i++;
			echo "</div>";
		}
		?>
		
	</div>
</div>