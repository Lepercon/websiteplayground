<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php 
    $show_old_events = $this->uri->segment(3);
    $old_message = FALSE;
    foreach($signups as $k=>$e){ 
        $old_event = time() > $e['event_time'];
        if($show_old_events and $old_event and !$old_message and $admin){
            $old_message = TRUE;
            echo '<p>Signups below here are for events that have happend and are hidden to non-admins.</p><p>It is recomended you do not delete them for finance reasons.</p>';
        }elseif($k == 0){
            echo '<h3>There are no signups scheduled. See what\'s coming up in <a href="'.site_url('events').'">the calendar</a>.</h3>';
        }
        if((!$old_event) || ($admin and $show_old_events)){
?>
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day"><?php echo $e['name'].($old_event?' - Hidden':''); ?></h3>
        <div class="wotw-box padding-box">
            <p><?php echo anchor('signup/event/'.$e['id'],$e['name']).' is at '.date('H:i \o\n D, d/m/y', $e['event_time']); ?><br/>
                <?php if($e['signup_opens'] <= time() && time() < $e['signup_closes']) {
                    if($e['seats_remain'] < 1) echo 'Signup Full';
                    else echo 'Signup open, '.$e['seats_remain'].' seats remaining. Signup will close at '.date('H:i \o\n D, d/m/y', $e['signup_closes']);
                }
                else if($e['signup_opens'] > time()) {
                    echo 'Signup opens at '.date('H:i \o\n D, d/m/y', $e['signup_opens']);
                }
                else echo 'Signup closed'; ?>
            <br/>
            <?php if(!empty($e['swap_price'])) :
                    if($e['swapping_opens'] <= time() && time() < $e['swapping_closes']) {
                        echo 'Seat swapping open, it will close at '.date('H:i \o\n D, d/m/y', $e['swapping_closes']);
                    }
                    else if($e['swapping_opens'] > time()) {
                        echo 'Seat swapping opens at '.date('H:i \o\n D, d/m/y', $e['swapping_opens']);
                    }
                    else echo 'Seat swapping closed'; ?>
                <br/>
            <?php endif;
            echo ($e['user_has_booking'] ? '' : 'No ').'Seat Booked'; ?>
            </p>
        </div>
    </div>
<?php     
        }
    }
    if($admin){
        if($show_old_events){ ?>
            <p>(<a href="<?php echo site_url('signup'); ?>">Hide Old Events</a>)</p>
    <?php }else{ ?>
            <p>(<a href="<?php echo site_url('signup/index/1'); ?>">Show Older Events</a>)</p>
    <?php
        }
    }
?>