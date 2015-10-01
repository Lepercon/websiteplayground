<?php
    $claim_set = isset($claim);
    if($claim_set){
        echo '<span id="claim-id" style="display:none">'.$claim['id'].'</span>';
    }
    echo form_open_multipart($submit_link, array('class' => 'jcr-form claim-edit-form no-jsify', 'id'=>'claim-edit-form'));
    
    if($claim_set){
        echo form_hidden('id', $claim['id']);
    }
        
    echo '<p>'.form_label('Pay To:', 'claim-name');
    echo form_input(array(
        'name'=>'pay_to', 
        'id'=>'claim-name', 
        'value'=>($claim_set?$claim['pay_to']:set_value('pay_to')),
        'size'=>100, 
        'placeholder'=>'Name', 
        'autofocus'=>'ture',
        'required'=>'true'
    )).'</p>';
    
    echo '<p>'.form_label('Payment Method:', 'claim-method');
    $options = array(''=>'Please Select', '1'=>'Cheque', '2'=>'Bank Transfer');
    echo form_dropdown('payment_method', $options, ($claim_set?$claim['payment_method']:set_value('payment_method')), 'id="claim-method" required="true"').'<br>';
    
    echo '<div id="transfer-details"'.(($claim_set?$claim['payment_method']:set_value('payment_method'))==2?' style="display:block;"':'').'>';
    echo '<p>'.form_label().form_label('Account Number:', 'account-number');
    echo form_input(array(
        'name'=>'account-number', 
        'id'=>'account-number', 
        'value'=>($claim_set?(empty($claim['account-number'])?'':'Hidden'):''),
        'maxlength'=>8, 
        'pattern'=>'[0-9]{8}|\Hidden',
        'placeholder'=>'Account Number'
    )).'</p>';
    
    echo '<p>'.form_label().form_label('Sort Code:', 'sort-code');
    echo form_input(array(
        'name'=>'sort-code', 
        'id'=>'sort-code', 
        'value'=>($claim_set?(empty($claim['sort-code'])?'':'Hidden'):''),
        'maxlength'=>8,
        'pattern'=>'[0-9]{2}-[0-9]{2}-[0-9]{2}|\Hidden',
        'placeholder'=>'Sort Code'
    )).'</p>';
    
    echo '<p class="help-note">Your connection to this website is '.(HTTPS?'':'not ').'secured. Your details are stored on our database and are encrypted. They are only accessable by the JCR Treasurer with a 2 factor authentication system.</p></div>';
    
    echo '<p>'.form_label('Amount:', 'claim-amount');
    echo form_input(array(
        'name'=>'amount', 
        'id'=>'claim-amount', 
        'value'=>($claim_set?'£'.$claim['amount']:set_value('amount')), 
        'size'=>10, 'placeholder'=>'£0.00',
        'required'=>'true'
    )).'</p>';
    
    echo '<p>'.form_label('Item:', 'claim-item');
    echo form_input(array(
        'name'=>'item', 
        'id'=>'claim-item', 
        'value'=>($claim_set?$claim['item']:set_value('item')), 
        'size'=>50, 
        'placeholder'=>'Item',
        'required'=>'true'
    )).'</p>';
    
    echo '<p>'.form_label('Budget:', 'claim-budget');
    echo form_dropdown('budget_id', array(''=>'Please Select') + $budgets, ($claim_set?$claim['budget_id']:set_value('budget_id')), 'id="claim-budget" required="true"').'</p>';
    
    echo '<p>'.form_label('Details:', 'claim-details', array('style'=>'vertical-align: top!important;'));
    echo form_textarea(array(
        'name'=>'details', 
        'id'=>'claim-details', 
        'size'=>2000, 
        'placeholder'=>'Details', 
        'value'=>($claim_set?$claim['details']:set_value('details'))
    )).'</p>';
    
    if($claim_set){
        $files = explode(',', $claim['files'],-1);
        $i = 1;
        foreach($files as $f){
            echo form_label($i==1?'Files: ':'');?>
            <span><span id="file-name" style="display:none;"><?php echo $f;?></span><a href="<?php echo site_url('application/views/finance/files/'.$f);?>"><?php echo 'File '.$i++;?></a> - <a href="#" class="remove-file">Remove</a></span><br><?php
        }
    }

    echo '<p>'.form_label('Upload Receipts:', '', array('style'=>'vertical-align: top!important;'));
    echo form_upload(array('name'=>'userfile')).'</p>';
    
    echo '<p>'.form_label('').'Accepted file types: pdf, png, and jpg.</p><br>';
    
    echo form_label('');
    echo form_input(array('name'=>'neworupdate', 'value'=>'new', 'style'=>'display:none;'));
    echo form_submit(array('name'=>'submitandupload', 'value'=>(isset($button_name)?$button_name:'Submit').' Claims Form', 'id'=>'claim-submit-button'));
    
    echo form_close();
?>
<div id="account-details-confirmation">
    <p>Are the following account details correct?</p>
    <p>Account Number:</p>
    <div class="acc-num details-box">123454678</div>
    <p>Sort Code:</p>
    <div class="sort-code details-box">12-34-56</div>
</div>