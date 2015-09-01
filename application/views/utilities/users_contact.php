<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
	$users= $this->users_model->get_users_with_level($level_ids, 'levels.full,level_list.level_id,users.id,users.prefname,users.firstname,users.surname,users.email,users.uid');
	
	if(!isset($title_level)){
		$title_level = 'h2';
	}
	
	if(!isset($name_level)){
		$name_level = 'h3';
	}
	
	foreach($users as $u){
		$level[$u['level_id']]['name'] = $u['full'];
		$level[$u['level_id']]['users'][] = $u;
	}
	
	foreach($level_ids as $l_id){
		if(isset($level[$l_id])){
			$l = $level[$l_id];
			echo '<'.$title_level.' class="'.(isset($title_class)?$title_class:'').'">'.(isset($title_before)?$title_before:'').$l['name'].(isset($title_after)?$title_after:'').'</'.$title_level.'><div class="contact-profiles-outer">';
			foreach($l['users'] as $u){
				echo '<div class="contact-profiles">';
				echo '<div class="user-image" style="background-image:url('.get_usr_img_src($u['uid'], array('medium','large')).');"></div>';
				echo '<'.$name_level.'>'.($u['prefname']==''?$u['firstname']:$u['prefname']).' '.$u['surname'].' (<a href="mailto:'.$u['email'].'">contact</a>)</'.$name_level.'>';
				echo '</div>';
			}
			echo '</div>';
		}
 	}
?>