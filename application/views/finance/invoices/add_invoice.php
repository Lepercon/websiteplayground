<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->model('finance_model');
$g_id = $this_group['id'];

echo back_link('finance/invoices/my_group/'.$this_group['id']);
?>

<h2><?php echo 'Add Invoice - '.$this_group['budget_name']; ?></h2>
<?php echo form_open('finance/invoices/add_invoice/'.$g_id, array('class' => 'jcr-form')); ?>

<div class="content-left width-33 narrow-full">
<h3>Select Members:</h3>
<table style="margin:0;padding:0;">
<?php
    foreach($members as $m){
?>
        <tr>
            <td style="border:0;margin:0;padding:0;">
                <?php 
                    $name = ($m['prefname']==''?$m['firstname']:$m['prefname']).' '.$m['surname'];
                    echo form_label($name, 'debtor_'.$m['u_id']);
                    echo form_checkbox(array('name' => 'debtor_'.$m['u_id'], 'id' => 'debtor_'.$m['u_id'], 'value' => $m['u_id'], 'checked' => set_checkbox('debtor_'.$m['u_id'])));
                ?>                
            </td>
        </tr>
<?php
    }
?>
</table>
</div>
<div class="content-right width-66 narrow-full">
<h3>Invoice Details:</h3>
<?php 
    echo validation_errors('<div class="validation_errors"><span class="inline-block ui-icon ui-icon-notice"></span>', '</div><br>');
    if($run and $result and !$no_ids){
?>
        <div class="validation_success"><span class="inline-block ui-icon ui-icon-check"></span>You have successfully added this invoice.</div>
<?php
    }
    if($no_ids){
?>
        <div class="validation_errors"><span class="inline-block ui-icon ui-icon-check"></span>You did not select any members.</div>
<?php
    }
?>
<?php 

    echo form_label('Invoice Name:', 'invoice-name');
    echo form_input(array('name'=>'invoice_name', 'id'=>'invoice-name', 'value'=>set_value('invoice_name'), 'size'=>50, 'placeholder'=>'Name', 'autofocus'=>'ture'))."<br>";
    
    echo form_label('Amount:', 'invoice-amount');
    $amount = set_value('amount');
    echo form_input(array('name'=>'amount', 'id'=>'invoice-amount', 'value'=>$amount==''?'':'£'.$amount, 'size'=>50, 'placeholder'=>'£0.00'))."<br>";
    
    echo form_label('Date:', 'invoice-amount');
    $date = isset($_POST['date'])?$_POST['date']:'';
    echo form_input(array(
        'name' => 'date',
        'placeholder' => 'DD/MM/YYYY',
        'maxlength' => '10',
        'class' => 'datepicker input-help narrow-full',
        'title' => 'Required field. Please select the event date from the dropdown calendar. If no calendar shows then please enable javascript and try again or enter the date in DD/MM/YYYY format.',
        'required' => 'required',
        'value'=>($date==''?date('d/m/Y'):$date)
    ))."<br>";
    
    echo form_label('Description:', 'invoice-details', array('style'=>'vertical-align: top!important;'));
    echo form_textarea(array('name'=>'details', 'id'=>'invoice-details', 'value'=>set_value('details'), 'size'=>2000, 'placeholder'=>'Description', 'style'=>'width:400px;'))."<br>";
    
    echo form_label('', '');
    echo form_submit(array('name'=>'add_invoice', 'value'=>'Add Invoice For Selected Members', 'id'=>'invoice-add'));
    
    echo form_close(); 

?>

<?php echo form_close(); ?>
</div>