<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<h3>Are you sure you want to cancel this questionnaire?</h3>
<p>This will delete all associated responses.</p>
<br />
<?php echo form_open('questionnaire/cancel/'.$q_id, array('class' => 'jcr-form'));
	echo token_ip('cancel_questionnaire'); ?>
	<input type="submit" name="cancel" value="Cancel Questionnaire" />
	<input type="submit" value="Don't Cancel" />
<?php echo form_close(); ?>