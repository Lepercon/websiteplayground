<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content-left width-50 narrow-full">
    <h2><a href="<?php echo site_url('game/snake'); ?>">Snake</a></h2>
    <p>Snake originated in the late 1970s in arcades. It became the standard pre-loaded game on Nokia mobile phones in 1998. Compete for the highest score against other Butlerites.</p>
    <h2><a href="<?php echo site_url('game/brick'); ?>">Brick</a></h2>
    <p>A take on the classic Breakout game which was first developed in the early 1970s by Steve Jobs and Steve Wozniak.</p>
    <h2><a href="<?php echo site_url('game/missile'); ?>">Missile Command</a></h2>
    <p>Defend Durham from alien attack.</p>
    <h2><a href="<?php echo site_url('game/defend'); ?>">Beat the Defenders</a></h2>
    <p>Score a goal past the lines of defenders.</p>
</div>
<div class="content-right width-50 narrow-full">
    <div>
        <h2>What's on this Week?</h2>
        <?php $show_nothing = TRUE;
        $this->load->helper('smiley');
        for($i = 1; $i <= 7; $i++) {
            if(!empty($data[$i]['appt']) or !empty($data[$i]['signup'])) {
                $show_nothing = FALSE;
                echo wotw_open(ucfirst($data[$i]['day']));
                if($i == 1) {
                    echo wotw_box('#0a0', 'green', 'Green Tip of the Day', 'Green', '<div class="green">'.$tip.'</div>');
                }
                if(!empty($data[$i]['signup'])) {
                    foreach($data[$i]['signup'] as $d) {
                        echo wotw_box('#eeb300', 'signup/event/'.$d['id'], 'Sign up for '.$d['name'], date('H:i', $d['signup_opens']) , 'Sign up for '.$d['name'].' on the JCR Website.');
                    }
                }
                if(!empty($data[$i]['swapping'])) {
                    foreach($data[$i]['swapping'] as $d) {
                        echo wotw_box('#eeb300', 'signup/event/'.$d['id'], 'Swap seats at '.$d['name'], date('H:i', $d['swapping_opens']) , 'Swap seats at '.$d['name'].' on the JCR Website.');
                    }
                }
                if(!empty($data[$i]['appt'])) {
                    foreach($data[$i]['appt'] as $d) {
                        echo wotw_box('#c80000', 'events/view_event/'.$d['id'], $d['name'], (date('H:i', $d['time']) == '00:00' ? 'All Day' : date('H:i', $d['time'])), parse_smileys($d['description'], VIEW_URL.'common/smileys/'), $d['facebook_url'], $d['twitter_handle']);
                    }
                }
                echo '</div>';
            } elseif($i == 1) {
                echo wotw_open(ucfirst($data[$i]['day']));
                echo wotw_box('#0a0', 'green', 'Green Tip of the Day', 'Green', '<span class="green">'.$tip.'</span>');
                echo '</div>';
            }
        }
        if($show_nothing) echo '<h3>There are no events scheduled this week. '.anchor('events', 'See what\'s next in the calendar.').'</h3>';
        ?>
    </div>
</div>