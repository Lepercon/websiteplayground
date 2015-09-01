<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('events'); ?>

<h2>Subscribe to the Butler JCR event calendar</h2>
<p>Subscribing to the Butler JCR calendar allows you to see the event schedule in the software, website or device of your choice. The calendar will be kept up-to-date automatically. Below are instructions for the most common calendar applications.</p>
<div id="accordion">
	<h3>Office 365 (Durham Outlook Web App)</h3>
	<div>
		<ol>
			<li>Once logged into the student email system, click on 'Calendar' at the top of the screen</li>
			<li>Right click on 'My Calendars' on the left of the screen and select 'Open Calendar</li>
			<li>Enter <b><?php echo site_url('events/getcal/'.$this->session->userdata('id')); ?></b> in the 'Internet Calendar' field</li>
			<li>Click 'Open'</li>
		</ol>
	</div>
	<h3>Microsoft Office Outlook 2013/2010</h3>
	<div>
		<ol>
			<li>In calendar view, on the 'Home' tab select 'Open Calendar' and choose 'From Internet' from the dropdown</li>
			<li>Enter <b><?php echo site_url('events/getcal/'.$this->session->userdata('id')); ?></b> in the URL field</li>
			<li>Click 'OK'</li>
			<li>Click 'Advanced' to give the calendar a name and optional description, then click 'OK'</li>
			<li>Click 'Yes' to subscribe to the calendar and receive future updates</li>
		</ol>
	</div>
	<h3>iPhone / iPad</h3>
	<div>
		<ol>
			<li>Click on the Settings app.</li>
			<li>Scroll down to "Mail, Contacts, Calendars".</li>
			<li>Choose "Add Account" then "Other".</li>
			<li>Under the Calendars section, click "Add Subscribed Calendar".</li>
			<li>Enter <b><?php echo site_url('events/getcal/'.$this->session->userdata('id')); ?></b> in the server field.</li>
			<li>Click on "Next", you may wish to change the description to something like "Butler Events", then click "Save".</li>
			<li>You should be able to view the calendar by visiting the Calender app.</li>
		</ol>
	</div>
	<h3>Google Calendar (Also for Android sync)</h3>
	<div>
		<ol>
			<li>Click the down-arrow next to 'Other Calendars'</li>
			<li>Select 'Add by URL' from the menu</li>
			<li>Enter <b><?php echo site_url('events/getcal/'.$this->session->userdata('id')); ?></b> in the URL field</li>
			<li>Click the 'Add Calendar' button. The calendar will appear in the 'Other Calendars' section of the calendar list to the left.</li>
		</ol>
	</div>
	<h3>Outlook.com Calendar (Also for Windows 8 and Windows Phone sync)</h3>
	<div>
		<ol>
			<li>In Outlook.com go to the calendar</li>
			<li>Click 'Import'</li>
			<li>Click 'Subscribe'</li>
			<li>Enter <b><?php echo site_url('events/getcal/'.$this->session->userdata('id')); ?></b> in the URL field</li>
			<li>Assign a name, colour and an optional charm for the calendar</li>
			<li>Click 'Subscribe'</li>
			<li>The calendar will automatically be shown in Windows Phone and Windows 8 devices synchronised with your Outlook account</li>
		</ol>
	</div>
</div>
<p>Depending on the application, updates may take up to 48 hours to be visible.</p>
<p>If you wish to see all past events from the Butler JCR calendar too, change the subscription URL to <b><?php echo site_url('events/getallcal/'.$this->session->userdata('id')); ?></b></p>

