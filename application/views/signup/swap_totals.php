<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup/event/'.$e_id);
echo print_link();
$count = 0;
echo '<h2>Total Swaps per Person</h2>';
echo '<ul class="nolist">';
foreach($swaps as $s) {
    echo '<li>'.$s['name'].': '.$s['number'].'</li>';
    $count = $count + $s['number'];
}
echo '</ul>';
echo '<h3>'.$count.' swaps in total</h3>';
?>
