<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('charities');

function name($users, $id){
    if(!is_null($id)){
        foreach($users as $u){
            if($u['id'] == $id){
                return $u['name'];
            }
        }
    }
    return '';
}
?>
<div class="content-left narrow-full">
    <?php
        $this->load->view('events/event_info', array('event' => $event));
    ?>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Dare Night Submission</h2>
        <div>
            <?php
                if(isset($team[0])){
                    $this->load->view('charities/dare_night/multiple_entries');
                }elseif($team['submitted']){
                    $this->load->view('charities/dare_night/dare_submitted');
                }else{
                    if(time() < $event['time'] and !is_admin()){
                        $this->load->view('charities/dare_night/dare_waiting');
                    }elseif(time() < $info['submission_deadline']){
                        $this->load->view('charities/dare_night/dare_open');
                    }else{
                        $this->load->view('charities/dare_night/dare_closed');
                    }
                }
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
