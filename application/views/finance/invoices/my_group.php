<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('finance/invoices/my_groups');

$mem = array();
foreach($members as $m){
    $mem[$m['u_id']] = $m;
}

foreach($invoices as $i){
    $grouped[$i['member_id']][] = $i;
    if(!isset($mem[$i['member_id']])){
        $mem[$i['member_id']] = $this->finance_model->add_group_member($group['id'], $i['member_id']);
    }
}

$totals = array('total'=>0, 'paid'=>0);
$meth = array('bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'cheque_college' => 'Cheque', 'cash'=>'Cash');
?>
<span id="group-id" style="display:none"><?php echo $group['id']; ?></span>

<div id="tabs-2">
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
        <?php echo anchor('finance/invoices/my_group/'.$this->uri->segment(4).'/'.(!$this->uri->segment(5)), $this->uri->segment(5)?'Hide Paid':'Show Paid'); ?>
        <input class="invoice-search-box" placeholder="Search..." onClick="this.setSelectionRange(0, this.value.length)" >
        <div>
            <table class="big-invoice-table"><tr><th>Name</th><th>Item</th><th>Date</th><th>Amount</th><th>Details</th><th>Marked Paid?</th><th>Paid?</th><th style="min-width:145px;">Actions</th></tr>
            <?php
                $last_name = '';
                foreach($mem as $m){
                    $name = ($m['prefname']==''?$m['firstname']:$m['prefname']).' '.$m['surname'];
                    if(isset($grouped[$m['u_id']])){
                        
                        foreach($grouped[$m['u_id']] as $i){
                            $totals['total'] += $i['amount'];
                            if($i['paid']){
                                $totals['paid'] += $i['amount'];
                            }
                    ?>
                            <tr class="person-row <?php echo $name==$last_name?'ditto':'new-member';?>">
                                <td><?php
                                    if($last_name != $name)
                                        echo $name;
                                    else
                                        echo '<span style="display:none;">'.$name.'</span>';
                                    $last_name = $name;
                                ?></td>
                                <td><?php echo $i['name']; ?></td>
                                <td><?php echo date('jS F Y', $i['date']); ?></td>
                                <td>£<?php echo $i['amount']; ?></td>
                                <td><?php echo $i['details']; ?></td>
                                <td><?php echo ($i['paid']?'':($i['marked_paid']?'YES':'NO')); ?></td>
                                <td class="invoice-paid"><?php echo ($i['paid']?(is_null($i['payment_method'])?'YES':$meth[$i['payment_method']]):'NO'); ?></td>
                                <td style="width:170px;" class="<?php echo $i['paid']?'paid':'un-paid';?>">
                                    <span style="display:none;" class="invoice-id"><?php echo $i['id'] ?></span>
                                    <span style="display:none;" class="invoice-status"><?php echo ($i['paid']?'1':'0'); ?></span>
                                    <a class="unpaid invoice-paid no-jsify jcr-button" href="#">Mark Unpaid</a> 
                                    <a class="bank-transfer invoice-paid no-jsify jcr-button" method="bank_transfer" method-name="Bank Transfer" href="#">Bank Transfer</a> 
                                    <a class="cheque invoice-paid no-jsify jcr-button" method="cheque" method-name="Cheque" href="#">Cheque</a> 
                                    <a class="cash invoice-paid no-jsify jcr-button" method="cash" method-name="Cash" href="#">Cash</a> 
                                    <!--<a class="" title="Edit This Entry" href="">Edit</a>-->
                                    <a class="invoice-remove no-jsify jcr-button inline-block" title="Remove This Entry" href="#">Remove</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?><?php
                       
                    
                }
                }
            ?>
            </table>
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
