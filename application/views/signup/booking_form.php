<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(empty($e['swap_price'])) {
    $min_num_reservations = 1;
    $max_num_reservations = 4;
} else {
    $min_num_reservations = 2;
    $max_num_reservations = 2;
}

if($num_reservations > 0) : ?>
    <h3>Booking Details</h3>
    <?php
    echo $num_reservations.' reservation'.($num_reservations == 1 ? '' : 's'); ?> to complete, <?php
    $mins = floor(($res['reserved_until'] - time())/60);
    echo $mins.' min'.($mins == 1 ? '' : 's'); ?> remaining<br />
    Please use full names, not nick names.<br />
    Type the first few letters of the name, then select it from the drop-down list.<br />
    <?php eval(error_code()); ?>
    <ul id="reservation-details" class="nolist">
        <li id="signup-for">
            <label class="narrow-full">For</label>
            <select name="user_id">
                <option></option>
                <?php foreach($users as $u) echo '<option value="'.$u['id'].'" '.($errors ? set_select('user_id', $u['id']) : '').'>'.$u['name'].'</option>'; ?>
            </select>
        </li>
        <li id="signup-name">
            <label class="narrow-full">Name</label>
            <input type="text" name="name" value="<?php if($errors) echo set_value('name'); ?>" /><span> (if not found above)</span>
        </li>

        <?php foreach(array('starter', 'main', 'dessert', 'drink') as $course) :
            $c = explode(',',$e[$course.'s']);
            if(!empty($c[0])) : ?>
            <li>
                <label class="narrow-full"><?php echo ucfirst($course).'s'; ?></label>
                <?php
                if(count($c) == 1) echo $c[0];
                else {
                    echo '<select name="'.$course.'">';
                    foreach($c as $opt) {
                        $opt = trim($opt);
                        echo '<option value="'.$opt.'" '.($errors ? set_select($course, $opt) : '').'>'.$opt.'</option>';
                    }
                    echo '</select>';
                }
                ?>
            </li>
            <?php endif;
        endforeach;
        $pu = explode(',', $e['pickup']);
        if(!empty($pu[0])) : ?>
        <li>
            <!--
<label class="narrow-full">Pickup Location</label>
            <select name="pickup">
                <option value="None">No transport</option>
-->

		<label class="narrow-full">Liver in or out?</label>
            <select name="pickup">
                <?php
                foreach($pu as $opt) {
                    $opt = trim($opt);
                    echo '<option value="'.$opt.'" '.($errors ? set_select('pickup', $opt) : '').'>'.$opt.'</option>';
                }
                ?>
            </select>
        </li>
        <?php endif; ?>
        <li>
            <label class="narrow-full">Special requirements</label>
            <input type="text" name="special" value="<?php if($errors) echo set_value('special'); ?>" />
        </li>
        <li>
            <input type="hidden" name="details" value="1" />
            <label class="narrow-hide"></label><input type="submit" value="Save and continue" />
            <?php echo $token; ?>
        </li>
        <li>
            <label class="narrow-hide"></label><input id="signup-cancel" type="submit" name="cancel" value="Cancel remaining reservations" />
        </li>
    </ul>
<?php elseif((($e['num_user_reservations'] + $max_num_reservations) <= ($e['sets'] * $max_num_reservations)) OR $this->signup_model->check_permission($e)) : ?>
    <h3>Reserve seats</h3>
    <?php eval(error_code()); ?>
    Reserve seats for 15 minutes while you enter details.<?php if($e['sets'] > 1) echo ' You can make up to '.($e['sets'] - ceil($e['num_user_reservations']/$max_num_reservations)).' sets of '.$max_num_reservations.' reservations.'; ?><br />
    <select name="reserve">
        <?php for($i=$min_num_reservations; $i<=$max_num_reservations; $i++) echo '<option value="'.$i.'" '.($errors ? set_select('reserve', $i) : '').'>'.$i.'</option>'; ?>
    </select> seats<br/>
        <?php if(empty($e['swap_price'])) {
            echo ($e['group_type'] ? 'On table' : 'In group');
            if(!empty($e['table_names'])) $c = explode(',',$e['table_names']);?><select name="table">
        <?php if($e['id'] == 159){ ?>
            <option value="none">Please Select:</option>
        <?php }else{ ?>
            <option value="any">Any</option>
        <?php } ?>
        <?php $seats = explode(",", $e['seats']);
        foreach($e['tables'] as $num => $t) {
            if(count($t['seats']) < $seats[$num - 1]) echo '<option value="'.$num.'">'.$num.(empty($e['table_names']) ? '' : ': '.trim($c[($num)-1])).'</option>';
        }?>
    </select><?php } ?><br/>
    <input type="hidden" name="res" value="1" />
    <input type="submit" value="Reserve" />
    <?php echo $token; ?>
<?php
elseif($e['user_has_booking']): ?>
<h3>Seat Booked</h3>
<?php else: ?>
<h3>Seats Reserved</h3>
<?php endif; ?>