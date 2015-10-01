<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Butler Bar</h3>
        <div>
            <?php echo editable_area('bar', 'content', $access_rights);
            if(!empty($events)) { ?>
                <h2 class="bold-header">Next Bar Events</h2>
                <?php foreach($events as $e) {
                    $this->load->view('events/event_info', array('event' => $e));
                } }?>
        </div>
    </div>
    <?php 
        if(logged_in()){  ?>
            <div class="jcr-box wotw-outer">
                <h3 class="wotw-day">Anonymous Bar Requests/Feedback</h3>
                <div>
                    <?php if(isset($_POST['feedback-success'])){?>
                        <p>Thank you for your feedback!</p>
                    <?php }else{ ?>
                    <p>If you have any requests/feedback for the bar, please fill in the box below:</p>
                    <?php 
                        echo form_open('', 'class="jcr-form"');
                        
                        echo form_textarea(array('name'=>'requests', 'placeholder'=>'Requests/Feedback', 'style'=>'max-width:99%;width:99%;min-width:99%;'));
                        echo form_submit('submit', 'Submit Feedback');
                        echo form_close();
                    }
                    ?>
                </div>
            </div>
<?php } ?>
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Bar Staff</h3>
        <?php $this->load->view('utilities/users_contact', array('level_ids'=>array(150,130))); ?>
    </div>
</div>
<div class="content-right width-33 narrow-full">
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Get In Contact</h3>
        <p>If you would like to know more information then get in contact:</p>
        <?php $this->load->view('utilities/users_contact', array('level_ids'=>array(8))); ?>
    </div>
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day" style="margin-bottom:8px;">Twitter</h3>
        <a class="twitter-timeline" data-chrome="noborders" href="https://twitter.com/ButlerBarStaff" data-widget-id="250251082100506624">Tweets by @ButlerBarStaff</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </div>
</div>