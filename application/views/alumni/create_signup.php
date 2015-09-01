<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
    echo back_link('alumni');
?>

<div class="jcr-box wotw-outer content-left width-33 narrow-full">
<h2 class="wotw-day">Information</h2>
<div>
<p>This form can be used to create external sign-ups for events that already exist in the <a href="<?php echo site_url('events/add_event'); ?>">JCR calendar</a>. These sign-ups will be availible to users who do not have an account to login to the website.</p>
<p><b>Select Event:</b> If the event isn't in the list, you'll need to add it to the <a href="<?php echo site_url('events/add_event'); ?>">JCR calendar</a>.</p>
<p><b>Cost:</b> Specify the cost per ticket for the event.</p>
<p><b>Details:</b> This information will be viewable on the signup page for this event.</p>
<p><b>Deadline:</b> The signup deadline for this event. Note that 10/12/2014 00:00 would be a deadline of the night of the 9th December, as soon as it turns midnight.</p>
<p><b>Ticket Limit:</b> Number of total available tickets, once this number of people has signed up, signup will close.</p>
<p><b>Including Guests:</b> Choose whether guests count in the ticket limit, if 0 is selected for 'Guest Limit' then this option is ignored.</p>
<p><b>Guest Limit:</b> If you wish to specify a maximum number of guests each member can bring, specify that here, choosing 0 will not allow users to sign up guests.</p>
<p><b>Dropdown Menus:</b> This can be used to define options for the user, such as meal, t-shirt size etc. You can specify an item limit in the drop down box. Note: The total for each dropdown menu must add up to at least the value of 'Ticket Limit', or one of the options must be 'No Limit'.</p>
<p><b>Email Message:</b> This message will be emailed to the user once they have signed up, it can be used to specify how or when they will receive more info about the event, or just contain information for future reference.</p>
<p><b>RSVP Info:</b> Specify where you would like RSVPs sent to. Note: the easiest way to view everybody who has signed up is to go <a href="<?php echo site_url('alumni'); ?>">here</a>.</p>
<p><b>Email Type:</b> You can specify how frequently you would like to be emailed about people signing up to the event.</p>

</div>

</div>



<div class="jcr-box wotw-outer content-right width-66 narrow-full">



<h2 class="wotw-day">Alumni Event - Create Sign Up</h2>



<?php    



    echo form_open('alumni/create_alumni_signup', array('class' => 'jcr-form create-sign-up-form no-jsify', 'id'=>'alumni-create-sign-up-form', 'style'=>'padding:5px!important'));  

    $events = array();



    foreach($event as $e){

        $events[] = date('d/m/Y',$e['time']).' - '.$e['name'];

    }



	echo form_label('Select Event:');

    echo form_dropdown('event', $events, '', 'id="event"').'<br>';	



    echo form_label('Cost:');	

    echo form_radio(array('name'=>'cost', 'id'=>'cost-free', 'value'=>'free', 'checked'=>TRUE, 'style'=>'margin:10px', 'class'=>'cost-free')).'<span class="cost-free" style="vertical-align:middle!important;">Free</span><br>';	

    echo form_label('');

	echo form_radio(array('name'=>'cost', 'id'=>'cost-not-free', 'value'=>'not-free', 'checked'=>FALSE, 'style'=>'margin:10px', 'class'=>'custom-price'));

	echo form_input(array('name'=>'input-cost', 'id'=>'create-signup-cost', 'value'=>'', 'size'=>10, 'placeholder'=>'Custom Price', 'class'=>'custom-price')).'<br>';



	echo form_label('Details:', 'create-signup-details', array('style'=>'vertical-align: top!important;'));

	echo form_textarea(array('name'=>'create-signup-details', 'id'=>'create-signup-details', 'value'=>'', 'placeholder'=>'Details specific to this event. Users will see this info before signing up.', 'style'=>'height:100px;')).'<br>';



    echo form_label('Deadline');

	echo '<span style="vertical-align:middle!important;">Date: '.form_input(array(

		'name' => 'date',

		'value' => (isset($e) ? date("d/m/Y", $e['time']) : (isset($errors) ? set_value('date') : $this->input->get('start') === FALSE ? '' : str_replace('-','/',$this->input->get('start')))),

		'placeholder' => 'DD/MM/YYYY',

		'maxlength' => '10',

		'class' => 'datepicker input-help narrow-full',

		'title' => 'Please select the event date from the dropdown calendar. If no calendar shows then please enable javascript and try again or enter the date in DD/MM/YYYY format.',

		'required' => 'required'

	)).'<br>';

	$hours = array();

	$minutes = array();

	for($i=0;$i<=23;$i++) $hours[] = sprintf('%02d', $i);

	for($i=0;$i<=59;$i++) $minutes[] = sprintf('%02d', $i);

	echo form_label('').'Time: ';

	echo form_dropdown('create-signup-hour', $hours, '0', 'id="create-signup-hour"').':';

	echo form_dropdown('create-signup-minute', $minutes, '0', 'id="create-signup-minute"');

	echo '</span><br>';

	

    echo form_label('Ticket Limit:', 'create-signup-guestlimit');

    $options = range(1,999);

    array_unshift($options, 'No Limit');

    echo form_dropdown('create-signup-ticket-limit', $options, '', 'id="create-signup-ticket-limit"');

    

    echo '<span style="vertical-align:middle!important;">';

    echo form_checkbox(array('name'=>'include-guests-in-limit', 'id'=>'include-guests-in-limit', 'value'=>'include-guests-in-limit', 'checked'=>TRUE, 'style'=>'margin:10px',));

    echo 'Including Guests</span><br>';



    echo form_label('Guest Limit:', 'create-signup-guestlimit');	

    $options = range(0,20);	

    array_unshift($options, 'No Limit');

    echo form_dropdown('create-signup-guestlimit', $options, '', 'id="create-signup-guestlimit"').'<br>';	



