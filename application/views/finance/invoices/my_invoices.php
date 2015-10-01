<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('finance/');

?>
<div class="jcr-box wotw-outer">
    <h3 class="wotw-day">How To Pay</h3>
    <?php echo editable_area('finance', 'content/finance_how_to_pay', $permissions); ?>
</div>
<div class="invoice-tables"><div class="wotw-outer jcr-box"><h3 class="wotw-day">My Due Invoices</h3><div><?php
if(!empty($invoices)) {
    $printasterisk = false;
    $foundany = false;
    $lastname = '';
    $total = 0;
    echo form_open('finance/payments', 'class="jcr-form no-jsify"');
    ?><table><?php
    foreach($invoices as $i) {
        if($i['paid'] == 0){
            if(!$foundany){?>
                <tr><!--<th></th>--><th>Group</th><th>Name</th><th style="min-width:120px;">Date</th><th>Amount</th><th style="min-width:145px;">Actions</th></tr>
            <?php } 
            foreach($groups as $g) {
            if($g['id'] === $i['group_id'])
                break;
            }?><tr>
            <!--<td><?php //echo form_checkbox('invoices[]', $i['id'], TRUE); ?></td>-->
            <th><?php echo ($g['budget_name']==$lastname?'':$g['budget_name']); $lastname = $g['budget_name']; ?></th>
            <td class="<?php echo $i['marked_paid']?'marked-paid':''; ?>"><?php echo $i['name']; ?></td>
            <td><?php echo date('jS F Y',$i['date']); ?></td>
            <td>£<?php echo $i['amount']; $total += $i['amount'];?></td>
            <td>
                <span style="display:none;" class="invoice-id"><?php echo $i['id'] ?></span>
                <span style="display:none;" class="invoice-marked-status"><?php echo ($i['marked_paid']?'1':'0'); ?></span>
                <a class="inline-block" title="See More Details About This Invoice." href="<?php echo site_url('finance/my_invoice/'.$i['id']); ?>">More Details</a>, 
                <a class="inline-block invoice-mark-paid" title="<?php echo ($i['marked_paid']?'Select this to undo.':'Select this once you have sent in the payment for this item.'); ?>" href="#"><?php echo ($i['marked_paid']?'Unm':'M'); ?>ark As Paid</a>
            </td>
            </tr><?php
            $printasterisk = $printasterisk || $i['marked_paid'];
            $foundany = true;
        }
    }
    
    ?><th class="no-border"></th><th class="no-border"></th><th class="no-border"></th><td><b>£<?php echo number_format($total, 2); ?></b></td></table><?php
    //echo form_submit('make-payment', 'Pay Selected');
    echo form_close();
    if(!$foundany){
        ?><p>You have no unpaid invoices.</p><?php
    }
    if($printasterisk){
        ?><p>* These have been marked as paid, but have not yet been accepted by the group owner.</p><?php
    }
    ?>    
    </div></div><div class="wotw-outer jcr-box">
    <h3 class="wotw-day">My Paid Invoices</h3><div><table><?php
    
    $foundany = false;
    $lastname = '';
    foreach($invoices as $i) {
        if($i['paid'] == 1){
            if(!$foundany){?>
                <th>Group</th><th>Name</th><th>Date</th><th>Amount</th><th>Actions</th>
            <?php } 
            foreach($groups as $g) {
            if($g['id'] === $i['group_id'])
                break;
            }?><tr>
            <td><b><?php echo ($g['budget_name']==$lastname?'':$g['budget_name']); $lastname = $g['budget_name']; ?></b></td>
            <td><?php echo $i['name']; ?></td>
            <td><?php echo date('jS F Y',$i['date']); ?></td>
            <td>£<?php echo $i['amount']; ?></td>
            <td>
                <a class="inline-block" title="See More Details About This Invoice." href="<?php echo site_url('finance/my_invoice/'.$i['id']); ?>">More Details</a>
            </td>
            </tr><?php
            $printasterisk = $printasterisk || $i['marked_paid'];
            $foundany = true;
        }
    }
     ?></table><?php
    if(!$foundany){
        ?><p>You have no paid invoices.</p><?php
    }

} else { ?>
    <p>You have no payments due.</p>
<?php } ?>
    </div></div></div>
<?php $this->load->view('finance/treasurer_contact'); ?>
    