<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="jcr-box wotw-outer poster-banner">
	<h3 class="wotw-day">What's On This Week?</h3>
	<div>
		<div class="poster-outer-box">
			<div class="poster-box"><?php
					foreach($posters as $p){
						?><span class="poster-image"><a href="<?php echo site_url('events/view_event/'.$p['id']); ?>"><?php
							echo img(array(
								'src'=>'application/views/events/posters/'.$p['event_poster'],
								'alt' => $p['name'].' - '.$p['description'],
								'title' => $p['name'].' - '.$p['description'],
								'class' => 'event-scroll-image'
							));
						?></a><span id="image-width" style="display:none;"><?php
							$info = getimagesize('application/views/events/posters/'.$p['event_poster']);
							echo $info[0]/$info[1]; 
						?></span></span><?php	
					}
				?></div>
		</div>
	</div>
</div>
<span id="image-height" style="display:none;"><?php echo isset($_GET['height'])?$_GET['height']:600; ?></span>