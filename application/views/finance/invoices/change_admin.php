<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->model('invoice_model');
$user_id = $this->session->userdata('id');
$group_id = $this->uri->segment(3);
$member_id = $this->uri->segment(4);
$change_to = $this->uri->segment(5);
$member = $this->invoice_model->get_group_member($group_id, $user_id);
echo back_link('invoice/my_group/'.$group_id);
?><h2 id="Title">Changing Admin</h2><?php
if($member['permissions']){
	if($member_id == $user_id){
		echo 'You can not change your own permissions.';?>
		<script>
			setInterval(function(){window.location = "../<?php echo site_url('invoice/my_group/'.$group_id); ?>"},1000);
		</script><?php
	}else{	
		$new_member = $this->invoice_model->get_group_member($group_id, $member_id);
		$new_member['permissions'] = $change_to;
		$this->db->where('id', $new_member['id']);
		$this->db->update('finance_members', $new_member);
		?> 
		
		<script>
			setInterval(function(){document.getElementById("Title").innerHTML = document.getElementById("Title").innerHTML+"."},300);
			setInterval(function(){window.location = "../<?php echo site_url('invoice/my_group/'.$group_id); ?>"},1000);
		</script>

		<?php
	}
}else{
	echo 'You do not have permission to do that.';?>
	<?php
}?>