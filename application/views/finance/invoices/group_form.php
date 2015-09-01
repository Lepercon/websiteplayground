<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
	
	$set = !isset($_POST['name']);
	if(!isset($group_info)){
		$group_info = array('budget_name'=>'', 'description'=>'', 'how_to_pay'=>'');
	}
		
	echo form_label('Group Name: ', 'name');
	echo form_input(array('id'=>'name', 'name'=>'name', 'placeholder'=>'Group Name', 'value'=>($set?$group_info['budget_name']:$_POST['name']), 'title'=>'Set the name of your group')).'<br>';
	
?>	
<datalist id="fullnameslist">
<?php
	foreach($users as $u){?>
		<option id="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></option><?php
	}
?>
</datalist>
<?php
	echo form_label('Description: ', 'details', array('style'=>'vertical-align: top!important;'));
	echo form_textarea(array('id'=>'details', 'name'=>'details', 'value'=>($set?$group_info['description']:$_POST['details']), 'placeholder'=>'Description', 'style'=>'height:50px;margin-bottom:3px;'))."<br>";
		
	echo form_label('Payment Details: ', 'payment', array('style'=>'vertical-align: top!important;'));
	echo form_textarea(array('id'=>'payment', 'name'=>'payment', 'value'=>($set?$group_info['how_to_pay']:$_POST['payment']), 'placeholder'=>'Payment Details', 'style'=>'height:50px;'))."<br>";
	
?>