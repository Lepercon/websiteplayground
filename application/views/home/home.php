<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="content-left width-66 narrow-full">
    <?php echo editable_area('home', 'content', $access_rights); ?>
    <div class="home-feed">
        <?php if(has_level('any')){ $this->load->view('home/new_status'); } ?>
        <?php 
            $show_nothing = TRUE;
            if(sizeof($posters) > 2){
                $this->load->view('home/poster_banner');
            }elseif($access_rights){
        ?>
                <div class="jcr-box">
                    <p>At least 3 events, out of the ones in the next 4 weeks, require posters for any to be displayed on the homepage. Make sure you upload any posters to the <a href="<?php echo site_url('events') ?>">calendar</a>.</p>
                </div>
        <?php
            }
        $this->load->helper('smiley');
        $this->load->view('green/totd', array('tip'=>$tip));
        echo '<h3>What\'s On This Week?</h3>';
        for($i = 1; $i <= 7; $i++) {
            //if(!empty($data[$i]['appt']) or !empty($data[$i]['signup'])) {
            if(!empty($data[$i]['signup'])) {
                $show_nothing = FALSE;
                foreach($data[$i]['signup'] as $d) {
                    $this->load->view('events/signup_info', array('signup'=>$d));
                }
            }
            if(!empty($data[$i]['swapping'])) {
                $show_nothing = FALSE;
                foreach($data[$i]['swapping'] as $d) {
                    $this->load->view('events/swapping_info', array('swap'=>$d));
                }
            }
            if(!empty($data[$i]['appt'])) {
                $show_nothing = FALSE;
                foreach($data[$i]['appt'] as $d) {
                    $this->load->view('events/event_info', array('event'=>$d));
                }
            }
            //}
        }
        if($show_nothing) echo '<h3>There are no events scheduled this week. See what\'s next in '.anchor('events', 'the calendar').'.</h3>';
        ?>
    </div>
    <?php //<div class="jcr-box wotw-outer"> ?>
        <h3 class="<?php //wotw-day?>">Latest News</h3>
        <?php foreach($news as $post) {
            $this->load->view('events/post', array('post' => $post, 'show_link' => TRUE));
        } ?>
    <?php //</div> ?>
</div>
<div class="content-right width-33 narrow-hide">
    <?php $this->load->view('home/slideshow'); ?>
    <div class="jcr-box">
        <ul class="nolist">
            <li>
                <a class="sprite-container"  href="http://www.facebook.com/groups/274250999315976/" target="_blank">
                    <div class="common-sprite" id="sprite-facebook"></div>
                    <div class="inline-block"><p>The JCR on Facebook</p></div>
                </a>
            </li><li>
                <a class="sprite-container"  href="http://twitter.com/butlerjcr" target="_blank">
                    <div class="common-sprite" id="sprite-twitter"></div>
                    <div class="inline-block"><p>Follow @butlerjcr</p></div>
                </a>
            </li><li>
                <a class="sprite-container"  href="http://www.dur.ac.uk/butler.college/" target="_blank">
                    <div class="common-sprite" id="sprite-butler"></div>
                    <div class="inline-block"><p>Butler College</p></div>
                </a>
            </li><li>
                <a class="sprite-container"  href="http://issuu.com/moundmagazine" target="_blank">
                    <div class="common-sprite" id="sprite-mound"></div>
                    <div class="inline-block"><p>Mound Magazine</p></div>
                </a>
            </li><li>
                <a class="sprite-container"  href="http://butlerscholarlyjournal.com/" target="_blank">
                    <div class="common-sprite" id="sprite-butler"></div>
                    <div class="inline-block"><p>Scholarly Journal</p></div>
                </a>
            </li>
        </ul>
    </div>
    <div class="jcr-box">
        <iframe width="306" height="172" src="https://www.youtube.com/embed/JmIgny_FvQs" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="jcr-box no-padding-box">
        <a class="twitter-timeline" href="https://twitter.com/butlerjcr" data-widget-id="250249192499453952">Tweets by @butlerjcr</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </div>
    <div class="jcr-box">
        <h2 class="center wotw-day">Mound Magazine</h2>
        <?php
            if(logged_in()) echo '<div data-configid="8576845/7245660" style="width: 300px; height: 213px;" class="issuuembed center"></div><script type="text/javascript" src="//e.issuu.com/embed.js" async="true"></script>';
            else echo '<p class="center">You must login to view Mound Magazine</p>';
        ?>
    </div>
</div>