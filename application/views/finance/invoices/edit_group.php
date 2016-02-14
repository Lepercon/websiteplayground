<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('finance/invoices/my_group/'.$group_info['id']);?>
<?php
    $this->load->view('finance/invoices/group_create_info');
?>
<div class="content-right width-66 narrow-full">
    <div class="jcr-box wotw-outer">
        <h2 class="wotw-day">Update Invoicing Group</h2>
        <div>
        <?php
            echo validation_errors('<div class="validation_errors"><span class="inline-block ui-icon ui-icon-notice"></span>', '</div><br>');
            echo $messages;
            echo form_open(site_url('finance/claims/edit_group/'.$group_info['id']), array('class'=>'jcr-form inline-block no-jsify'));
            echo form_hidden(array('group_id'=>$group_info['id']));
            
            $this->load->view('finance/invoices/group_form');
            
            echo form_label();
            echo form_submit('update', 'Update Group');
            echo form_close();
        ?>
        </div>
    </div>
</div>