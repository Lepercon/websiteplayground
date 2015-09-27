<?php 

	if (!empty($error)){
		foreach($error as $e){
			echo $e;
		}
	}
	
	echo form_open('', 'class="jcr-form no-jsify"');
	
	echo form_label('Event title');
	echo form_input('Title',set_value('Title'), 'placeholder="Title" required');
	echo '<br><br>';
	
	echo form_label('Phone number');
	echo form_input('Phone_number', set_value('Phone_number'), 'placeholder="Number" required');
	echo '<Br><br>';
	
	$num_of_ppl = array_merge(array(''=>'Please Select'), range(1,30));
	echo form_label('Number of people');
	echo form_dropdown('Number_of_People',$num_of_ppl,set_value('Number_of_People'),'required="required" class="people"').'<br><br>';
	
	echo form_label('Room');
	$room_list=array(''=>'Please Select');
	echo '<select name="Rooms" required="" class="rooms"><option capacity="9999" value="">Please Select</option>';
	foreach ($rooms as $r) {
		echo '<option capacity="'.$r['capacity'].'" value="'.$r['id'].'" '.($this->input->post('Rooms')==$r['id']?'selected="selected"':'').'>'.$r['name'].' ('.$r['capacity'].')'.'</option>';
	}
	echo '</select><br><br>';
	
	echo form_label('First date of booking');
	echo form_input(array(
		'name' => 'start_date',
		'value' => set_value('start_date'),
		'placeholder' => 'DD/MM/YYYY',
		'maxlength' => '10',
		'class' => 'datepicker input-help narrow-full',
		'required' => 'required'
	));
	
	$hour = array(''=>'');
	$minute = array(''=>'');
	for($i = 0; $i <= 23; $i++) $hour[$i] = sprintf('%02d', $i);
	for($i = 0; $i <= 55; $i+=5) $minute[$i] = sprintf('%02d', $i);
	echo '<br><br>';
	echo form_label('Start time'); 
	echo form_dropdown('hour', $hour, set_value('hour'), 'class="input-help" required="required" required');
	echo ':'.form_dropdown('minute', $minute, set_value('minute'), 'class="input-help" required="required" required');
	
	$minute = array(''=>'Please select');
	for($i = 0; $i <= 500; $i+=10) $minute[$i] = sprintf('%02d', $i);
	echo '<br><br>';
	echo form_label('Length of booking (minutes)'); 
	echo form_dropdown('Length', $minute, set_value('minute'), 'class="input-help" required="required" required');

	
	$freq = array('No repeat'=>'No repeat', 'Daily'=>'Daily', 'Weekly'=>'Weekly', 'Fortnightly'=>'Fortnightly', 'Monthly'=>'Monthly');
	echo '<br><br>';
	echo form_label('Repeat bookings');
	echo form_dropdown('Frequency of bookings',$freq,set_value('Frequency_of_bookings'),'required class="frequency"');
	
	echo '<br><br>';
	echo '<div class="last-date" style="display:none">';
	echo form_label('Last date of booking');
	echo form_input(array(
		'name' => 'last_date',
		'value' => set_value('last_date'),
		'placeholder' => 'DD/MM/YYYY',
		'maxlength' => '10',
		'class' => 'datepicker input-help narrow-full',
	));
	echo '</div>';
	
	echo '<br>';
	echo form_label();
	echo form_submit('mysubmit', 'Next');
	echo form_close();