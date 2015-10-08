<?php 
		
	var_dump($details);
	var_dump($_POST);
	$room = $this->bookings_model->room_id_to_name($details);
	echo 'You have selected the '.
			$room.
			' for '.
			$details['number_of_people'].
			' people. The booking will start at '.
			date('H:i',$details['booking_start']).
			', and end at '.
			date('H:i',$details['booking_end']).
			' on '.
			date('D d/m/Y',$details['booking_start']);
	if ($details['frequency'] !== 'No repeat') {
		echo ' The booking will repeat '.$details['frequency'].' until '.date('D d/m/Y.',$details['booking_end']);
	}
	if (in_array($details['room_id'], array(1,2,3,4))){
		echo '<br>You have chosen the layout '.$details['Layout'];
	}
	if (!empty($details['Equiptment'])){
		echo '<br>You have chosen to use the equiptment: '.$details['Equiptment'].'.';
	}
	else{
		echo '<br>You have chosen to have no equiptment';
		var_dump($details['Equiptment']);
	}
	echo form_open('', 'class="jcr-form no-jsify"');
	echo form_submit('confirm_booking', 'Confirm Booking');
	echo form_hidden($details);
	echo form_close();
	echo '<br>'.anchor('bookings/index', 'Return to bookings home', 'class="jcr-button no-jsify"');
	
