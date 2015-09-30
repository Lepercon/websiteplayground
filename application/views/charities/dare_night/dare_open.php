<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<p class="javascript-warning">You don't seem to have javascript enabled, you can try to use this page without it, but you may not get very far. We recomend you use another browser, or enable javascript in your current one.</p>
<div id="accordion" class="submit-dare" role="tablist">
    <h3 class="instructions">Instructions</h3>
    <div>
        <?php 
            if($event['time'] > time()){
        ?>
                <p style="color:red;">Note: You are viewing this as a preview, this page won't go live until after the event.</p>
        <?php
            }
        ?>
        <p><?php echo $info['details']; ?></p>
        <p>What You Need To Do:</p>
        <ol style="padding-left:25px;">
        <li>Enter your team infomation on the tab below.</li>
        <li>Upload a photo for each of the dares you have completed below.</li>
        <li>Click "Submit Everything" on the final tab.</li>
        <li>Click "Confirm" on the next page.</li>
        <li>Celebrate because you've done something awesome for charity!</li>
        <li>Join our <a href="http://www.facebook.com/groups/butlercharitycomm/">facebook group</a>.</li>
        </ol>
        <p>Submission Deadline: <b><?php echo date('g:ia', $info['submission_deadline']).'</b> on <b>'.date('l jS F Y', $info['submission_deadline']); ?></b>.</p>
    </div>
    <h3 class="team">Your Team</h3>
    <div class="active">
        <div class="<?php echo ($tab == 'team'?'validation_success':''); ?>" id="team-form-helper"><?php echo ($tab == 'team'?$message:''); ?></div>
        <p>Please enter the details of your team:</p>
        <p id="no-safari" style="display:none;color:red">Please note, team name entry is not supported on your browser (Safari), please use an alternative.</p>
        <div class="inline-block content-left width-50 narrow-full">
        <?php
            echo form_open('charities/dare_night/'.$info['id'].'/'.$team['id'], array('id'=>'dare-team-info', 'class'=>'jcr-form no-jsify'));                        
        ?>
            <datalist id="fullnameslist">
        <?php
            foreach($users as $u){?>
                <option id="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></option><?php
                if($u['id'] == $u_id){
                    $users_name = $u['name'];
                }
            }
        ?>
            </datalist>
        <?php
            echo form_hidden(array('submit_type'=>'team_info', 'team_id'=>$team['id']));
            echo form_label('Team Name:');
            echo form_input(array('name'=>'teamnanme', 'id'=>'teamname', 'placeholder'=>'Team Name', 'value'=>$team['team_name'])).'<br>';
            
            $team_info_done = ($team['team_name'] !== '');
            for($i=1;$i<=6;$i++){
                echo form_label('Team Member '.$i.':');
                echo form_input(array('name'=>'member-'.$i, 'id'=>'member-'.$i, 'placeholder'=>'Team Member '.$i, 'value'=>name($users, $team['team_member_'.$i]), 'list'=>'fullnameslist'));
                echo form_input(array('name'=>'member-'.$i.'-hidden', 'id'=>'member-'.$i.'-hidden', 'type'=>'hidden')).'<br>';
                $team_info_done = $team_info_done && !is_null($team['team_member_'.$i]);
            }

            echo form_label('');
            echo form_submit('team-info-submit', 'Submit Team Info');
            
            echo form_close();            
        ?>
        </div>
        <div class="inline-block content-right width-50 narrow-full">
            <p>Once you have submitted your team info, send your team mates this link and they can upload their own photos:</p>
            <a href="<?php echo site_url('charities/dare_night/'.$info['id'].'/'.$team['id']); ?>"><?php echo site_url('charities/dare_night/'.$info['id'].'/'.$team['id']); ?></a>
            <p>Once you have all uploaded your photos, one person will need to click submit button below, where you can then review, and then submit your entry.</p>
        </div>
    </div>
    <?php
        $dare_list = explode(';;',$info['dares'], -1);
        $i = 0;    
        $count = 0;                
        foreach($dare_list as $d){
            $sep = explode(';',$d);
            $complete = isset($team['photos'][++$i]);
            $count += $complete;
    ?>
            <h3 class="dare-<?php echo $i ?>"><?php echo $i.'. '.$sep[0];?> - <?php echo ($complete?'Complete':'Incomplete'); ?></h3>
            <div>
                <div class="<?php echo ($tab == ('dare-'.$i)?($success?'validation_success':'validation_errors'):''); ?>" id="dare-<?php echo $i; ?>-form-helper"><?php echo (('dare-'.$i) == $tab?('<span class="green-icon ui-icon ui-icon-'.($success?'check':'notice').' inline-block"></span>'.$message):''); ?></div>
                <p><?php echo $sep[1]; ?></p>
                <div class="content-left width-66">
                    <?php
                        echo form_open_multipart(site_url('charities/dare_night/'.$info['id'].'/'.$team['id']), array('id'=>'dare'.$i.'info', 'class'=>'jcr-form no-jsify'));                                                
                        
                        echo form_hidden(array('submit_type'=>($complete?'dare_evidence_details':'dare_evidence'), 'dare_num'=>$i, 'team_id'=>$team['id']));
                        echo form_label('Details:', 'file-'.$i.'-upload', array('style'=>'vertical-align:top!important'));
                        echo form_textarea(array('name'=>'details', 'id'=>'dare-'.$i.'-details', 'value'=>($complete?$team['photos'][$i]['details']:''), 'placeholder'=>'Details about this submisssion.')).'<br>';
                        
                        if($complete){
                    
                        }else{
                            echo form_label('Upload Image:');
                            echo form_upload(array('name'=>'userfile'.$i)).'<br>';
                        }
                        
                        echo form_label();
                        echo form_submit('dare-'.$i.'-submit', ($complete?'Update Details':'Upload Photo '.$i));
                        
                        echo form_close();

                    ?>
                    <p>Make sure you cick the upload button on each tab.</p>
                </div>
                <div class="content-right width-33">
                    Your Submission:<br>
                    <img class="evidence-image" src="<?php echo site_url('application/views/charities/dare_night/'.($complete?'images/'.$team['photos'][$i]['file_name']:'qm.png')); ?>">
                    <?php 
                        if($complete){
                            echo form_open('charities/dare_night/'.$info['id'].'/'.$team['id'], array('id'=>'dare-delete-photo', 'class'=>'jcr-form no-jsify dare-delete-photo'));
                            echo form_hidden(array('submit_type'=>'dare_delete_photo', 'dare_num'=>$i, 'team_id'=>$team['id']));
                            echo anchor('', 'Delete Photo', 'class="delete-photo-button no-jsify"');
                            echo form_close();
                        }
                    ?>        

                </div>
            </div>
    <?php
        }
    ?>
    <h3 class="submission">Submission</h3>
    <div>
        <p>Once you are sure you have submitted everything, click the 'Submit' button below. Good Luck!</p>
        <p>You have <?php echo $team_info_done?'':'<b>not</b> '; ?>completed your team info.</p>
        <p>You have completed <b><?php echo $count; ?></b> out of <b><?php echo $i; ?></b> submissions.</p>
        <?php
            echo form_open('charities/dare_night/'.$info['id'].'/'.$team['id'], array('id'=>'dare-submit', 'class'=>'jcr-form no-jsify'));
            echo form_hidden(array('submit_type'=>'submit_all', 'team_id'=>$team['id']));
            echo form_submit('dare-night-submit', 'Submit Everything');
            echo form_close();
        ?>
    </div>
</div>
