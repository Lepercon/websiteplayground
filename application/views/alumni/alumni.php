<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

    $this->load->model('events_model');

?>

<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Josephine Butler College Alumni</h2>
        <div>
            <?php echo editable_area('alumni', 'content', $access_rights);?>
        </div>
    </div>
<?php
    if(!empty($events)) { ?>
        <h2 class="bold-header">Next Alumni Events</h2>
        <?php foreach($events as $e) {
            $this->load->view('events/event_info', array('event' => $e));
        } ?>
<?php } ?>
<?php
    if($admin){        
?>

<div class="jcr-box wotw-outer">
<h2 class="wotw-day">Admin</h2>
<div>
<h3 style="margin:2px;">Sign Ups</h3>
<h4>Create A Sign Up</h4>
To create a sign up you will first need to create an event in the <a href="<?php echo site_url('events/add_event'); ?>">JCR Calendar</a>, and fill out the form <a href="<?php echo site_url('alumni/create_signup'); ?>">here</a>.<br>
<h4>Current Sign Ups</h4>
<table class="alumni-list-signups">
<tr><th>Event</th><th>Signup Deadline</th><th>Created By</th></tr>
<?php
    foreach($signups as $s){
?>

<tr>
    <td>
        <a href="<?php echo site_url('alumni/sign_up/'.$s['event_id']); ?>"><?php
            $event_name = $this->events_model->get_event($s['event_id']);
            echo $event_name['name']; 
        ?></a>
    </td>
    <td><?php echo date('g:i a l jS F Y',$s['signup_deadline']); ?></td>
    <td>
        <?php 
            echo $this->users_model->get_full_name($s['created_by']);
        ?>
    </td>
</tr>

<?php     
    }
?>
</table>
</div>
<font size=1 style="margin-left:5px;"> This box is displayed for admins of this page only</font>
</div>

<?php
    }

?>

</div>
<div class="content-right width-33 narrow-full">
    <div class="jcr-box">
        <ul class="nolist">
            <li>
                <a class="sprite-container" href="https://www.facebook.com/groups/7852787225/" target="_blank">
                    <div class="common-sprite" id="sprite-facebook"></div>
                    <div class="inline-block"><p>Butler Alumni on Facebook</p></div>
                </a>
            </li>
            <li>
                <a class="sprite-container" href="http://www.linkedin.com/groups?gid=2130091" target="_blank">
                    <div class="common-sprite" id="sprite-linkedin"></div>
                    <div class="inline-block"><p>Butler Alumni on LinkedIn</p></div>
                </a>
            </li>
            <li>
                <a class="sprite-container" href="http://www.dur.ac.uk/butler.angels/" target="_blank">
                    <div class="common-sprite" id="sprite-angels"></div>
                    <div class="inline-block"><p>Butler Angels</p></div>
                </a>
            </li>
            <li>
                <a class="sprite-container" href="http://butleralumni.wordpress.com/" target="_blank">
                    <div class="common-sprite" id="sprite-alumni"></div>
                    <div class="inline-block"><p>Alumni Blog</p></div>
                </a>
            </li>
            <li>
                <a class="sprite-container" href="http://www.dur.ac.uk/butler.college/alumni" target="_blank">
                    <div class="common-sprite" id="sprite-butler"></div>
                    <div class="inline-block"><p>College Alumni Website</p></div>
                </a>
            </li>
            <li>
                <a class="sprite-container" href="mailto:jbalumni.association@durham.ac.uk">
                    <div class="common-sprite" id="sprite-email"></div>
                    <div class="inline-block"><p>Email the Alumni Association</p></div>
                </a>
            </li>
        </ul>
    </div>
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Get In Contact</h3>
        <p>If you would like to know more information then get in contact:</p>
        <?php $this->load->view('utilities/users_contact', array('level_ids'=>array(4))); ?>
    </div>
    <div class="jcr-box no-padding-box">
        <a class="twitter-timeline" href="https://twitter.com/butler_alumni" data-chrome="noborders" data-widget-id="250386313516285952">Tweets by @butler_alumni</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </div>
    <div class="jcr-box narrow-hide">
        <div class="center">
            <img style="max-width: 100%;" src="<?php echo VIEW_URL; ?>alumni/img/angels-poster.jpg" width="300" height="434" alt="Butler Angels Poster" />
        </div>
    </div>
</div>