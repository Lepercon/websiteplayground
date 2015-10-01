<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('finance/my_group/'.$group['id']); 
$total = array();
?>
<h2><?php echo $group['budget_name']; ?></h2>
<table>
    <tr><th>Name</th><th>Email</th><th>For</th><th>Amount</th></tr>
    <?php foreach($invoices as $key=>$i){ 
        $total[$key] = 0;
        foreach($i as $k=>$j){ 
            $total[$key] += $j['amount']; ?>
            <tr>
                <td><?php echo $k==0?(($j['prefname']==''?$j['firstname']:$j['prefname']).' '.$j['surname']):''; ?></td>
                <td><?php echo $k==0?$j['email']:''; ?></td>
                <td><?php echo $j['name']; ?></td>
                <td>£<?php echo number_format($j['amount'], 2); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td></td>
            <td></td>
            <td><b>Total</b></td>
            <td><b>£<?php echo number_format($total[$key], 2); ?></b></td>
        </tr>
        <?php
        } ?>
</table>
