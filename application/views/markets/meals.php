<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="width-66 narrow-full content-left">
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Durham Markets</h3>

<?php


$this->load->view('markets/nav', array('page_match' => 1));

eval(error_code());

echo form_open('markets/meals'); ?>

<p>Order the Veg boxes you want. Please note you do NOT need to select loose vegetables if you are ordering a veg box!</p><br />
<ul class="nolist jcr-form">
	<?php
	$mealcount = 1;
	foreach($meals as $m) {
		$mealcount++;
		echo '<li>';
		echo '<input id="meal'.$mealcount.'" type="radio" name="meal" value="'.strtolower($m['id']).'" '.($this->session->userdata('market_meal') == strtolower($m['id']) ? 'checked="checked"' : set_radio('meal', strtolower($m['id']))).'><label class="radio-label" for="meal'.$mealcount.'">'.$m['name'].(!file_exists(VIEW_PATH.'markets/recipes/'.$m['id'].'.pdf') ? '' : ' - <a href="'.VIEW_URL.'markets/recipes/'.$m['id'].'.pdf" class="no-jsify">View the recipe</a>').'</label>';
		echo '</li>';
		
		if($m['name']=='Turkey Butterfly Christmas Meal Pack £25' || $m['name']=='No Meal' || $m['name']=='5 Week Veg Box £20'){
			echo '<br /><hr><br />';
		}
	} ?>
	<li>
		<label>No. of Vegetarians</label>
                <select name="vegetarians" required="required" title="Select the number of vegetarians to receive a substitute meal.">
			<?php for($vegcount = 0; $vegcount <=6; $vegcount++) {
				echo '<option value="'.$vegcount.'" '.set_select('vegetarians', $vegcount, ($vegcount==0 ? TRUE : FALSE)).'>'.$vegcount.'</option>';
			}?>
		</select>
	</li>
        <li>
                <label>No. of Vegans</label>
                <select name="vegans" required="required" title="Select the number of vegans to receieve a substitute meal.">
                        <?php for($vegcount = 0; $vegcount <=6; $vegcount++) {
                                echo '<option value="'.$vegcount.'" '.set_select('vegans', $vegcount, ($vegcount==0 ? TRUE : FALSE)).'>'.$vegcount.'</option>';
                        }?>
                </select>
        </li>
	<!--<p>
	*Christmas meal packs include choice of meat and vegetables including potatoes, parsnips, onions and carrots for roasting.
	</p>-->
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
