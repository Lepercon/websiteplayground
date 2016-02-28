<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); echo back_link('finance');?>
<h2>Enter Code</h2>
<?php if(isset($GLOBALS['authorise-verify-errors'])){
    echo '<div class="validation_errors">'.$GLOBALS['authorise-verify-errors'].'</div>';
} ?>
<p>Please open the Google Authenticator app and enter the generated code below:</p>
<?php
    echo form_open('', 'class="jcr-form no-jsify"');
    echo '<p>'.form_label('Code:').' '.form_input('code', '', 'placeholder="Code"').'</p>';
    echo '<p>'.form_label().' '.form_submit('authorise', 'Authorise').'</p>';
    echo form_close();
?>
<p>If you need to setup the Google Authenticator app, please click <a href="<?php echo site_url('finance/claims/show_qr_code'); ?>">here</a>.</p>