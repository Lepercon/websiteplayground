<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); echo back_link('finance');?>
<h2>Setup 'Google Authenticator'</h2>
<?php if(isset($GLOBALS['password-verify-qr-errors'])){
    echo '<div class="validation_errors">'.$GLOBALS['password-verify-qr-errors'].'</div>';
} ?>
<p>To generate a QR code you need to enter your password:</p>
<?php
    echo form_open('', 'class="jcr-form"');
    echo '<p>'.form_label('Username:').' '.$this->session->userdata('username').'</p>';
    echo '<p>'.form_label('Password:').' '.form_password('password', '', 'placeholder="Password"').'</p>';
    echo '<p>'.form_label().' '.form_submit('generate', 'Generate QR Code').'</p>';
    echo form_close();
?>
<p>Please note that this will stop any currently existing 2 factor authentication systems from working.</p>
<h2>The App</h2>
<p>You can download the app you need by searching 'Google Authenticator' in your app store.</p>