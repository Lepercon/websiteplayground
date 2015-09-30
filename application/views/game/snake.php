<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('game'); ?>

<div class="content-left width-50 narrow-full">
    <h2>Snake</h2>
    <canvas id="snake-canvas" width="300" height="300" style="display: block; margin: 0 auto;"></canvas>
</div>
<div class="content-right width-50 narrow-full">
    <h2>Controls</h2>
    <div>
        <ul class="nolist" id="shortcut-list">
            <li><span>&#8592;</span>Go left</li>
            <li><span>&#8594;</span>Go right</li>
            <li><span>&#8593;</span>Go up</li>
            <li><span>&#8595;</span>Go down</li>
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