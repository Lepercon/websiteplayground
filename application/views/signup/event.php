<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup');
$permissions = $this->signup_model->check_permission($e);
$token = token_ip('signup-event');

echo editable_area('signup', 'content/info/'.$e['id'].'_intro', $permissions); 
?>

<input type="hidden" id="e_id" value="<?php echo $e['id']; ?>" />
<div id="accordion" class="no-print">
    <?php if(!(!empty($e['swap_price']) && time() >= $e['swapping_opens'] && time() < $e['swapping_closes'] && time() > $e['signup_closes']) || $permissions) {?>
        <h3>Sign Up</h3>
        <div>
    <?php $signup_public = (time() >= $e['signup_opens'] && time() < $e['signup_closes']);
        if($signup_public OR ($permissions && (((time() + 15*60) < $e['signup_opens'])) || time() > $e['signup_closes'])) :
            if(!$signup_public) {
                echo '<h2 id="servertime">Admin only</h2>';
                $this->load->view('signup/clock');
            }
            echo form_open('signup/event/'.$e['id'], array('class' => 'no-jsify jcr-form', 'id' => 'reservation-form'));
                $this->load->view('signup/booking_form', array('token' => $token));
            echo form_close();
        elseif(time() < $e['signup_opens']) :
            echo '<h2 id="servertime">Sign up not yet open</h2>';
            $this->load->view('signup/clock');
        else :
            echo '<h2>Sign up has closed</h2>';
        endif; ?>
        </div>
    <?php } ?>
    <?php if(!empty($e['swap_price'])) { ?>
        <h3>Swapping</h3>
        <div>
        <?php if((time() >= $e['swapping_opens'] && time() < $e['swapping_closes']) || is_admin()) :
            echo form_open('signup/event/'.$e['id'], 'class="jcr-form"'); 
            echo '<h2 id="servertime">Swapping Closes At '.date('g:ia d/m/Y', $e['swapping_closes']).'</h2>';
           
            $this->load->view('signup/clock');
        ?>
            Choose two pairs of people to swap. You will be charged <?php echo (is_numeric($e['swap_price']) ? '£':'').$e['swap_price'];?> for each swap. You have made <?php echo $num_swaps;?> swap<?php echo ($num_swaps == 1 ? '':'s');?> so far.
            <?php eval(error_code()); ?>
            <ul id="reservation-details" class="nolist">
                <li>
                    <label>Swap:</label>
                    <select name="pair1">
                        <option></option>
                        <?php 
                            if($e['id'] == 158){
                                foreach($pairs as $p) echo '<option value="'.$p['id1'].';'.$p['id2'].';'.$p['table1'].'">'.$p['name1'].'</option>'; 
                            }else{
                                foreach($pairs as $p) echo '<option value="'.$p['id1'].';'.$p['id2'].';'.$p['table1'].'">'.$p['name1'].' & '.$p['name2'].'</option>'; 
                            }
                        ?>
                    </select>
                </li>
                <li>
                    <label>With:</label>
                    <select name="pair2">
                        <option></option>
                        <?php 
                            if($e['id'] == 158){
                                foreach($pairs as $p) echo '<option value="'.$p['id1'].';'.$p['id2'].';'.$p['table1'].'">'.$p['name1'].'</option>'; 
                            }else{
                                foreach($pairs as $p) echo '<option value="'.$p['id1'].';'.$p['id2'].';'.$p['table1'].'">'.$p['name1'].' & '.$p['name2'].'</option>'; 
                            }
                        ?>
                    </select>
                </li>
                <li>
                    <input type="hidden" name="swap" value="1" />
                    <label></label><input type="submit" value="Swap Pairs" />
                    <?php echo $token; ?>
                </li>
            </ul>
            <?php echo form_close();
        elseif(time() < $e['swapping_opens']) :
            echo '<h3>Swapping not yet open</h3>';
        else :
            echo '<h3>Swapping has closed</h3>';
        endif; ?>
        </div>
    <?php } ?>
    <h3><?php echo $e['name']; ?></h3>
    <div>
        <?php echo editable_area('signup', 'content/info/'.$e['id'].'_info', $permissions); ?>
        <div id="item-details">
            <ul class="nolist">
                <li>
                    <label>Date</label><div class="narrow-full"><?php echo date('l, d/m/y', $e['event_time']); ?></div>
                </li>
                <li>
                    <label>Time</label><div class="narrow-full"><?php echo date('H:i', $e['event_time']); ?></div>
                </li>
                <?php if(!empty($e['meet_location'])) { ?>
                    <li>
                        <label>Meeting</label><div class="narrow-full"><?php echo sprintf('%2d',$e['meet_hour']).':'.sprintf('%02d',$e['meet_min']).' at '.$e['meet_location']; ?></div>
                    </li>
                <?php }
                if(!empty($e['dress_code'])) { ?>
                    <li>
                        <label>Dress code</label><div class="narrow-full"><?php echo $e['dress_code']; ?></div>
                    </li>
                <?php }
                if(!empty($e['price'])) { ?>
                <li>
                    <label>Price</label><div class="narrow-full"><?php echo (is_numeric($e['price']) ? '£'.$e['price'] : $e['price']); ?></div>
                </li>
                <?php } ?>
                <li>
                    <label>Signup opens</label><div class="narrow-full"><?php echo date('H:i \o\n D, d/m/y', $e['signup_opens']); ?></div>
                </li>
                <li>
                    <label>Signup ends</label><div class="narrow-full"><?php echo date('H:i \o\n D, d/m/y', $e['signup_closes']); ?></div>
                </li>
                <?php if(!empty($e['swap_price'])) { ?>
                    <li>
                        <label>Swapping opens</label><div class="narrow-full"><?php echo date('H:i \o\n D, d/m/y', $e['swapping_opens']); ?></div>
                    </li>
                    <li>
                        <label>Swapping ends</label><div class="narrow-full"><?php echo date('H:i \o\n D, d/m/y', $e['swapping_closes']); ?></div>
                    </li>
                    <li>
                        <label>Swapping Price</label><div class="narrow-full"><?php echo (is_numeric($e['swap_price']) ? '£'.$e['swap_price'] : $e['swap_price']); ?></div>
                    </li>
                <?php } ?>
                <li>
                    <label>Spaces</label><div class="narrow-full"><?php echo ($e['seats_remain'] == 0 ? 'None' : $e['seats_remain']); ?></div>
                </li>
                <?php if(!empty($e['notes'])) : ?>
                <li>
                    <label>Notes</label><div class="narrow-full"><?php echo $e['notes']; ?></div>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <h3>Instructions</h3>
    <div>
        <?php echo editable_area('signup', 'content/instructions', $permissions); ?>
    </div>
    <h3>Feedback</h3>
    <div>
        <?php if(isset($_POST['feedback-success'])){?>
            <p>Thank you for your feedback!</p>
        <?php }else{ ?>
            <h2>Anonymous Requests/Feedback</h2>
            <p>If you have any requests/feedback for formals or sign up, please fill in the box below:</p>
            <?php 
                echo form_open('', 'class="jcr-form"');
                
                echo form_textarea(array('name'=>'requests-feeback', 'placeholder'=>'Requests/Feedback', 'style'=>'max-width:99%;width:99%;min-width:99%;'));
                echo form_submit('submit', 'Submit Feedback');
                echo form_close();
        }
        ?>
    </div>
    <?php if($permissions) : ?>
        <h3>Admin</h3>
        <div>
            <?php echo anchor('signup/edit_signup/'.$e['id'], 'Edit signup').'<br />';
            echo anchor('signup/food_choices/'.$e['id'], 'View attendee table').'<br />';
            echo anchor('signup/catering/'.$e['id'], 'View catering numbers').'<br />';
            if(!empty($e['swap_price'])) {
                echo anchor('signup/swaps/'.$e['id'], 'View user seat changes').'<br />';
                echo anchor('signup/swap_totals/'.$e['id'], 'View swap debts').'<br />';
            }
            echo anchor('signup/stats/'.$e['id'], 'View signup stats').'<br />';
            echo print_link().'<br />';
            echo anchor('signup/cancel_signup/'.$e['id'], 'Cancel signup'); ?>
        </div>
    <?php endif; ?>
</div>
<?php if(time() >= $e['signup_opens'] && time() < $e['signup_closes']) { ?>
    <h3><a class="no-print" href="#" id="refresh-tables">Refresh <?php echo ($e['group_type'] ? 'table' : 'group'); ?>s</a></h3>
<?php } ?>
<div id="signup-tables" class="print-width">
    <?php $this->load->view('signup/tables'); ?>
</div>
