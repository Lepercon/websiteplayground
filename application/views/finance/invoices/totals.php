<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('finance/invoices/my_group/'.$group['id']); 
$totals = array('total'=>0,'paid'=>0);
if($sent_emails === TRUE){
    echo '<span class="validation_success" style="display:block;"><span class="ui-icon ui-icon-check green-icon"></span>Emails Sent</span>';
}
?>
<h2><?php echo $group['budget_name']; ?></h2>
<table>
    <tr><th>Name</th><th>Amount</th><th>Paid</th><th>Due</th><th>Email</th></tr>
    <?php foreach($invoices as $i){ 
        $totals['total'] += $i['total'];
        $totals['paid'] += $i['paid'];
        if($i['total']-$i['paid'] > 0){
        ?>
            <tr>
                <td><?php echo $i['name']; ?></td>
                <td>£<?php echo number_format($i['total'], 2); ?></td>
                <td>£<?php echo number_format($i['paid'], 2); ?></td>
                <td>£<?php echo number_format($i['total']-$i['paid'], 2); ?></td>
                <td><?php echo $i['current']?$i['email']:$i['custom_email']; ?></td>
            </tr>
    <?php
        }
     } ?>
</table>

<h3>Totals</h3>
<div>
    <table class="totals-table members-tables">
        <tr><td>Total:</td><td>£</td><td><?php echo number_format($totals['total'], 2); ?></td></tr>
        <tr><td>Paid:</td><td>£</td><td><?php echo number_format($totals['paid'], 2); ?></td></tr>
        <tr><td>Due:</td><td>£</td><td><?php echo number_format($totals['total']-$totals['paid'], 2); ?></td></tr>
    </table>
</div>
