<div class="width-66 narrow-full content-left" style="width:79%">  
<?php
    echo '<p><b>Please note this availability is not 100% accurate.</b></p>';
    echo '<p>For more reliable availability please contact reception at: 0191 334 7260 OR <a href="mailto:butler.reception@durham.ac.uk">butler.reception@durham.ac.uk</a></p>';
    echo '<p>The University and Josephine Butler College reserve the right to overwrite any bookings.</p>';
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

    //var_dump($ins);
    
    $day_start = $date - 60*60*12;
    $day_end = $date + 60*60*12;
    foreach ($ins as $i){
        if($day_start <= $i['time_end'] || $i['time_start'] <= $day_end){
            if($i['time_start'] < $day_start)
                $i['time_start'] = $day_start;
            if($i['time_end'] > $day_end)
                $i['time_end'] = $day_end;
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
                                    $title = 'Event: '.$i['Title'].' ('.date('H:i', $i['time_start']).' until '.date('H:i', $i['time_end']).')';
                                    echo '<span class="booking-instance '.($i['user_id']==$u_id?'my-booking':'').'" style="width:'.($width).'px;left:'.$start.'px" title="'.$title.'"></span>';
                                }
                            ?>
                        </td>
			<?php foreach($times as $t){ ?>
				<td></td>
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






