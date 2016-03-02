<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); echo back_link('finance/claims/my_claims');?>
<div class="width-66 content-left narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Edit JCR Claim</h2>
        <div>
            <?php
                if(isset($validation) and $validation and empty($file_errors)){
            ?>
                <div class="validation_success">
                <span class="ui-icon ui-icon-check inline-block "></span> Thank you for <?php echo $type=='new'?'submitting':'updating'; ?> your claim, it has been passed onto the budget holder for approval. Once approved it will then be passed onto the JCR Treasurer for processing.
                </div>
            <?php
                }else{
                    $this->load->view('finance/claims/form_errors', array('file_errors'=>$file_errors));
                }
                
                $this->load->view('finance/claims/form_template', array('claim'=>$claim, 'button_name'=>'Update', 'submit_link'=>'finance/claims/edit_claim/'.$claim['id']));
            ?>
        </div>
    </div>
    <?php
        $this->load->view('finance/treasurer_contact');
    ?>
</div>
<div class="content-right width-33">    
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
                    <a class="jcr-button inline-block" href="<?php echo site_url('finance/claims/view_claims');?>">View Filed Claims</a><br><?php
                }
            ?>
            <a class="jcr-button inline-block" href="<?php echo site_url('finance/claims/my_claims');?>">View My Claims</a>
        </div>
    </div>
</div>