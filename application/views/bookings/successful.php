<?php

var_dump($_POST);
var_dump($details);

echo 'Your request has been submitted to the College staff and should be prosesed within 2 working days. You will recieve email confirmation once this is done.<br><br>
	Please be aware that all bookings are subject to being overwritten by the University. You will recieve an email at the start of every week you have a booking detailing whether any changes have been made
	If you have any queeries please click '.anchor('contact/user/jcr', 'HERE').' or contact: butler.jcr@durham.ac.uk<br><br>
	Thank you for booking through the JCR website!';
	
	echo '<br><br><br>'.anchor('bookings/index', 'Return to bookings home', 'class="jcr-button"'); 

