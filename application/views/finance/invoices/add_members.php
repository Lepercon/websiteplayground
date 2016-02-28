<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->model('finance_model');

$g_id = $group['id'];
$members = $this->finance_model->get_members($g_id);

echo back_link('finance/invoices/my_group/'.$g_id);
?>

<h2><?php echo $group['budget_name']; ?></h2>
<div class="content-left width-33 narrow-full">
<h4>Current Members:</h4>
<?php
    foreach($members as $m){
?>
        <p><?php echo ($m['prefname']==''?$m['firstname']:$m['prefname']).' '.$m['surname'];?> </p>
<?php
    }
?>
<em>* Admins of this group.</em>
</div>
<div class="content-right width-66 narrow-full">
<?php 
    echo validation_errors('<div class="validation_errors"><span class="inline-block ui-icon ui-icon-notice"></span>', '</div><br>'); 
    if($message !== ''){
?>
    <div class="validation_errors"><span class="inline-block ui-icon ui-icon-notice"></span><?php echo $message; ?></div><br>
<?php
    }
    if($new_members > 0){
?>
    <div class="validation_success"><span class="inline-block ui-icon ui-icon-check"></span>You have successfully added <?php echo $new_members; ?> new group members.</div>
<?php
    }
?>
<h4 id="demo">Add New Members:</h4>
<p>Type the first few letters of the name, then select it from the drop-down list.</p>
<p>If it doesn't seem to be working, you may need to refresh the page.</p>
<p><input type="text" name="newmember" value="" style="width:150px" id="nameentry"><input type="hidden" name="newmember-id" id="nameentry-id"><font id="input-helper" style="font-size:small;color:red;"></font></p>
<span id="users-list">
<?php
    foreach($users as $u){?>
        <p value="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></p><?php
    }
?>
</span>

<a id="invoice-add-member" title="Add the above name to the list below." class="jcr-button inline-block"><span class="inline-block ui-icon ui-icon-plus"></span>Add To List</a>

<?php
    echo form_open('finance/add_members/'.$g_id, array('class' => 'jcr-form no-jsify', 'id'=>'invoice-add-members-form'));
    echo form_dropdown('newmembers[]', array(), '', 'size="8" style="width:200px;" id="namelist" multiple="yes"');
?>
    <br>    
    <a title="Remove the selected name from the list." class="jcr-button inline-block" id="removefromlist"><span class="inline-block ui-icon ui-icon-minus"></span>Remove From List</a><br>
    <input type="submit" name="add_all" value="Add Members To Group" id="invoice-add-members">
<?php
    echo form_input(array('name'=>'ids', 'type'=>'hidden', 'id'=>'ids'));
    echo form_close();
?>
</div>


