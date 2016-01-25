<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
echo back_link('finance/my_invoices');
var_dump(GoCardless::$environment);

if(isset($_POST['invoices'])){
	echo '<h2>Make Payments</h2>';
	echo '<p>You have selected to make the following payments, to change this click '.anchor('finance/my_invoices', 'here').'.</p>';
	$total = 0.0;
	$name = '';
	foreach($_POST['invoices'] as $p){
		$i = $invoices[$p];
		echo '<p><b>'.$groups[$i['group_id']]['budget_name'].'</b> - '.$i['name'].' - £'.$i['amount'].'</p>';
		$total += $i['amount'];
		$name .= ($name==''?'':', ').$groups[$i['group_id']]['budget_name'].' - '.$i['name'];
	}
	echo '<p><b>Total:</b> £'.number_format($total,2).'</p>';
}else{
	echo '<p>You did not select any invoices, please click '.anchor('finance/my_invoices', 'here').' to go back.</p>';
}

$bill_id = uniqid();

$payment_details = array(
  'amount'  => $total,
  'name'    => $name,
  'description' => '',
  'user'             => array(
    'first_name'       => $this->session->userdata('firstname'),
    'last_name'        => $this->session->userdata('surname'),
    'company_name'     => '',
    'billing_address1' => '',
    'billing_address2' => '',
    'billing_town'     => '',
    'billing_postcode' => '',
    'country_code'     => 'GB'
  ),
  'redirect_uri' => site_url('finance/payments/payment_complete'),
  'state' => $bill_id  
);

$bill_url = GoCardless::new_bill_url($payment_details);
echo anchor($bill_url, 'Make Payment'); // Link to set up one off bill