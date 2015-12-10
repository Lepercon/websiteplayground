<div class="width-66 narrow-full content-left" style="width:79%">  
<?php
echo 'Please note this availability is not 100% accurate. For more reliable availability please contact reception at:<br><br>0191 334 7260    OR    butler.reception@durham.ac.uk<br><br>
        The University and Josephine Butler College reserve the right to overwrite any bookings.'.'<br>';
?>
    
    <?php 
	$times = range(6, 23);
	$next_day = $date + (60*60*24);
	$prev_day = $date - (60*60*24);
	$next_week = $date + (60*60*24*7);
	$prev_week = $date - (60*60*24*7);
	
        $this->db->join('bookings_reservations', 'bookings_reservations.id=bookings_instances.booking_id');
	$ins = $this->db->get('bookings_instances')->result_array();
        $u_id = $this->session->userdata('id');
	$rooms = $this->db->get('bookings_rooms')->result_array();
	$instances = array();
	foreach ($rooms as $r){
		$instances[$r['id']] = array();
	}
	
	foreach ($ins as $i){
		$day_end = $date + 60*60*24;
		if($date <= $i['time_start'] && $i['time_start'] <= $day_end){
			$instances[$i['room_id']][] = $i;
		}
	}
?>
<table class="navigation-bar">
	<tr><td style="border:0"><?php	
	echo anchor('bookings/calender/'.date('Y',$prev_week).'/'.date('m',$prev_week).'/'.date('d',$prev_week), 'Prev week');
	?></td><td style="border:0"><?php	
	echo anchor('bookings/calender/'.date('Y',$prev_day).'/'.date('m',$prev_day).'/'.date('d',$prev_day), 'Prev day');
	?></td><td style="border:0"><?php
	echo date('l', $date).' '.date('d', $date).date('S', $date).' '.date('F', $date).' '.date('Y', $date);
	?></td><td style="border:0"><?php
	echo anchor('bookings/calender/'.date('Y',$next_day).'/'.date('m',$next_day).'/'.date('d',$next_day), 'Next day');
	?></td><td style="border:0"><?php
	echo anchor('bookings/calender/'.date('Y',$next_week).'/'.date('m',$next_week).'/'.date('d',$next_week), 'Next week');
?>

</tr>
</table>

<table class="calendar-view">
	<tr>
		<td></td><td></td>
		<?php foreach($times as $t){ ?>
			<td><?php echo $t.':00'; ?></td>
		<?php } ?>
	</tr>
	<?php foreach($rooms as $r){ ?>
		<tr>
			<td>
				<p class="room_name"><?php echo $r['name']; ?></p>
			</td>
                        <td>
                            <?php
                                foreach($instances[$r['id']] as $i){
                                    $length = $i['time_end'] - $i['time_start'];
                                    $width = ($length / (60 * 60)) * 37;
                                    $start = 119 + 37 * (($i['time_start'] % (60*60*24))/(60*60) - 6);
                                    echo '<span class="booking-instance '.($i['user_id']==$u_id?'my-booking':'').'" style="width:'.($width).'px;left:'.$start.'px" title="Event: '.$i['Title'].'"></span>';
                                }
                            ?>
                        </td>
			<?php foreach($times as $t){ ?>
				<?php
					//if ($r['id'] == $b['room_id'] && $t == date('G', $b['booking_start'])){
						// put close php here <td bgcolor="#FF0000"><?php
					//}
					//else{
						?><td><?php
					//}
				?></td>
			<?php } ?>
		</tr>
	<?php } ?>
</table>
<?php
	echo '<br>'.anchor('bookings/index', 'Return to bookings home', 'class="jcr-button"');
?>

</div>
<div class="width-33 narrow-full content-right" style="width:19%;">
<table class="navigation-bar">
	
<?php
	$mnth = 60*60*24*30;
	$end = $date + $mnth*9;
	$d = $date - $mnth*9;
	while ($d <= $end){
		?><tr><td><?php
		echo anchor('bookings/calender/'.date('Y',$d).'/'.date('m',$d).'/'.date('d',$d), date('F',$d).' '.date('Y',$d));
		?></td></tr><?php
		$d = $d + $mnth;
	}
?>
</table>
</div>






