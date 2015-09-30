<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// set opt
function so($a,$b) {
    return ($a == $b ? 'selected="selected"':'');
}

function sc($a,$b) {
    return ($a == $b ? 'checked="checked"':'');
}

$hour = array();
$minute = array();
for($i = 0; $i <= 23; $i++) $hour[$i] = sprintf('%02d', $i);
for($i = 0; $i <= 55; $i+=5) $minute[$i] = sprintf('%02d', $i);

$signup_type = array(
                  '1'  => 'Formal',
                  '2'    => 'Swap Formal',
                  '3'   => 'Coaches Signup',
                  '4' => 'T-Shirt Signup',
                  '5' => 'Event Signup',
                );
?>

<div class="jcr-box">
    <h2>Signup options</h2>
    <ul class="nolist">
        <li>
            <label for="name">Signup Type</label>
            <?php echo form_dropdown('type', $signup_type,  (isset($e) ? $e['type'] : '1')); ?>
        </li>
        <li>
            <label for="name">Signup Name</label><?php echo form_input(array(
                'name' => 'name',
                'id' => 'name',
                'class' => 'input-help',
                'placeholder' => 'Signup Name',
                'maxlength' => '50',
                'required' => 'required',
                'title' => 'Required field. Maximum 50 characters.',
                'value' => set_value('name', (isset($e) ? $e['name'] : ''))
            )); ?>
        </li>
        <li>
            <label for="dress-code">Dress Code</label><?php echo form_input(array(
                'name' => 'dress_code',
                'id' => 'dress-code',
                'class' => 'input-help',
                'placeholder' => 'Dress Code',
                'maxlength' => '50',
                'title' => 'Optional field. Maximum 50 characters. If applicable, state if gowns should be worn.',
                'value' => set_value('dress_code', (isset($e) ? $e['dress_code'] : ''))
            )); ?>
        </li>
        <li>
            <label for="price">Price</label><?php echo form_input(array(
                'name' => 'price',
                'id' => 'price',
                'class' => 'input-help',
                'placeholder' => 'Price',
                'maxlength' => '50',
                'title' => 'Optional field. Maximum 50 characters. Can be a number or text, eg \'Free\'',
                'value' => set_value('price', (isset($e) ? $e['price'] : ''))
            )); ?>
        </li>
        <li>
            <label for="pickup">Pickup Locations</label><?php echo form_input(array(
                'name' => 'pickup',
                'id' => 'pickup',
                'class' => 'input-help',
                'placeholder' => 'Pickup Locations',
                'maxlength' => '200',
                'title' => 'Optional field. Separate locations using commas. Maximum 200 characters.',
                'value' => set_value('pickup', (isset($e) ? $e['pickup'] : ''))
            ));?>
        </li>
        <?php foreach(array('event_time', 'signup_opens', 'signup_closes') as $k) {
            echo '<li>';
                echo '<label>'.ucwords(str_replace('_', ' ', $k)).'</label>';
                echo form_input(array(
                    'name' => $k.'_date',
                    'value' => set_value('date', (isset($e) ? date("d/m/Y", $e[$k]) : date("d/m/Y"))),
                    'placeholder' => 'DD/MM/YYYY',
                    'maxlength' => '10',
                    'class' => 'datepicker'
                ));
                echo form_dropdown($k.'_hour', $hour, set_value($k.'_hour', (isset($e) ? date("G", $e[$k]) : 0)));
                echo form_dropdown($k.'_minute', $minute, set_value($k.'_minute', (isset($e) ? date("i", $e[$k]) : 0)));
            echo '</li>';
        } ?>
        <li>
            <label>Meeting Time</label><?php echo form_dropdown('meet_hour', $hour, set_value('meet_hour', (isset($e) ? $e['meet_hour'] : 0)));
            echo form_dropdown('meet_min', $minute, set_value('meet_min', (isset($e) ? $e['meet_min'] : 0))); ?>
        </li>
        <li>
            <label for="meet-location">Meeting Location</label><?php echo form_input(array(
                'name' => 'meet_location',
                'id' => 'meet-location',
                'class' => 'input-help',
                'placeholder' => 'Meeting Location',
                'maxlength' => '50',
                'title' => 'Optional. Maximum 50 characters.',
                'value' => set_value('meet_location', (isset($e) ? $e['meet_location'] : ''))
            )); ?>
        </li>
        <li>
            <label>Signup sets</label><?php echo form_dropdown('sets', array('1' => '1', '2' => '2', '3' => '3'), set_value('sets', (isset($e) ? $e['sets'] : '1')), array(
                'title' => 'How many \'sets\' of signups can a user make, eg 2 sets of reservations.'
            )); ?>
        </li>
        <li>
            <label for="notes">Signup Notes</label><?php echo form_textarea(array(
                'name' => 'notes',
                'id' => 'notes',
                'rows' => '6',
                'maxlength' => '1000',
                'title' => 'Maximum 1000 characters.',
                'value' => set_value('notes', (isset($e) ? db_to_textarea($e['notes']) : ''))
            )); ?>
        </li>
    </ul>
