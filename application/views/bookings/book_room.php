<?php
 
    if(isset($GLOBALS['errors'])){
        foreach($GLOBALS['errors'] as $e){
            if ($e != FALSE) {
                echo '<p class="validation-failure">'.$e.'</p>';
            }
        }
    }
	
	echo form_open('', 'class="jcr-form no-jsify room-booking-form"');
	
	echo form_label('Event title');
	echo form_input('Title',set_value('Title'), 'placeholder="Title" required class="title"');
	echo '<br><br>';
	
	echo form_label('Phone number');
	echo form_input('Phone_number', set_value('Phone_number'), 'placeholder="Number" required class="phone"');
	echo '<Br><br>';
	
	$num_of_ppl = array_merge(array(''=>'Please Select'), range(0,30));
	echo form_label('Number of people');
	echo form_dropdown('Number_of_People',$num_of_ppl,set_value('Number_of_People'),'required="required" class="people"').'<br><br>';
	
	echo form_label('Room');
	$room_list=array(''=>'Please Select');
	echo '<select name="room_id" required="" class="rooms"><option capacity="9999" value="">Please Select</option>';
	foreach ($rooms as $r) {
		echo '<option capacity="'.$r['capacity'].'" value="'.$r['id'].'" '.($this->input->post('Rooms')==$r['id']?'selected="selected"':'').'>'.$r['name'].' ('.$r['capacity'].')'.'</option>';
	}
	echo '</select><br><br>';
	
	echo '<div class="layout_selection">';
		echo form_label('Layout');
		$layout_list=array(''=>'Please Select');
		echo '<select name="Room_Layout" required class="layouts"><option value="">Please Select</option>';
		foreach ($layouts as $l) {
			//if ($l['which_room'] == $details['room_id']){ 
				echo "\n";
				echo '<option value="'.$l['id'].'"'.' room-id="'.$l['which_room'].'">'.$l['l_name'].'</option>';
			//}
		}
		echo '</select><br><br>';
	echo '</div>';
	
	echo '<div class="equiptment_selection">';
		$equiptment_list=array(''=>'Please Select');
		foreach ($equiptment as $e) {
			//if ($e['which_room'] == $details['room_id']){7
				echo '<p style="display:none;">';
				echo form_label($e['e_name']);
				echo form_checkbox('equipt['.$e['id'].']', $e['e_name'], FALSE, 'room-id = "'.$e['which_room'].'"').'<br>';
				echo '</p>';
			//}
		}
	echo '</div>';
	
	echo '<div class="start-date">';
        echo form_label('First date of booking');
	echo form_input(array(
		'name' => 'start_date',
		'value' => set_value('start_date'),
		'placeholder' => 'DD/MM/YYYY',
		'maxlength' => '10',
		'class' => 'datepicker input-help narrow-full',
		'required' => 'required'
	));
	echo '</div>';
        
	$hour = array(''=>'');
	$minute = array(''=>'');
	for($i = 0; $i <= 23; $i++) $hour[$i] = sprintf('%02d', $i);
	for($i = 0; $i <= 55; $i+=5) $minute[$i] = sprintf('%02d', $i);
	echo '<br><br>';
	echo form_label('Start time'); 
	echo form_dropdown('s_hour', $hour, set_value('hour'), 'class="input-help start-hour" required="required" required');
	echo ':'.form_dropdown('s_minute', $minute, set_value('minute'), 'class="input-help start-min" required="required" required');
	
	echo '<br><br>';
	echo form_label('End time'); 
	echo form_dropdown('e_hour', $hour, set_value('hour'), 'class="input-help end-hour" required="required" required');
	echo ':'.form_dropdown('e_minute', $minute, set_value('minute'), 'class="input-help end-min" required="required" required');
	
	$freq = array('No repeat', 'Weekly', 'Fortnightly', 'Monthly');
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
	echo form_submit('main_details', 'Next');
	echo form_close();
	
	echo '<br>'.anchor('bookings/index', 'Return to bookings home', 'class="jcr-button"');