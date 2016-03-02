<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->model('finance_model');
echo back_link('finance');?>    

<div class="content-left width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Online JCR Claims Form</h2>
        <div>
            <?php
                $this->load->view('finance/claims/form_errors');
                $this->load->view('finance/claims/form_template', array('submit_link'=>'finance/claims/claims_form'));
            ?><br>
            We recommend you save all of your receipts into one pdf, but if you need to upload multiple files you can do so on the next page.<br><br>
        </div>
    </div>
    <?php 
        $this->load->view('finance/treasurer_contact');
    ?>
</div>
<div class="content-right width-33 narrow-full">    
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Information</h2>
        <div>
            <?php echo editable_area('finance', 'content/claims_form', $page_admin); ?>
        </div>
    </div>
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Links</h2>
        <div>
            <?php
                if($this->finance_model->finance_permissions()){?>
                    <a class="jcr-button inline-block" href="<?php echo site_url('finance/claims/view_claims');?>"><span class="inline-block ui-icon ui-icon-document"></span>View Filed Claims</a><br><?php
                }
            ?>
            <a class="jcr-button inline-block" href="<?php echo site_url('finance/claims/my_claims');?>"><span class="inline-block ui-icon ui-icon-folder-open"></span>View My Claims</a>
        </div>
    </div>
    
</div>