<p>You have multiple dare night entries, please select the one you would like to use:</p>
<ol style="padding-left:40px">
<?php
    foreach($team as $t){
?>
        <li>
            Team Name: <b><?php echo $t['team_name']; ?></b><br>
            Members:<br>
            1. <b><?php echo $this->users_model->get_full_name($t['team_member_1']); ?></b><br>
            2. <b><?php echo $this->users_model->get_full_name($t['team_member_2']); ?></b><br>
            3. <b><?php echo $this->users_model->get_full_name($t['team_member_3']); ?></b><br>
            4. <b><?php echo $this->users_model->get_full_name($t['team_member_4']); ?></b><br>
            Dares Submitted: <b><?php echo sizeof($t['photos']).'/'.sizeof(explode(';;', $info['dares'])); ?></b><br>
            <a href="<?php echo site_url('charities/dare_night/'.$info['id'].'/'.$t['id']); ?>" class="inline-block jcr-button">Choose This Submission</a>

        </li>
<?php        
    }
?>
</ol>