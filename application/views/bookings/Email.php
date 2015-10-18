<link href='https://fonts.googleapis.com/css?family=Monsieur+La+Doulaise' rel='stylesheet' type='text/css'>

<style>
table{ 
	border-collapse:collapse;
}
td{
	border:1px solid black;
}
</style>

<h1>Josephine Butler College</h1> 
<h2>ROOM BOOKING REQUEST</h2>
<p>Please read the notes overleaf before filling in the form. Once completed, e-mail the form to butler.reception@durham.ac.uk.</p>
<table style="width:100%">
	<tr>
		<td style="width:10%;">Start date</td>
		<td style="width:40%;"><?php echo date('d/m/Y',$b['booking_start']); ?></td>
		<td style="width:10%;">End date</td>
		<td style="width:40%;"><?php echo date('d/m/Y',$b['booking_end']);?></td>
	</tr>
	<tr>
		<td>Start time</td>
		<td><?php echo date('H:i',$b['booking_start']);?></td>
		<td>End time</td>
		<td><?php echo date('H:i',$b['booking_end']);?></td>
	</tr>
	<tr>
		<td>Title of event</td>
		<td colspan="3"><?php echo $b['Title'];?></td>
	</tr>
	<tr>
		<td>Organiser name</td>
		<td><?php echo $this->users_model->get_full_name($b['user_id'], FALSE);?></td>
		<td>Telephone</td>
		<td><?php echo $b['Phone_number'];?></td>
	</tr>
	<tr>
		<td>Organiser e-mail</td>
		<td colspan="3"><?php 
			 $user = $this->users_model->get_users($b['user_id'],'email');
			 echo $user['email'];
		?></td>
	</tr>
</table>
<br>
<table>
	<tr>
		<td style="width:30%;"><b>Rooms required (Max nos.)</b></td>
		<td style="width:10%;"><b>Numbers attending</b></td>
		<td style="width:20%;"><b>Layout</b></td>
		<td style="width:40%;"><b>Special requirements</b></td>
	</tr>
<?php
	foreach($room as $r){
?>
		<tr>
			<td><?php 
			if ($r['id'] == $b['room_id']){
				echo "<b>".$r['name'].' ('.$r['capacity'].')';
			}
			else{
				echo $r['name'].' ('.$r['capacity'].')';
			}
			?></td>
			<td><?php if ($r['id'] == $b['room_id']){
							echo '<b>'.$b['number_of_people'];
							}
			?></td>
			<td><?php foreach($layout as $l){
				if ($r['id'] == $l['which_room']){
					if ($l['id'] == $b['Layout']){
						echo "<b>".$l['l_name'].'</b><br>';
					}
					else{
						echo $l['l_name'].'<br>';
					}
				}
			}
			?></td>
			<td><?php
				if ($r['id'] == $b['room_id']){
                                    $E = explode(', ', $b['Equiptment']);
                                    foreach ($E as $e) {
                                        echo "<b>".$this->bookings_model->euiptment_id_2_name($e)."</b>";
                                    }
				}
			?></td>
		</tr>
<?php 
	}
?>
	</table>

<table width="100%;">
	<tr>
		<td width:30%;border:0">Signed Organiser:</td>
		<td style="width:20%;border:0;font-size:30px;padding-top:0;font-family:'Monsieur La Doulaise',cursive"><i style="position:absolute;margin-top:-10px;"><?php echo "<b>".$this->users_model->get_full_name($b['user_id'], FALSE);?></i></td>
		<td style="width:30%;border:0">Date:</td>
		<td style="width:20%;border:0"><?php echo substr ($b['timestamp'], 0, 10);?></td>
	</tr>
	<tr>
		<td style="border:0;">Signer Operations Manager:</td>
		<td style="border:0;"></td>
		<td style="border:0;">Date:</td>
		<td style="border:0;"></td>
	</tr>

</table>


