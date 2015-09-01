<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="cal-title">
	<a class="inline-block" id="cal-left" href="<?php echo site_url('events/index/'.$data['prev']['year'].'/'.$data['prev']['month']); ?>" rel="nofollow"></a>
	<h2 class="inline-block" ><?php echo date('F Y', mktime(0,0,0,$data['month'],1,$data['year'])); ?></h2>
	<a class="inline-block" id="cal-right" href="<?php echo site_url('events/index/'.$data['next']['year'].'/'.$data['next']['month']); ?>" rel="nofollow"></a>
</div>

<div>
	<?php $current_year = date('Y');
	$current_month = date('n');
	if($current_month != $data['month'] OR $current_year != $data['year']) {
		$arrow = 'w';
		if($current_year > $data['year'] OR ($current_month > $data['month'] && $current_year == $data['year'])){
			$arrow = 'e';
		} ?>
		<a class="jcr-button inline-block" title="Go to the current month" href="<?php echo site_url('events/index/'.$current_year.'/'.$current_month); ?>">
			<span class="inline-block ui-icon ui-icon-arrowthick-1-<?php echo $arrow; ?>"></span>Today
		</a>
	<?php }?>
	<a class="jcr-button inline-block" title="Subscribe to the JCR Calendar" href="<?php echo site_url('events/ical'); ?>">
		<span class="inline-block ui-icon ui-icon-calendar"></span>Subscribe
	</a>
	<a class="jcr-button inline-block" title="Guide: How To Make Your Own Event" href="<?php echo site_url('events/event_guide'); ?>">
		<span class="inline-block ui-icon ui-icon-note"></span>How To: Make Your Own Event
	</a>
	<?php if(has_level('any')) { ?>
		<a class="jcr-button inline-block" title="Add an event" href="<?php echo site_url('events/add_event'); ?>">
			<span class="inline-block ui-icon ui-icon-plus"></span>Add Event
		</a>
	<?php } ?>
</div>

<?php $month_list = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'); ?>

<table id="cal">
	<tr>
		<?php foreach(array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') as $day) echo '<th>'.substr($day, 0, 1).'<span class="narrow-hide">'.substr($day, 1).'</span></th>'; ?>
	</tr>
	<?php
	foreach($data['data'] as $row): ?>
	<tr>
		<?php foreach($row as $cell) { ?>
		<td <?php if(!empty($cell['class'])) echo 'class="'.$cell['class'].'"'; ?>>
			<div class="date_num"><?php echo $cell['date']; ?><?php if($cell['date'] === 1) echo '<span class="narrow-hide inline-block">&nbsp;'.$month_list[$cell['month'] - 1].'</span>';?>
				<?php if(has_level('any') && mktime(23,59,59,$cell['month'],$cell['date'],$cell['year']) >= time()) { ?>
					<a class="event-add-day" href="<?php echo site_url('events/add_event?start='.$cell['date'].'-'.$cell['month'].'-'.$cell['year'].'&finish='.$cell['date'].'-'.$cell['month'].'-'.$cell['year']); ?>">
						<span class="ui-icon ui-icon-plus"></span>
					</a>
				<?php } ?>
			</div>
			<?php foreach($cell['appts'] as $appt) {				
				$time = (date('H:i', $appt['time']) == '00:00' ? 'Day' : date('H:i', $appt['time'])); ?>
				<a href="<?php echo site_url('events/view_event/'.$appt['id']); ?>" class="apt<?php echo ((isset($appt['hidden']) && $appt['hidden'])?' hidden-event':''); ?>" <?php echo (empty($appt['description']) ? '' : ' title="'.$time.': '.$appt['description'].'"');?>>
					<?php echo $time; ?><span class="narrow-hide"><?php echo $appt['name']; ?></span>
				</a>
			<?php } ?>
		</td>
		<?php } ?>
	</tr>
	<?php endforeach; ?>
</table>