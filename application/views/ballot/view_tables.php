<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<div>
<?php
ksort($tables);
//echo nl2br(var_export($tables, true));
$tab = explode(';', $b['tables']);
$show_tables = true;

foreach($tables as $k=>$t){
	$show_tables = $show_tables && ($tab[$k-1]-count($t))==0;
}
$show_tables = $show_tables || $b['published'];

if($show_tables){
	$i=1;
?>
	 </div>
	<div id="tables-section">
	<div class="tables">
	<?php foreach($tables as $k=>$t){ ?>
		<div>
			<p><b><?php echo ($b['close_time'] > time())?'Group '.$i++:'Table '.$k; ?>:</b></p>
			<?php foreach($t as $key=>$p){ ?>
				<p><?php echo ($key+1).'. '.($p['firstname']==''?$p['name']:($p['prefname']==''?$p['firstname']:$p['prefname']).' '.$p['surname']).($p['user_id']==-1?' ('.($p['pn']==''?$p['fn']:$p['pn']).' '.$p['sn'].')':''); ?></p>
			<?php } ?>
		</div>
	<?php } ?>
	</div>
	</div>

<?php
	}else{
		echo '<p>We seem to have had a problem working out who to put on each table, and we want a human to check it before we publish it.</p>';
		echo '<p>We\'re very sorry about this and we hope it get resolved soon.</p>';
	}
?>