<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<h3>Are you sure you want to cancel this signup?</h3>
<p>This will delete all associated bookings.</p>
<br />
<?php echo form_open('signup/cancel_signup/'.$e_id);
    echo token_ip('cancel_signup');
    echo form_submit('cancel', 'Cancel Signup');
    echo form_submit('dont', 'Don\'t Cancel');
echo form_close();