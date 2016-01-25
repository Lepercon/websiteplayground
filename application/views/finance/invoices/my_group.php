<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('finance/my_groups');

foreach($invoices as $i){
    $grouped[$i['member_id']][] = $i;
}
$totals = array('total'=>0, 'paid'=>0);
?>
<span id="group-id" style="display:none"><?php echo $group['id']; ?></span>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php echo $group['budget_name']; ?></a></li>
        <li><a href="#tabs-2">Group Members</a></li>
        <li><a href="#tabs-3">Totals</a></li>
    </ul>
    <div id="tabs-1">
        <?php if($group['id'] != 'all'){ ?>
            <a class="jcr-button inline-block no-jsify" title="Add Group Members" href="<?php echo site_url('finance/invoices/add_members/'.$group['id']); ?>">Add Members<span class="ui-icon ui-icon-plus"></span></a>
            <a class="jcr-button inline-block" title="Add A New Invoice" href="<?php echo site_url('finance/invoices/add_invoice/'.$group['id']); ?>">New Invoice<span class="ui-icon ui-icon-pencil"></span></a>
            <a class="jcr-button inline-block" title="Change The Group Information and Payment Information" href="<?php echo site_url('finance/invoices/edit_group/'.$group['id']); ?>">Group Info<span class="ui-icon ui-icon-note"></span></a>
        <?php } ?>
        <a class="jcr-button inline-block" href="<?php echo site_url('finance/invoices/view_expected/'.$group['id']); ?>">Expected Payments<span class="ui-icon ui-icon-script"></span></a>
        <?php
            if(!is_null($group['balance_date'])){
        ?>
                <h4>Account Ballance (<?php echo date('jS F Y', $group['balance_date']); ?>):</h4>
                <p class="<?php echo $group['balance']<0?'negative-bal':''; ?>">£<?php echo $group['balance']; ?></p>
        <?php
            }else{
        ?>
            <h4>Account Ballance:</h4>
            <p>£-.-- Currently Unknown</p>
        <?php } ?>
        <h4>Description:</h4>
        <p><em><?php echo $group['description']; ?></em></p>
        <h4>How To Pay:</h4>
        <p><?php echo $group['how_to_pay']; ?></p>
    </div>
    <div id="tabs-2" class="members-tables">
        <div>
            <?php
                foreach($members as $m){
            ?>
                    <div><p><span class="member-id" style="display:none"><?php echo $m['id']; ?></span>
                    <span class="admin-check"><?php echo ($m['prefname']==''?$m['firstname']:$m['prefname']).' '.$m['surname'];?></span> 
                    <a class="remove-member" title="Warning: Any Unpaid Invoices Will Be Deleted." href="#">Remove From Group</a>
            <?php
                    if(isset($grouped[$m['u_id']])){
                        ?></p>
                        <table><th style="min-width:120px;">Name</th><th style="min-width:120px;">Date</th><th>Amount</th><th>Details</th><th>Marked Paid?</th><th>Paid?</th><th style="min-width:145px;">Actions</th><?php
                        foreach($grouped[$m['u_id']] as $i){
                            $totals['total'] += $i['amount'];
                            if($i['paid']){
                                $totals['paid'] += $i['amount'];
                            }
                    ?>
                            <tr>
                            <td><?php echo $i['name']; ?></td>
                            <td><?php echo date('jS F Y', $i['date']); ?></td>
                            <td>£<?php echo $i['amount']; ?></td>
                            <td><?php echo $i['details']; ?></td>
                            <td><?php echo ($i['paid']?'':($i['marked_paid']?'YES':'NO')); ?></td>
                            <td class="invoice-paid"><?php echo ($i['paid']?'YES':'NO'); ?></td>
                            <td>
                                <span style="display:none;" class="invoice-id"><?php echo $i['id'] ?></span>
                                <span style="display:none;" class="invoice-status"><?php echo ($i['paid']?'1':'0'); ?></span>
                                <a class="invoice-paid no-jsify" title="<?php echo ($i['paid']?'Mark this entry as unpaid':'Mark this entry as paid'); ?>" href="#"><?php echo ($i['paid']?'Mark as unpaid':'Mark as paid'); ?></a> 
                                <a class="" title="Edit This Entry" href="<?php echo ''; ?>">Edit</a> 
                                <a class="invoice-remove no-jsify" title="Remove This Entry" href="#">Remove</a>
                            </td>
                            </tr>
                            <?php
                        }
                        ?></table><?php
                    }else{
                    ?>
                        </p>
                    <?php
                    } ?></div><?php
                    
                }
            
            ?>
        </div>
    </div>
    
    <div id="tabs-3" class="members-tables">
        <?php $this->load->view('finance/invoices/totals', array(
                'group'=>$group,
                'invoices'=>$inv_tot,
                'sent_emails'=>FALSE
            )); ?>
    </div>
</div>
