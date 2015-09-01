<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup/event/'.$e_id); 

$last_id = -1;
?>
<h2>Table of Reservation Attempts</h2>
<em>This table shows users who attempted to sign up, along with the number of reservations they ultimately kept. Negative seat values show canceled or expired seats.</em>
<table class="attempts-table">
<tr><th>Name</th><th>ID</th><th>Reservations</th><th>Time</th><th>Table</th><th>Seats</th></tr>
<?php
	foreach($attempts as $a){
?>
		<tr class="<?php echo $last_id==$a['user_id']?'no-border':'border'; ?>">
			<?php if($last_id==$a['user_id']){ ?>
				<td colspan="3"></td>
			<?php }else{ ?>
				<td><?php echo $last_id==$a['user_id']?'':(($a['prefname']==''?$a['firstname']:$a['prefname']).' '.$a['surname']); ?></td>
				<td><?php echo $last_id==$a['user_id']?'':$a['user_id']; ?></td>
				<td><?php echo $last_id==$a['user_id']?'':$a['reservations']; ?></td>
			<?php } ?>
			<td><?php echo date('d-m-Y H:i:s', $a['timestamp']); ?></td>
			<?php if(is_null($a['table_num'])){ ?>
				<td colspan="2">Page View</td>
			<?php }else{ ?>
				<td><?php echo $a['table_num']; ?></td>
				<td><?php echo $a['num_seats']; ?></td>
			<?php } ?>
		</tr>	
<?php
		$last_id = $a['user_id'];
	}
?>
</table>