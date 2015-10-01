<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="wotw-outer jcr-box">
    <?php
        if(isset($details['full'])){
            echo '<h2 class="wotw-day">'.$details['full'].'</h2>';
        }elseif(isset($page)){
            echo '<h2 class="wotw-day">'.ucfirst($page).'</h2>';
        }
        
        if(is_null($details)){
            echo editable_area('involved', 'content/'.$page, $access_rights);
        }
        
        if(logged_in() && !empty($details['mailing'])) {
            echo '<p>Mailing List: '.anchor('involved/subscribe/'.$page.'/'.$section, 'Subscribe').' or '.anchor('involved/unsubscribe/'.$page.'/'.$section, 'Unsubscribe').'</p>';
        }
        
        if(!empty($details['cost'])) echo '<p>'.$details['full'].' membership cost is '.$details['cost'].'</p>';
        
        if(!empty($details['schedule'])) echo '<p>We meet '.$details['schedule'].'</p>';
        
        if(!empty($teams)) {
            foreach($teams as $t) {
                echo '<p>'.$t['team_name'].': <a href="https://www.teamdurham.com/collegesport/league/index.php?comp_id='.$t['comp_id'].'" target="_blank">League Table</a> &bull; <a href="https://www.teamdurham.com/collegesport/team/?team_id='.$t['team_id'].'" target="_blank">Fixtures and Results</a></p>';
            }
        }
        
        if(!empty($section) && !empty($page)) {
            log_message('error', $this->session->userdata('id').' - '.$access_rights);
            echo editable_area('involved', 'content/'.$page.'/'.$section, $access_rights);
        }
        if(!empty($details['associateexec'])){
    ?>
            </div>
            <div class="wotw-outer jcr-box">
    <?php    
            $this->load->view('utilities/users_contact', array(
                'level_ids'=>array($details['associateexec']),
                'title_level'=>'h2',
                'title_before'=>'Your ',
                'title_after'=>':',
                'title_class'=>'wotw-day'
            ));
        } 
    ?>
</div>