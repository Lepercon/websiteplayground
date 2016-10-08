<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('finance');
$this->load->view('finance/claims/claim_table');
$this->load->model('finance_model');
?>
<div class="wotw-outer jcr-box">
    <h2 class="wotw-day">Claims</h2>
    <div>
        <h3>Claims Awaiting Payment</h3>
        <?php 
            table_of_claims($claims_waiting, $admin, 'Mark Paid', 'button-mark-paid', array(1), true); 
            
            if($admin){
                echo '<p><input type="checkbox" class="select-all-claims"> Select All</p><p><a href="'.site_url('finance/claims/pay/').'" class="jcr-button pay-selected no-jsify">Pay Selected</a></p>';
            }
        ?>
        
    </div>
</div>
<div class="wotw-outer jcr-box">
    <h2 class="wotw-day">Budgets</h2>
    <div>
        <h3>Current Budgets</h3>
        <a class="jcr-button inline-block" title="Edit" href="<?php echo site_url('finance/claims/edit_budgets'); ?>">
            <span class="inline-block ui-icon ui-icon-pencil"></span>Edit
        </a>
        <ol id="budgets-list" style="padding-left:40px">
        <?php foreach($budgets as $b){ ?>
                <li><?php echo $b['budget_name'].' ('.implode(',', $b['levels']).')'; ?></li>
        <?php }?>
        </ol><br>
        <h3>Add New Budget</h3>
        <?php
        
            echo form_open('', array('class' => 'jcr-form claim-add-budget no-jsify', 'id'=>'claim-add-budget'));
            
            echo '<p>'.form_label('Budget Name:', 'newname');
            echo form_input(array('name'=>'newname', 'id'=>'newname', 'value'=>'', 'size'=>50, 'placeholder'=>'Budget Name'))."</p>";

            echo '<p>'.form_label('Budget Holder:', 'newname');
            echo form_dropdown('holder', $all_levels, '').'</p>';

            echo form_label('');
            echo form_submit(array('name'=>'newbudget', 'value'=>'Add Budget', 'id'=>'add-new-budget'));
        
            echo form_close();
            
        ?>
    </div>
</div>

<div class="wotw-outer jcr-box">
    <h2 class="wotw-day">Claims</h2>
    <div>
        <h3>Claims Awaiting Review From Budget Holder 
        <?php
            if($claims_to_be_reviewed !== FALSE){
        ?>        
                (<a href="<?php echo site_url('finance/claims/view_claims/0/'.($claims_paid === FALSE?0:1)); ?>">Hide</a>)</h3>
                <?php table_of_claims($claims_to_be_reviewed, $admin, 'Approve', 'button-approve-claim', array(0)) ?>        
        <?php
            }else{
        ?>
                (<a href="<?php echo site_url('finance/claims/view_claims/1/'.($claims_paid === FALSE?0:1)); ?>">Show</a>)</h3>
                <p>Claims Hidden</p>
        <?php
            }
        ?>
        <h3>Claims Paid 
        <?php
            if($claims_paid !== FALSE){
        ?>        
                (<a href="<?php echo site_url('finance/claims/view_claims/'.($claims_to_be_reviewed === FALSE?0:1).'/0'); ?>">Hide</a>)</h3>
                <?php table_of_claims($claims_paid, $admin, 'Unmark As Paid', 'button-mark-paid', array(2)); ?>        
        <?php
            }else{
        ?>
                (<a href="<?php echo site_url('finance/claims/view_claims/'.($claims_to_be_reviewed === FALSE?0:1).'/1'); ?>">Show</a>)</h3>
                <p>Claims Hidden</p>
        <?php
            }
        ?>
</div>
</div>