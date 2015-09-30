<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('finance/'.($admin?'view':'my').'_claims');

$methods = array('1'=>'Cheque', '2'=>'Bank Transfer');
?>

<a class="no-print" id="print-file" href="#">Save as PDF</a> | 
<a class="no-print authorise-link no-jsify" href="<?php echo site_url('finance/authorise'); ?>">Authorise</a>
<div id="claim-print">
    <table class="claim-table claim-table-title">
        <tr class="no-border">
            <td><img class="claim-logo" src="<?php echo site_url('application/views/finance/claims/logo.png'); ?>"></td>
            <td id="claim-title">JCR Claims Form</td>
            <td><img class="claim-logo" src="<?php echo site_url('application/views/finance/claims/logo.png'); ?>"></td>
        </tr>
    </table>
    
    <table class="claim-table claim-table-body">
        <tr>
            <td>Pay:<font class="input"><?php echo $claim['pay_to']; ?></font></td>
            <td>The sum of:<font class="input">£<?php echo $claim['amount']; ?></font></td>
        </tr>
        <tr>
            <td>Item:<font class="input"><?php echo $claim['item']; ?></font></td>
            <td>Payment Method:<font class="input"><?php echo $methods[$claim['payment_method']]; ?></font></td>
        </tr>
        <?php if($claim['payment_method'] == 1){ ?>
        
        <?php }else{ ?>
            <tr>
                <td>Account Number:<font class="input important-info"><?php echo $claim['account-number']; ?></font><font class="input unimportant-info"> ########</font></td>
                <td>Sort Code:<font class="input important-info"><?php echo $claim['sort-code']; ?></font><font class="input unimportant-info"> ##-##-##</font></td>
            </tr>
        <?php } ?>
        <tr>
            <td>Budget:<font class="input"><?php echo $claim['budget_name']; ?></font></td>
            <td>
                Approved By:
                <font class="input"><?php echo ($claim['status']>0)?$claim['approver_name'].' <img src="'.site_url('application/views/finance/claims/check.png').'">':'<img src="'.site_url('application/views/finance/claims/cross.png').'">'; ?></font>
            </td>
        </tr>

    </table>
    
    <p class="body-text"><?php echo nl2br($claim['details']); ?></p>
    <p class="note">For JCR Treasurer Use:</p>
    <table class="claim-table claim-table-footer">
        <tr class="double-line">
            <td colspan="3">Paid On:</td>
            <td colspan="3">/&nbsp;&nbsp;&nbsp;/</td>
        </tr>
        <tr class="double-line">
            <td colspan="3">Cheque Number:</td>
            <td colspan="3"></td>
        </tr>
        <tr class="sign-row">
            <td colspan="2"><hr></td>
            <td colspan="2"><hr></td>
            <td colspan="2"><hr></td>
        </tr>
        <tr class="bottom-row">
            <td colspan="2">JCR President</td>
            <td colspan="2">JCR Treasurer</td>
            <td colspan="2">College Bursar</td>
        </tr>

    </table>
    
</div>
<?php

    function count_pages($pdfname) {
      $pdftext = file_get_contents($pdfname);
      $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
      return $num;
    }

    $files = explode(',', $claim['files'], -1);
    foreach($files as $f){
        if(strpos($f, 'pdf')){
            $num = count_pages('application/views/finance/files/'.$f);
    ?>
            <object type="application/pdf" data="<?php echo site_url('application/views/finance/files/'.$f); ?>" width="100%" height="<?php echo 1200*$num; ?>px"></object>
    <?php
        }else{
    ?>
            <img src="<?php echo site_url('application/views/finance/files/'.$f); ?>" class="claim-image">
    <?php

        }
    }
?>
