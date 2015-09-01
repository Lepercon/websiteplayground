<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="width-66 narrow-full content-left">
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Durham Markets</h3>

<?php


$this->load->view('markets/nav', array('page_match' => 2)); ?>

<div class="content-left width-33 narrow-full">
	<div class="jcr-box">
		<h3>Search</h3>
		<ul class="nolist">
			<li>
				<input class="inline-block narrow-full" type="text" name="s" id="grocery-search" placeholder="Search" autocomplete="off">
			</li>
		</ul>
	</div>
	<div class="jcr-box narrow-hide">
		<h3>Meal</h3>
		<ul class="nolist">
			<li><?php echo ucfirst($this->session->userdata('market_meal')); ?></li>
			<li><?php echo $this->session->userdata('market_vegetarians'); ?>&nbsp;Vegetarians</li>
		</ul>
		<h3>Groceries</h3>
		<ul id="grocery-order" class="nolist">
			<li>Enable javascript to see your order summary</li>
		</ul>
	</div>
</div>

<div class="content-right width-66 narrow-full">
	<?php eval(error_code()); ?>

	<?php echo form_open('markets/groceries', array('class' => 'jcr-form')); ?>
	<ul class="nolist">
		<li><p>Because the price of fruit and veg varies on a daily basis, please select a spending cap for your total order of fruit and veg.</p></li>
		<li>
			<label style="width: auto;">Total Spend Cap</label><select name="spend" required="required">
				<?php for($vegcount = 5; $vegcount <=15; $vegcount+=2.5) {
					echo '<option value="'.$vegcount.'" '.set_select('vegetarians', $vegcount, ($vegcount==0 ? TRUE : FALSE)).'>&pound;'.number_format($vegcount, 2, '.', ',').'</option>';
				}?>
			</select>
		</li>
		<li><p>Find or search for groceries, then enter the quantity or weight you want to order in the boxes below.</p></li>
	</ul>

	<div id="accordion" style="font-size: 90%;">
		<?php
		$boxcount = 0;
		foreach($categories as $c) { ?>
			<h3><a href="#"><?php echo $c; ?></a></h3>
			<div><ul class="nolist">
			<?php foreach($vegetables as $v) {
				if($v['category'] == $c) {
				$boxcount++; ?>
					<li>
						<div>
							<label style="width: auto;"><?php echo (in_array($v['id'], $favourites) ? '<span class="ui-icon ui-icon-star grocery-favourite inline-block" title="Favourite"></span>' : '').'<span class="grocery-name">'.$v['name'].'</span> ('.$v['unit'].')'; ?>:</label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<?php echo form_input(array(
								'name' => 'veg['.$boxcount.'][amount]',
								'value' => $errors != FALSE ? set_value('veg['.$boxcount.']') : (empty($cart) ? '' : (array_key_exists($v['id'], $cart) ? $cart[$v['id']] : '')),
								'maxlength' => '30',
								'placeholder' => $v['unit'].' of '.$v['name'],
								'title' => 'Maximum 30 characters. Enter an amount.',
								'class' => 'input-help grocery-amount'
							));
							echo form_hidden('veg['.$boxcount.'][id]', $v['id']);
							echo form_hidden('veg['.$boxcount.'][name]', $v['name']);
							echo form_hidden('veg['.$boxcount.'][unit]', $v['unit']);?>
						</div>
					</li>
				<?php }
			} ?>
			</ul></div>
		<?php } ?>
	</div>
	<?php echo form_submit('groceries', 'Continue'); ?>
	<?php echo token_ip('market_order'); ?>
	<?php echo form_close(); ?>
</div>

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

