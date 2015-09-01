<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('alumni');
function dropdown_options($option, $extra_pad){

	$i = 1;
	$st = '';
    if(!is_null($option)){
    	$options = explode(';',$option);
    	foreach($options as $o){
    		$op = explode(',', $o);
    		if($extra_pad)
    			$st .= form_label('');
    		$st .= form_label($op[0]).form_dropdown('guest-COUNT-option-'.$i, $op, '', 'id="guest-COUNT-option-'.$i.'"').'<font id="guest-COUNT-helper-'.$i.'" style="color:red;font-size:12px;"></font><br>';
    		$i++;
    	}
    }
    return str_replace('value="0"', 'value="0" style="display:none"', $st);

}

$menu_options = $alumni_event_info['options'];

?>
<h1>Alumni Event Sign Up Form</h1>
<div class="content-left width-33 narrow-full">
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Information</h2>
		<div>
			<?php 
				$access_rights = $this->alumni_model->alumni_permissions();	
				echo editable_area('alumni', 'content/signup_content', $access_rights); 				
			?>
		</div>
	</div>
	<?php
		$this->load->view('events/event_info', array('event' => $event_info));
	?>
</div>

<div class="content-right width-66 narrow-full">
<div class="jcr-box wotw-outer">
<h2 class="wotw-day">Sign Up</h2>
<?php
	if(is_null($alumni_event_info['signup_deadline'])){	
		$signup_open = true;
	}else{
		$signup_open = ($alumni_event_info['signup_deadline'] > time());
		if($signup_open)
			echo '<h3 style="padding:5px;">Signup Deadline: '.date('d/m/Y H:i',$alumni_event_info['signup_deadline']).'</h3>';
	}
	echo '<p style="padding:5px;">'.str_replace(chr(10),'<br>', $alumni_event_info['sign_up_details']).'</p>';
	echo '<span id="event_id" style="display:none">'.$alumni_event_info['event_id'].'</span>';
	echo '<span id="max_guests" style="display:none">'.$alumni_event_info['guest_limit'].'</span>';
	if($signup_open){
	    echo form_open('alumni/submit_sign_up', array('class' => 'jcr-form alumni-signup-form no-jsify', 'id'=>'alumni-sign-up-form', 'style'=>'padding:5px!important'));
	    
	    echo form_label('Name:', 'name');
	    echo form_input(array('name'=>'name', 'id'=>'name', 'value'=>'', 'size'=>100, 'placeholder'=>'Name', 'autofocus'=>'ture')).'<font id="name-helper" style="color:red;font-size:12px;"></font><br>';
	        
	    echo form_label('Name at University:', 'name-at-uni');
	    echo form_input(array('name'=>'amount', 'id'=>'name-at-uni', 'value'=>'', 'size'=>10, 'placeholder'=>'Name at University')).'<br>';
	    
	    echo form_label('Address:', 'alumni-address', array('style'=>'vertical-align: top!important;'));
	    echo form_textarea(array('name'=>'address', 'id'=>'alumni-address', 'value'=>'', 'placeholder'=>'Address', 'style'=>'height:100px;')).'<br>';
	    
	    echo form_label('Email:', 'alumni-email');
	    echo form_input(array('name'=>'email', 'id'=>'alumni-email', 'value'=>'', 'size'=>10, 'placeholder'=>'Email')).'<font id="alumni-email-helper" style="color:red;font-size:12px;"></font><br>';
	    
	    echo form_label('Phone Number:', 'alumni-phone');
	    echo form_input(array('name'=>'phone', 'id'=>'alumni-phone', 'value'=>'', 'size'=>10, 'placeholder'=>'Phone Number')).'<br>';
	    
	    echo form_label('Year of Graduation:');
	    $dates = range(date('Y'),2006,-1);
	    echo form_dropdown('year_of_graduation', $dates, '', 'id="year-of-grad"').'<br>';
	    
	    echo form_label('Subject:', 'subject-at-uni');
	    echo form_input(array('name'=>'subject', 'id'=>'subject-at-uni', 'value'=>'', 'size'=>10, 'placeholder'=>'Subject')).'<font id="alumni-subject-helper" style="color:red;font-size:12px;"></font><br>';
	
		if(!is_null($menu_options)){
			echo '<span id="options" style="display:none">'.sizeof(explode(';',$menu_options)).'</span>';
		    echo str_replace('COUNT', '0', dropdown_options($menu_options, false));
		    echo '<br>';
	    }else{
	    	echo '<span id="options" style="display:none">0</span>';
	    }
	    
	    $st = '<span id="guest_span_COUNT">';
	    $st .= form_label('Guest COUNT Name:', 'guest-nameCOUNT').form_input(array('name'=>'guest-nameCOUNT', 'id'=>'guest-nameCOUNT', 'value'=>'', 'size'=>10, 'placeholder'=>'Guest COUNT Name'));
	    $st .= '<font id="guest-COUNT-helper" style="color:red;font-size:12px;"></font><br>';
	    if(!is_null($menu_options)){    
	    	$st .= dropdown_options($menu_options, true);
	    }
	    $st .= '</span>';
	    echo '<span id="guest_span_0" style="display:none;">'.$st.'</span>'; //Hide the code for the input boxes in the html so the javascript can get it and create more later    
	    echo form_label('');        
	        
	?>
	<a class="jcr-button inline-block alumni-add-guest" title="Add another guest" href="#">
		<span class="inline-block ui-icon ui-icon-plus"></span>Add Guest
	</a>
	<span id="alumni-signup-remove-guest" style="display:none"><a class="jcr-button inline-block alumni-remove-guest" title="Remove a Guest" href="#">
		<span class="inline-block ui-icon ui-icon-minus"></span>Remove Guest
	</a></span><br>
	<?php
	    echo form_label('Dietary Requirements / Request:', 'details', array('style'=>'vertical-align: top!important;'));
	    echo form_textarea(array('name'=>'details', 'id'=>'details', 'value'=>'', 'placeholder'=>'Details', 'style'=>'height:100px;')).'<br><br>';
	    
	    echo form_label('Total Cost:');
	    echo '£<span id="cost">'.$alumni_event_info['cost'].'</span><br><br>';
	    
	    echo form_label('');
	    echo form_submit(array('name'=>'submit', 'value'=>'Sign Up', 'id'=>'alumni-sign-up'));
	    
	    echo form_close();
    }else{
    	echo '<h3 style="padding:5px;">Sorry, signup for this event has now closed.</h3>';    	
    }    
?>
<p style="padding:5px"><?php echo 'If you have any questions, you can contact our Alumni Relations Assistant, '.$current_scdo; ?></p>
</div>
</div>