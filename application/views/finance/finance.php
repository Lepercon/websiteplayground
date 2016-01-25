<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Finances</h1>

<?php if(isset($GLOBALS['messages'])){ echo '<div class="validation_success">'.$GLOBALS['messages'].'</div>'; }  ?>

<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">JCR Finance</h2>
        <div>
            <?php echo editable_area('finance', 'content/finance_intro', $page_admin); ?>
        </div>
    </div>
    
    <?php $this->load->view('finance/treasurer_contact'); ?>
</div>

<div class="content-right width-33 narrow-full">
    <?php
        $this->load->view('finance/notifications/notifications');
    ?>
    <div class="jcr-box wotw-outer">
        <h2  class="wotw-day">Invoicing System</h2>
        <div>
            View your invoices.
            <br>
            <a class="jcr-button inline-block" title="View Your Invoices" href="<?php echo site_url('finance/invoices/my_invoices'); ?>"><span class="inline-block ui-icon ui-icon-folder-collapsed"></span>My Invoices</a>
            <a class="jcr-button inline-block" title="View The Groups You Own" href="<?php echo site_url('finance/invoices/my_groups'); ?>"><span class="inline-block ui-icon ui-icon-person"></span>My Groups</a><br><br>
        </div>
    </div>

    <div class="jcr-box wotw-outer">
        <h2  class="wotw-day">JCR Online Claims System</h2>
        <div>
            File a claim, or view your past claims.
            <br>
            <a class="jcr-button inline-block" title="File a Claims Form" href="<?php echo site_url('finance/claims/claims_form'); ?>"><span class="inline-block ui-icon ui-icon-pencil"></span>File a Claim Online</a>
            <?php
                if($page_admin){?>
                    <br><a class="jcr-button inline-block" title="View all claims made." href="<?php echo site_url('finance/claims/view_claims');?>"><span class="inline-block ui-icon ui-icon-document"></span>View Filed Claims</a><br><?php
                }
            ?>
            <a class="jcr-button inline-block" title="View the status of claims you have made, or approve others claims." href="<?php echo site_url('finance/claims/my_claims');?>"><span class="inline-block ui-icon ui-icon-folder-open"></span>My Claims</a>
        </div>
    </div>
    
</div>