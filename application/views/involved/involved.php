<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	$levels = array('committees'=>3, 'societies'=>9, 'sports'=>19, 'gym'=>62);
?>

<div id="in-nav" class="<?php echo $page;?> narrow-hide">
<ul class="nolist"><?php foreach($this->inv_pages as $p) echo '<li id="in-'.$p.'">'.anchor('involved/index/'.$p, ucfirst($p)).'</li>'; ?></ul>
</div>

<div id="involved-left" class="content-left width-33 narrow-full">
	<?php 
		echo '<div class="poster-outline">';
		if(isset($details['full'])){
			$this->load->view('involved/poster', array(
				'access_rights' => $access_rights,
				'details' => $details
			));
		}else{
			echo '<div class="jcr-box wotw-outer">';
			if(isset($levels[$page])){
				$this->load->view('utilities/users_contact', array(
					'level_ids'=>array($levels[$page]),
					'title_level'=>'h2',
					'title_before'=>'Your ',
					'title_after'=>':',
					'title_class'=>'wotw-day'
				)); 
			}
			echo '</div>';
		}
		echo '</div>';
	?>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day"><?php echo ucfirst($page); ?> at Butler:</h2>
		<?php if(is_admin()) { ?>
			<a href="<?php echo site_url('involved/manage/'.$page); ?>" class="jcr-button inline-block" title="Manage the sections on this page">
				<span class="ui-icon ui-icon-gear inline-block"></span>Manage
			</a>
		<?php } ?>
		<ul class="nolist soc-list">
			<?php foreach($sections as $s){ ?>
			<li class="invmenu">
				<?php echo anchor('involved/index/'.$page.'/'.$s['short'], '<p>'.$s['full'].'</p>', array('class' => 'no-jsify')); ?>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
<div id="involved-right" class="content-right width-66 narrow-full">
	<div id="involved-content-area">
	<?php $this->load->view('involved/get_content', array('access_rights' => $access_rights)); ?>
	</div>
</div>