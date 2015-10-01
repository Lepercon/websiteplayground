<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h2>Please Check Your Email</h2>
<p>We have sent a confirmation email to <b><?php echo $this->session->userdata('custom_email'); ?></b>, please click that link to confirm your email.</p>
<?php
    echo form_open('', 'class="jcr-form"');
    echo form_submit('account_resend_email', 'Resend Email');
    echo form_submit('account_update_email', 'Change Email');
    echo form_close();
    
?>