</div>
<div class="jcr-box">
    <h2>Permissions</h2>
    <p>The JCR President, JCR Vice-President, Social Chair and Webmaster have full permissions for signups. Other roles can be enabled here.</p>
    <ul class="nolist">
        <li>
            <select name="permission">
                <option value=""></option>
                <?php foreach($levels as $level) : ?>
                    <option value="<?php echo $level['id']; ?>"<?php echo (isset($e) ? ($level['id'] == $e['permission'] ? ' selected="selected"' : '') : '');?>><?php echo $level['full']; ?></option>
                <?php endforeach; ?>
            </select>
        </li>
    </ul>
</div>
<div class="jcr-box">
    <h2>Table/Group Options</h2>
    <ul class="nolist">
        <?php $seats_array = (isset($e) ? explode(',', $e['seats']) : array(4));
        $tables_array = (isset($e) ? explode(',', $e['table_names']) : array());
        if($e['type']==1 || $e['type']==2){
            $group_type = 'Table'; 
        }
        elseif($e['type']==3){
            $group_type = 'Coach'; 
        }
        elseif($e['type']==4){
            $group_type = 'Shirt Size'; 
        }
        else{
            $group_type = 'Group'; 
        }
        foreach($seats_array as $j => $set) {
            echo '<li><label class="signup-group-number">'.$group_type.' '.($j + 1).'</label>';
            echo '<select name="seats[]">';
                for($i = 4; $i <= 250; $i++) echo '<option value="'.$i.'" '.so($i, set_value('seats', $set)).'>'.$i.'</option>';
            echo '</select>';
            echo form_input(array(
                'name' => 'table_names[]',
                'placeholder' => 'Name',
                'title' => 'Optional field',
                'value' => set_value('table_names', (empty($tables_array[$j]) ? '' : $tables_array[$j]))
            ));
            echo '<span class="signup-group-delete ui-icon ui-icon-trash inline-block"></span>';
            echo '</li>';
        }?>
        <li><label class="narrow-hide"></label><span class="signup-group-add ui-icon ui-icon-plusthick inline-block"></span></li>
    </ul>
</div>
<div class="jcr-box" id="food">
    <h2>Food Choices</h2>
    <ul class="nolist">
        <?php foreach(array('starter','main','dessert','drink') as $v) {
            echo '<li>';
            echo '<label for="'.$v.'">'.ucfirst($v).' Options</label>';
            echo form_input(array(
                'name' => $v.'s',
                'id' => $v,
                'class' => 'input-help',
                'placeholder' => ucfirst($v).' Options',
                'maxlength' => '200',
                'title' => 'Optional field. Menu choices for the '.$v.'s. Separate choices with commas, e.g: \'Chocolate, Fruit, Cheesecake\'. Maximum 200 characters in total.',
                'value' => set_value($v.'s', (isset($e) ? $e[$v.'s'] : ''))
            ));
        } ?>
    </ul>
</div>
<div class="jcr-box" id="swapping">
    <h2>Seat Swapping Options</h2>
    <ul class="nolist">
        <li>
            <label for="swap-price">Swap Price</label><?php echo form_input(array(
                'name' => 'swap_price',
                'id' => 'swap-price',
                'class' => 'input-help',
                'maxlength' => '50',
                'placeholder' => 'Swap Price',
                'title' => 'Use this field to enable swapping by entering any content. Can be a number or text, eg \'Free\'. Swapping means that users can swap seats for themselves and others. Attendees have to sign up in pairs.',
                'value' => set_value('swap_price', (isset($e) ? $e['swap_price'] : ''))
            )); ?>
        </li>
        <?php foreach(array('swapping_opens', 'swapping_closes') as $k) {
            echo '<li>';
                echo '<label>'.ucwords(str_replace('_', ' ', $k)).'</label>';
                echo form_input(array(
                    'name' => $k.'_date',
                    'value' => set_value('date', (isset($e) && $e[$k] > 0 ? date("d/m/Y", $e[$k]) : date("d/m/Y"))),
                    'placeholder' => 'DD/MM/YYYY',
                    'maxlength' => '10',
                    'class' => 'datepicker'
                ));
                echo form_dropdown($k.'_hour', $hour, set_value($k.'_hour', (isset($e) && $e[$k] > 0 ? date("G", $e[$k]) : 0)));
                echo form_dropdown($k.'_minute', $hour, set_value($k.'_minute', (isset($e) && $e[$k] > 0 ? date("i", $e[$k]) : 0)));
            echo '</li>';
        } ?>
    </ul>
</div>