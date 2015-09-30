<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<p>Thank you for reporting the problem. The details you have given are:</p>
<ul class="nolist">
    <li>Description: <?php echo nl2br($this->input->post('description')); ?></li>
    <li>Location: <?php echo $this->input->post('location'); ?></li>
</ul>
<a href="<?php echo site_url('faults'); ?>" class="jcr-button inline-block" title="Return to the fault reporting home">
    <span class="ui-icon ui-icon-arrowreturn-1-w inline-block"></span>Return
</a>