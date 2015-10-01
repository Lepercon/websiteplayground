<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('charities');
?>
<div class="content-left narrow-full">
    <?php
        $this->load->view('events/event_info', array('event' => $event));
    ?>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Dare Night Submission</h2>
        <div>
            <p>Please review your submission, <b>you will need to confirm</b> at the bottom of this page:</p>
            <p>Please note: you are submitting on behalf of the team, so only do this once all of your photos have been uploaded.</p><br>
            <?php
                echo form_open('charities/dare_night/'.$info['id'].'/'.$team['id'], array('id'=>'dare-submit-confirm', 'class'=>'jcr-form no-jsify dare-submit-confirm'));
                $this->load->view('charities/dare_night/dare_table');
                echo form_hidden(array('submit_type'=>'darenight_submit_confirm', 'team_id'=>$team['id']));
                
                echo form_submit('dare-night-submit-confirm', 'Confirm Submission');
                echo form_submit('dare-night-submit-confirm', 'Cancel Submission');
                
                echo form_close();
            ?>
        </div>
    </div>
    <?php 
        $this->load->view('charities/charities_contact'); 
        $this->load->view('charities/dare_night/admin');         
    ?>

</div>
<span id="tab_open" style="display:none;"><?php echo $tab; ?></span>
<span id="team_number" style="display:none;"><?php echo isset($team['id'])?$team['id']:''; ?></span>
