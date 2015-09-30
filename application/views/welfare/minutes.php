<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<h2 style="margin-top:0;">Meeting Minutes</h2>
<?php
    $this->load->view('utilities/file_browser', array(
        'admin' => $access_rights,
        'section' => 'welfare',
        'path' => 'welfare/minutes'
    ));
?>
