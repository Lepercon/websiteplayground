<?php
	echo 'Welcome to the Butler JCR bookings page!<br><br>
		Please be aware that availabilty on here may not be 100% up to date and that bookings take 2 working days to be processed by college.<br><br>
		You cannot make a booking which crosses between term and holiday time.<br><br>
		The University and Josephine Butler College reserve the right to reject any booking applications and to overwrite any accepted bookings at a later date.'.'<br><br><br>';
	//echo anchor('bookings/book_old', 'Old system', 'class="jcr-button"');
	echo ' '.anchor('bookings/calender', 'View availability', 'class="jcr-button"');
	echo ' '.anchor('bookings/book', 'Make a booking', 'class="jcr-button"');
        echo ' '.anchor('bookings/tsandcs', 'Term and Conditions', 'class="jcr-button"');
        //echo ' '.anchor('bookings/forms', 'Download a Booking Form', 'class="jcr-button"'); 