<?php

	echo form_open('', 'class="jcr-form no-jsify"');
	
	if (in_array($details['room_id'], array(1,2,3,4))){
		echo form_label('Layout');
		$layout_list=array(''=>'Please Select');
		echo '<select name="Room_Layout" required="" class="layouts"><option value="">Please Select</option>';
		foreach ($layouts as $l) {
			if ($l['which_room'] == $details['room_id']){
				echo '<option value="'.$l['id'].'" >'.$l['l_name'].'</option>';
			}
		}
		echo '</select><br><br>';
	}
	
	if (in_array($details['room_id'], array(1,2,3,5,11))){
		//echo form_label('Extras');
		$equiptment_list=array(''=>'Please Select');
		//echo '<select name="Extras" required="" class="equiptment"><option value="">Please Select</option>';
		foreach ($equiptment as $e) {
			if ($e['which_room'] == $details['room_id']){
				echo form_label($e['e_name']);
				//echo '<option value="'.$e['id'].'" >'.$e['e_name'].'</option>';
				echo form_checkbox('equipment['.$e['id'].']', $e['e_name'], FALSE).'<br>';
			}
		}
		//echo '</select><br><br>';
	}
	
	echo form_submit('mysubmit', 'next');
	echo form_hidden($details);
	echo form_close();