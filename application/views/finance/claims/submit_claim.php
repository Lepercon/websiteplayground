<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('finance');
?>

<h1>Finances</h1>
<?php 
if($success){ ?>
	<p>Thank you for submitting your claim, it has now been passed onto the budget holder for approval. Once approved it will then be passed onto the JCR Treasurer for processing.</p><?php	
}else{?>
	<p>There seems to have been some kind of error, please try again, or contact the JCR Treasurer.</p>Response: <?php
	echo $response;
}?>
<a href="<?php echo site_url('finance/my_claims'); ?>">View your submitted claims.</a><br><br>
<h3>Upload More Receipts</h3>
<?php

	echo form_open_multipart('finance/submit_claim', array('class' => 'jcr-form claim-submit-form no-jsify', 'id'=>'claim-submit-form'));
	echo form_input(array('name'=>'neworupdate', 'value'=>'update', 'style'=>'display:none;'));
	echo form_input(array('name'=>'claimid', 'value'=>$claimid, 'style'=>'display:none;'));
	
	//echo form_label('');
    echo form_upload(array('name'=>'file')).'<br>';
    
    //echo form_label('');
    echo form_submit(array('name'=>'submitandupload', 'value'=>'Submit File', 'id'=>'claim-submit-button'));

?>
<?php 
	$this->load->view('finance/treasurer_contact');
?>