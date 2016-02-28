<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('finance/claims/view_claims');

$types = array(
    1 => 'cheque',
    2 => 'bank_transfer'
);

foreach($types as $k => $v){
    $cat[$v] = array();
}

foreach($claims as $c){
    $cat[$types[$c['payment_method']]][] = $c;
}

if(count($cat['bank_transfer']) > 0){
?>
<a href="#" class="download no-print jcr-button">To PDF</a><a class="no-print authorise-link no-jsify jcr-button" href="<?php echo site_url('finance/claims/authorise'); ?>">Authorise</a>
<div id="letter">
    

    <h2>Bank Transfer</h2>
    <table class="hsbc-address-header">
        <tr>
            <td class="bank-address"><?php echo editable_area('finance', 'claims/editable/bank_address'); ?></td>
            <td class="jcr-address"><?php echo editable_area('finance', 'claims/editable/jcr_address'); ?><p class="date" alt-format="<?php echo date('Y.m.d'); ?>"><?php echo date('l, j F Y'); ?></p></td>
        </tr>
    </table><br>
    
    
    <p>Dear Sir or Madam,</p><br>
    <p>We would like to request the following bank transfer(s) from the account:</p>
    <p>Account Name: <b>Josephine Butler College JCR</b></p>
    <p>Account Number: <b>51890832</b></p>
    <p>Sort Code: <b>40-19-31</b></p><br>
    <p>Bank Transfer(s):</p>
    <table class="bank-transfer-table"><tr><th>Account Name</th><th>Account Number</th><th>Sort Code</th><th>Amount</th><th>Reference</th></tr>
        <?php
            
            foreach($cat['bank_transfer'] as $c){
                echo '<tr><td>'.$c['pay_to'].'</td>';
                echo '<td>'.$this->finance_model->decrypt_data($c['account-number']).'</td>';
                echo '<td>'.$this->finance_model->decrypt_data($c['sort-code']).'</td>';
                echo '<td>£'.$c['amount'].'</td>';
                echo '<td>Butler'.$c['id'].' '.$c['item'].'</td></tr>';
            }
            
        ?>
    </table><br>
    <p>Total Number of Transfers: <b>5</b></p><br>
    <p>Thank you very much for your help, if you have any questions feel free to contact us in writing, or on the number you have on file for this account.</p><br><br>
    <p>Yours faithfully,</p><br>
    <p>Signature:</p>
    <table class="sig-table"><tr><td></td><td></td><td></td><td></td><td></td></tr></table>
    <p>Printed:</p>
    <table class="sig-table"><tr><td></td><td></td><td></td><td></td><td></td></tr></table>
</div>
<?php 
}


