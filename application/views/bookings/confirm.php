<?php 
		
	var_dump($details);
	var_dump($_POST);
	$room = $this->bookings_model->room_id_to_name($details);
	echo 'You have selected the '.$room.' for '.$details['number_of_people'].' people on '.date('D d/m/Y \a\t H:i.',$details['booking_start']);
	if ($details['frequency'] !== 'No repeat') {
		echo ' The booking will repeat '.$details['frequency'].' until '.date('D d/m/Y.',$details['booking_end']).'<br>';
	}
	if (in_array($details['room_id'], array(1,2,3,4))){
		echo 'You have chosen the layout '.$details['Layout'];
	}
	if (in_array($details['room_id'], array(1,2,3,5,11))){
		echo 'You have chosen to use the equiptment '.$details['Equiptment'];
	}
	echo form_open('', 'class="jcr-form no-jsify"');
	echo form_submit('confirm_booking', 'Confirm Booking');
	echo form_hidden($details);
	echo form_close();
	
	