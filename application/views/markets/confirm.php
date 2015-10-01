<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="width-66 narrow-full content-left">
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Durham Markets</h3>

<?php

eval(error_code());

$this->load->view('markets/nav', array('page_match' => 3)); ?>

<div class="jcr-box">
    <h3>Details</h3>
    <ul class="nolist">
        <li><b>Name:</b>&nbsp;<?php echo $this->session->userdata('market_name'); ?></li>
        <li><b>Email:</b>&nbsp;<?php echo $this->session->userdata('market_email'); ?></li>
        <li><b>Phone:</b>&nbsp;<?php echo $this->session->userdata('market_phone'); ?></li>
        <li><b>Day:</b>&nbsp;<?php echo $this->session->userdata('market_delivery'); ?></li>
        <li><b>College:</b>&nbsp;<?php echo $this->session->userdata('market_college'); ?></li>
    </ul>
    <a href="<?php echo site_url('markets/details'); ?>" class="jcr-button inline-block" title="Edit your details for this markets order">
        <span class="ui-icon ui-icon-pencil inline-block"></span>Edit
    </a>
</div>
<div class="jcr-box">

	<h3>Meals</h3>
	<ul class="nolist">
		<li><?php echo $meal_name['name']; ?></li>
		<li><?php echo $this->session->userdata('market_vegetarians'); ?>&nbsp;Vegetarians</li>
	</ul>
	<a href="<?php echo site_url('markets/meals'); ?>" class="jcr-button inline-block" title="Edit your meal choice">
		<span class="ui-icon ui-icon-pencil inline-block"></span>Edit
	</a>

</div>
<div class="jcr-box">
    <h3>Groceries</h3>
    <ul class="nolist">
        <li><b>Spending cap:</b> &pound;<?php echo number_format($this->session->userdata('market_spend'), 2, '.', ','); ?></li>
        <?php $cart = $this->cart->contents();
        if(!empty($cart)) {
            foreach($cart as $c) {?>
                <li><?php echo $c['amount'].' ('.$c['unit'].') '.$c['name']; ?></li>
            <?php }
        } else { ?>
            <li>No groceries</li>
        <?php } ?>
    </ul>
    <a href="<?php echo site_url('markets/groceries'); ?>" class="jcr-button inline-block" title="Edit your grocery choices">
        <span class="ui-icon ui-icon-pencil inline-block"></span>Edit
    </a>
</div>
<a href="<?php echo site_url('markets/order'); ?>" class="jcr-button inline-block" title="Submit your markets order">
    <span class="ui-icon ui-icon-cart inline-block"></span>Submit
</a>

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
