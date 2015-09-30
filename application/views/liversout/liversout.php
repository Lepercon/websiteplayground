<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Living Out</h2>
        <div>
            <?php echo anchor('liversout/tenancy_advice', '<h2>Tenancy Advice</h2>'); ?>
            <p>Advice for living out.</p>
            <?php echo anchor('liversout/proctors', '<h2>Proctors</h2>'); ?>
            <p>Information and contact details for all the Livers Out proctor team.</p>
            <?php echo anchor('liversout/house_hunting', '<h2>House Hunting</h2>'); ?>
            <p>When and where to start looking.</p>
            <?php echo anchor('liversout/resources', '<h2>Campaigns</h2>'); ?>
            <p>Digital versions of all the guides and leaflets.</p>
            <a href="http://www.dur.ac.uk/butler.college/local/current/housekeeping/"><h2>Living In</h2></a>
            <p>Housekeeping Notification Form.</p>
            <!--<?php echo anchor('liversout/submit_housing_info', '<h2>Submit Housing Info</h2>'); ?>
            <p>Once you have decided where you are living you need to submit your housing info to the livers out officer here.</p>-->
        </div>
    </div>
    <?php if(!empty($events)) { ?>
        <div>
            <h2 >Next Livers Out Events</h2>
            <?php foreach($events as $e) {
                $this->load->view('events/event_info', array('event' => $e));
                echo '<hr />';
            } ?>
        </div>
    <?php } ?>
    <?php $this->load->view('liversout/liversout_contact'); ?>
</div>
<div class="content-right width-33 narrow-full">
    <?php $this->load->view('liversout/search_form');?>
    <div class="jcr-box wotw-outer sponsor-container">
        <h2 class="wotw-day">Sponsor</h2>
        <a href="http://www.universalstudentliving.com/properties/durham/"><img src="<?php echo site_url('application/views/liversout/img/usllogo.png'); ?>" alt="Universal Student Living"></a>
    </div>
</div>
<div id="map_container" style="margin:0;width:100%;height:500px;"></div>