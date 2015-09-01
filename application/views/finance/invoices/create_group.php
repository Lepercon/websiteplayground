<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('finance/my_groups');?>
<?php
	$this->load->view('finance/invoices/group_create_info');
?>
<div class="content-right width-66 narrow-full">
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Create a New Invoicing Group</h2>
		<div>
		<?php
			echo validation_errors('<div class="validation_errors"><span class="inline-block ui-icon ui-icon-notice"></span>', '</div><br>');
			echo form_open(site_url('finance/create_group'), array('class'=>'jcr-form inline-block no-jsify'));
			
			$this->load->view('finance/invoices/group_form');
			
			echo form_label();
			echo form_submit('create', 'Create Group');
			echo form_close();
		?>
		</div>
	</div>
</div>