<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('game'); ?>

<div class="content-left width-66 narrow-full">
	<h2>Beat the Defenders</h2>
	<canvas id="defend-canvas" width="500" height="500" style="display: block; margin: 0 auto;"></canvas>
</div>
<div class="content-right width-33 narrow-full">
	<h2>Controls</h2>
	<div>
		<p>Select your opponents, hit the ball past the defenders and score a goal.</p>
		<ul class="nolist" id="shortcut-list">
			<li><span>&#8592;</span>Go left</li>
			<li><span>&#8594;</span>Go right</li>
			<li><span>SPACE</span>Pause</li>
		</ul>
	</div>
	<h2>Leaderboard</h2>
	<div>
		<ul class="nolist" id="leaderboard">
			<?php foreach($scores as $s) {
				echo '<li>'.$this->users_model->get_full_name($s['user_id']).': '.$s['score'].'</li>';
			}?>
		</ul>
		<p id="gameusername" style="display: none;"><?php echo $this->users_model->get_full_name($this->session->userdata('id')); ?></p>
	</div>
</div>