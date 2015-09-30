<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="width-66 narrow-full content-left">
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Durham Markets</h3>

<?php


$this->load->view('markets/nav', array('page_match' => 1));

eval(error_code());

echo form_open('markets/meals'); ?>

<p>&pound;15 for 6 people. Select the number of vegetarians you are ordering for to receive substitute dishes in your order.</p>
<ul class="nolist jcr-form">
    <?php
    $mealcount = 1;
    echo '<li>';
    echo '<input id="meal'.$mealcount.'" type="radio" name="meal" value="no meal" '.(($this->session->userdata('market_meal') == 'no meal' OR ($mealcount == 1 && $this->session->userdata('market_meal') == FALSE)) ? 'checked="checked"' : set_radio('meal', 'no meal')).'><label class="radio-label" for="meal'.$mealcount.'">No Meal</label>';
    echo '</li>';
    foreach($meals as $m) {
        $mealcount++;
        echo '<li>';
        echo '<input id="meal'.$mealcount.'" type="radio" name="meal" value="'.strtolower($m['name']).'" '.($this->session->userdata('market_meal') == strtolower($m['name']) ? 'checked="checked"' : set_radio('meal', strtolower($m['name']))).'><label class="radio-label" for="meal'.$mealcount.'">'.$m['name'].(!file_exists(VIEW_PATH.'markets/recipes/'.$m['id'].'.pdf') ? '' : ' - <a href="'.VIEW_URL.'markets/recipes/'.$m['id'].'.pdf" class="no-jsify">View the recipe</a>').'</label>';
        echo '</li>';
    } ?>
    <li>
        <label>No. of Vegetarians</label><select name="vegetarians" required="required" title="Select the number of vegetarians to receive a substitute meal.">
            <?php for($vegcount = 0; $vegcount <=6; $vegcount++) {
                echo '<option value="'.$vegcount.'" '.set_select('vegetarians', $vegcount, ($vegcount==0 ? TRUE : FALSE)).'>'.$vegcount.'</option>';
            }?>
        </select>
    </li>
    <li>
        <?php echo form_submit('meals', 'Continue'); ?>
    </li>
</ul>
<?php echo token_ip('market_order'); ?>
<?php echo form_close(); ?>
    </div>
</div>
<div class="content-right width-33 narrow-full">
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Get In Contact</h3>
        <?php $this->load->view('utilities/users_contact', array(
            'level_ids'=>array(3),
            'title_before'=>'If you would like more information then contact your ',
            'title_after'=>':',
            'title_level'=>'p'
        )); ?>
    </div>
</div>
