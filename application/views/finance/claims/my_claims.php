<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('finance/claims/claim_table');
echo back_link('finance');
?>
<h2>My Claims</h2>
<div class="wotw-outer jcr-box">
	<h2 class="wotw-day">Claims Under Budgets You Own</h2>
	<div>
		<?php table_of_claims($to_user_claims, $admin, 'Approve', 'button-approve-claim', array(0)); ?>
	</div>
</div>
<h3></h3>
<div class="wotw-outer jcr-box">
	<h2 class="wotw-day">Claims You Have Made</h2>
	<div>
		<?php table_of_claims($from_user_claims, $admin); ?>
	</div>
</div>
<?php 
	$this->load->view('finance/treasurer_contact');
?>