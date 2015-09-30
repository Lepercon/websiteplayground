<?php
    
    echo form_label('Team Name:').$team['team_name'].'<br><br>';
    echo form_label('Members:').'1. '.$this->users_model->get_full_name($team['team_member_1']).'<br>';
    echo form_label('').'2. '.$this->users_model->get_full_name($team['team_member_2']).'<br>';
    echo form_label('').'3. '.$this->users_model->get_full_name($team['team_member_3']).'<br>';
    echo form_label('').'4. '.$this->users_model->get_full_name($team['team_member_4']).'<br>';
    echo form_label('').'5. '.$this->users_model->get_full_name($team['team_member_5']).'<br>';
    echo form_label('').'6. '.$this->users_model->get_full_name($team['team_member_6']).'<br><br>';
    
    $dare_list = explode(';;',$info['dares'], -1);
    $i = 0;
?>
<table class="dare-review-table"><tr>
<?php        
    foreach($dare_list as $d){
        $sep = explode(';',$d);
        $complete = isset($team['photos'][++$i]);
        if($i % 3 == 1){
?>
            </tr><tr>
<?php
        }
?>
        <td>
            <h3><?php echo $i.'. '.$sep[0]; ?></h3>
            <?php echo $complete?'<a href="'.site_url('application/views/charities/dare_night/images/'.$team['photos'][$i]['file_name']).'">':''; ?>        
            <img class="evidence-image" src="<?php echo site_url('application/views/charities/dare_night/'.($complete?'images/'.$team['photos'][$i]['file_name']:'qm.png')); ?>">
            <?php echo $complete?'</a>':''; ?>
            <p><?php echo $sep[1]; ?></p>
            <?php
                if($complete){
            ?>
                    <p><?php echo $team['photos'][$i]['details']; ?></p>
            <?php
                }
            ?>
        </td>
<?php        
    }
?>
</tr></table>