?>

<label>Dropdown Menus:</label><br>

<span id="option-0" style="display:none;">
    <span id="option-COUNT" style="padding:10px">
		<label for="optionname-COUNT">Dropdown COUNT:</label>
		<input type="text" name="optionname-COUNT" id="optionname-COUNT" placeholder="Dropdown Menu Title">
		<font id="optionname-COUNT-helper" color="red" size="2"></font><br>		
		<span id="suboptions" style="display:none;">2</span>
		<span id="option-number" style="display:none;">COUNT</span>
		<span id="option-COUNT-0" style="display:none;">
			<span id="option-COUNT-SUB">
				<label></label>
				<label for="option-value-COUNT-SUB">Option SUB:</label>
				<input type="text" name="option-value-COUNT-SUB" id="option-value-COUNT-SUB" placeholder="Option SUB" style="width:100px">                
                <?php
                    $options = range(0,999);    
                    array_unshift($options, 'No Limit');
                    echo form_dropdown('option-value-COUNT-SUB-limit', $options, '', 'id="option-value-COUNT-SUB-limit"').'<br>';
                ?>
				<font id="option-COUNT-sub-SUB-helper" color="red" size="2"></font><br>
			</span>
		</span>
		<label></label><label></label>
		<span class="add"><a class="jcr-button inline-block" id="add-suboption-COUNT"><span class="inline-block ui-icon ui-icon-plus"></span>Add Option</a></span>
		<span class="remove" style="display:none;"><a class="jcr-button inline-block remove" id="remove-suboption-COUNT"><span class="inline-block ui-icon ui-icon-minus"></span>Remove Option</a></span><br>
	</span>
</span>

<label></label>

<a class="jcr-button inline-block" id="add-dropdown"><span class="inline-block ui-icon ui-icon-plus"></span>Add Dropdown Menu</a>

<span style="display:none" class="remove-dropdown"><a class="jcr-button inline-block" id="remove-dropdown"><span class="inline-block ui-icon ui-icon-minus"></span>Remove Dropdown Menu</a></span><br>

<br>



<?php



    echo form_label('Email Message:', 'create-signup-email-message', array('style'=>'vertical-align: top!important;'));	

    echo form_textarea(array('name'=>'create-signup-email-message', 'id'=>'create-signup-email-message', 'value'=>'', 'placeholder'=>'A message that will be emailed to the user once they have signed up.', 'style'=>'height:100px;')).'<br>';



    echo form_label('RSVP Info:');	

    echo form_checkbox(array('name'=>'email-scdo', 'id'=>'email-scdo', 'value'=>'email-scdo', 'checked'=>FALSE, 'style'=>'margin:10px',));

    echo '<span style="vertical-align:middle!important;">Email SCDO</span><br>';

	echo form_label('');	

	echo form_checkbox(array('name'=>'email-user', 'id'=>'email-user', 'value'=>'email-user', 'checked'=>TRUE, 'style'=>'margin:10px'));

	echo '<span style="vertical-align:middle!important;">Email Yourself (<a href="mailto:'.$user_email.'">'.$user_email.'</a>)</span><br>';

	echo form_label('');	

    echo form_checkbox(array('name'=>'email-other', 'id'=>'email-other', 'value'=>'email-other', 'checked'=>FALSE, 'style'=>'margin:10px', 'class'=>'custom-email'));

    echo '<span style="vertical-align:middle!important;" class="custom-email">Email Other: </span>';

	echo form_input(array('name'=>'create-signup-rsvp-email', 'id'=>'create-signup-rsvp-email', 'value'=>'', 'placeholder'=>'Email Address', 'class'=>'custom-email')).'<br>';

	echo form_label('Email Type:');
	echo '<span class="email-type">'.form_radio(array('name'=>'email-type', 'id'=>'email-type', 'value'=>'email-type-daily', 'checked'=>TRUE, 'style'=>'margin:10px')).'Daily Digest</span><br>';
	echo form_label('');
	echo '<span class="email-type">'.form_radio(array('name'=>'email-type', 'id'=>'email-type', 'value'=>'email-type-weekly', 'checked'=>FALSE, 'style'=>'margin:10px')).'Weekly Digest</span><br>';	
	echo form_label('');
	echo '<span class="email-type">'.form_radio(array('name'=>'email-type', 'id'=>'email-type', 'value'=>'email-type-instant', 'checked'=>FALSE, 'style'=>'margin:10px')).'Instant</span><br>';


	echo form_label('');

	echo form_submit('submit', 'Create Signup');

    echo form_close();



?>

</div>



<div class="jcr-box wotw-outer content-right width-66 narrow-full">

<h2 class="wotw-day">Help</h2>

<div>

For help or more information, or to request more options or features, send in a request <a href="<?php echo site_url('contact/user/jcr'); ?>">here</a>.

</div>

</div>







