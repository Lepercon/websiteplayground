<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

	$sections = array('exec'=>'Exec', 'assistants'=>'Assistants', 'sports'=>'Sports Presidents', 'societies'=>'Society Presidents', 'committees'=>'Committee Chairs', 'support'=>'Support', 'services'=>'Bar, Kitchen, Coffee Shop', 'staff'=>'College Staff');
	
?>
<h1 style="text-transform:none;font-size:35px;text-align:center;">History of Josephine Butler College JCR</h1>
<div class="width-33 narrow-full content-left list-of-roles">
<?php 
	$ro = array();
	foreach($sections as $k=>$s){
		echo '<h2>'.$s.'</h2>';
		foreach($roles[$k] as $kk=>$r){
			if($cr == $kk){
				$ro = $r;
			}
			echo '<a href="'.site_url('whoswho/history/'.$kk).'" class="no-jsify"><h3>'.$r[0]['full'].'</h3></a>';
			echo '<div>';
			echo '<p>'.$r[0]['description'].'</p>';
			foreach($r as $p){
				$photo = get_usr_img_src($p['uid'],'large');
				echo '<hr><div style="display:block"><h3 value="'.$photo.'">'.($p['prefname']==''?$p['firstname']:$p['prefname']).' '.$p['surname'].' ('.$p['year'].'/'.($p['year']+1).')</h3>';
				echo '<p>'.$p['level_desc'].'</p>';
				echo '</div>';
			}
			echo '</div>';
		}
	}

?>
</div>

<div class="width-66 narrow-full content-right">
<p class="top-link"><a href="#">^^ Back to the top ^^</a></p>
<div class="role-description">
	<?php if(empty($ro)){ ?>
		<p style="margin-left:-10px;">Click a role on the left hand side to find out more about it.</p>
	<?php }else{ ?>
		<h2><?php echo $ro[0]['full']; ?></h2>
		<p><?php echo $ro[0]['description']; ?></p>
		<?php 
			foreach($ro as $p){
				$photo = get_usr_img_src($p['uid'],'large');
				echo '<hr><div style="display:block"><h3>'.($p['prefname']==''?$p['firstname']:$p['prefname']).' '.$p['surname'].' ('.$p['year'].'/'.($p['year']+1).')</h3>';
				echo '<img src="'.$photo.'"/>';
				echo '<p>'.$p['level_desc'].'</p>';
				echo '</div>';
			}
		?>
	<?php } ?>
</div>
<?php if(logged_in()){ ?>
	<p>If you believe there are any mistakes or that there is anything missing on this page, please <a href="#" class="report-link">click here</a>.</p>
	<div id="report-form" style="display:none;">Please help us:<br><textarea class="text-area" style="width:450px;min-height:200px;"></textarea></div>
<?php } ?>
</div>