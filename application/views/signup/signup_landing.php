<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(empty($signups)) { ?>
    <h3>There are no signups available at the moment. Have a look at the <?php echo anchor('events', 'calendar'); ?> to see what else is on.</h3>
<?php } else {
    $this->load->view('signup/signup', array('signups' => $signups));
    
} ?>