<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup/event/'.$e['id']).print_link();
?>
<br /><?php echo anchor('signup/food_choices/'.$e['id'].'/1', 'Download as CSV for use in Excel', 'class="no-jsify no-print"'); ?>
<br /><br />
<table id="signup-food-choices-table">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Table Num</th>
        <th>Starter</th>
        <th>Main</th>
        <th>Dessert</th>
        <th>Drink</th>
        <th>Special requirements</th>
        <th>Pickup</th>
        <th>Booked by</th>
    </tr>
    <?php foreach($reservations as $r) : ?>
        <tr>
            <td><?php
            if(empty($r['uname'])) {
                $name = explode(' ',$r['name']);
                $last = array_pop($name);
                $name = $last.(empty($name) ? '' : ', '.implode(' ', $name));
            }
            else $name = $r['uname'];
            echo $name; ?></td>
            <?php foreach(array('email', 'table_num', 'starter', 'main', 'dessert', 'drink', 'special', 'pickup', 'booked_by') as $t) echo '<td>'.$r[$t].'</td>';?>
        </tr>
    <?php endforeach; ?>
</table>