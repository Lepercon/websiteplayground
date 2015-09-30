<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
                <h2 class='wotw-day'>Volunteering Projects</h2>
                <?php echo editable_area('volunteering', 'content', $access_rights);?>
    </div>
    <?php
    if(!empty($events)) { ?>
        <div class="jcr-box wotw-outer">
            <h2 class="wotw-day">Next Volunteering Events</h2>
            <div>
            <?php foreach($events as $e) {
                
                $this->load->view('events/event_info', array('event' => $e));
            } ?>
            </div>
        </div>
    <?php } ?>

</div>
<?php $this->load->view('volunteering/sidebar');?>