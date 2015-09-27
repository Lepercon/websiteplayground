<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('whoswho/banner');
?>

<div id="ww-nav" value="<?php echo $page; ?>" class="<?php echo $page; ?>">
	<ul class="nolist">
		<li id="ww-exec">
			<?php echo anchor('whoswho/index/exec', 'Exec'); ?>
		</li><li id="ww-assistants">
			<?php echo anchor('whoswho/index/assistants', 'Exec Officio & Assistants', 'class="nav-two-line"'); ?>
		</li><li id="ww-sports">
			<?php echo anchor('whoswho/index/sports', 'Sports Captains', 'class="nav-two-line"'); ?>
		</li><li id="ww-societies">
			<?php echo anchor('whoswho/index/societies', 'Society Presidents', 'class="nav-two-line"'); ?>
		</li><li id="ww-committees">
			<?php echo anchor('whoswho/index/committees', 'Committee Chairs', 'class="nav-two-line"'); ?>
		</li><li id="ww-support">
			<?php echo anchor('whoswho/index/support', 'Student Support', 'class="nav-two-line"'); ?>
		</li><li id="ww-services">
			<?php echo anchor('whoswho/index/services', 'Services'); ?>
		</li><li id="ww-staff">
			<?php echo anchor('whoswho/index/staff', 'College Staff'); ?>
		</li>
	</ul>
</div>
<div id="narrow-menu">
	<h2>Who's Who?</h2>
	<?php
		$sections = array(
			'exec' => 'Exec',
			'assistants' => 'Exec Officio & Assistants',
			'sports' => 'Sports Captains',
			'societies' => 'Society Presidents',
			'committees' => 'Committee Chairs',
			'support' => 'Student Support',
			'services' => 'Services',
		);
		$names = array('exec'=>'Exec', 'sports'=>'Sports Presidents', 'societies'=>'Society Presidents', 'committees'=>'Committee Chairs', 'assistants'=>'Assistants', 'support'=>'Support', 'services'=>'Bar, Kitchen, Coffee Shop', 'staff'=>'College Staff');
		
		foreach($sections as $k => $s){
			echo anchor('whoswho/index/'.$k, '<p>'.$s.'</p>', ($page==$k)?'class="selected"':'');
		}	
	?>
</div>
<div class="jcr-box wotw-outer whoswho-box">
	<h3 class="wotw-day">Your <?php echo $names[$page]; ?>:</h3>
	<?php echo editable_area('whoswho', 'content/'.$page, $access_rights); ?>
	<div id="whoswho-icons">
	<?php
	if($access_rights) { ?>
		<a href="" class="jcr-button inline-block" id="whoswho-order" title="Admin: Change Order">
			<span class="ui-icon ui-icon-gear inline-block"></span>Change order
		</a>
                <a href="<?php echo site_url('whoswho/print_profiles'); ?>" class="jcr-button inline-block">
			<span class="ui-icon ui-icon-print inline-block"></span>Printable Version
		</a>
	<?php } ?>
	
	<ul id="whoswho" class="nolist">
	<?php foreach($all_whoswho as $who){ ?><li class="inline-block">
		<div class="whoswho-id"><?php echo $who['id']; ?></div>
		<a class="no-jsify whoswho-jsify" href="<?php echo site_url('whoswho/index/'.$page.'/'.$who['id']);?>"><div style="background-image: url(<?php echo get_usr_img_src($who['uid'], array('medium', 'small')); ?>);"></div>
			<p><b><?php echo user_pref_name($who['firstname'], $who['prefname'], $who['surname']); ?></b></p>
			<p><?php echo $who['title']; ?></p>
		</a>
	</li><?php } ?>
	</ul>
	</div><div id="whoswho-details"><div id="whoswho-mem-content"><?php if(!empty($mem)) $this->load->view('whoswho/users', array('mem' => $mem)); ?></div></div>

</div>
