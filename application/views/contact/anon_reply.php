<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

eval(error_code().success_code());

echo form_open('contact/anon_reply/'.$details['uid']);
?>
	<ul id="contact-form" class="nolist">
		<li>
			<label>Subject:</label>
			Re: <?php echo $details['subject']; ?>
		</li>
		<li>
			<label class="msg-label">Message:</label>
			<textarea name="amessage" rows="8" maxlength="10000"><?php echo set_value('amessage'); ?></textarea>
		</li>
		<li>
			<label></label>
			<?php echo token_ip('anon_reply');
			echo form_submit('send', 'Send Reply'); ?>
		</li>
		<li>
			<label class="msg-label">In Reply to:</label>
			<div id="reply-msg" class="inline-block"><?php echo nl2br($details['message']);?></div>
		</li>
	</ul>
<?php echo form_close();?>