<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('charities');
?>
<div class="content-left width-66 narrow-full">
<div class="jcr-box wotw-outer">
    <h2 class="wotw-day">Dare Night</h2>
    <div>
        <p>You can see the information below about upcoming dare night events. You can click on the 'Submissions' button to submit your photos in order to enter the compertition.</p>
    </div>
</div>
<?php
    foreach($dare_nights as $d){
        $extra_text = '<a class="jcr-button inline-block" title="Submit Your Dare Night Entry" href="'.site_url('charities/dare_night/'.$d['id']).'">Submissions</a>';
        $this->load->view('events/event_info', array('event' => $d['event_info'], 'extra_text'=>$extra_text));
    }
    
    $this->load->view('charities/charities_contact');
?>
</div>
<div class="content-right width-33 narrow-full">
<?php
    $this->load->view('charities/charities_intro');
?>
</div>