<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('photos/nav.php', array('tab' => 'photos')); ?>

<div>
	<h2>
		<?php if(empty($range)) echo 'There are no photos at the moment.';
			else {for($i = $range['min']; $i<= $range['max']; $i++) {
				echo $i == $year ?  $i.'-'.($i+1) : anchor('photos/index/'.$i, $i.'-'.($i+1));
				if($i < $range['max']) {
					echo ' &bull; ';
				}
			}
		} ?>
	</h2>
</div>

<?php if(!empty($events)) {
	foreach($events as $e) { ?>
		<div class="jcr-box">
			<h3><?php echo anchor('events/view_event/'.$e['id'], $e['name'].' - '.date('l jS F Y', $e['time'])); ?></h3>
			<?php if(!empty($e['description'])) echo '<p>'.$e['description'].'</p>';
			$this->load->view('events/photo_thumbs', array('photos' => $e['photos'])); ?>
		</div>
	<?php }
} else {
	echo '<h3>There are no photos for this academic year. Try a different one from the list above.</h3>';
}?>