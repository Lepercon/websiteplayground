<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

eval(error_code().success_code());
?>

<div class="content-left width-66 narrow-full">
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Email the JCR</h2>
		<?php echo form_open('contact/send', array('class' => 'jcr-form')); ?>
			<ul id="contact-form" class="nolist">
				<li>
					<label>To</label>
					<?php if(isset($user)) {
						echo $user['firstname'].' '.$user['surname'].(isset($user['title']) ? ' ('.$user['title'].')' : '');
					} else { ?>
						<select name="user[]">
							<option></option>
							<?php foreach($users as $u): ?>
								<option value="<?php echo $u['id']; ?>" <?php echo set_select('user[]',$u['id'], (isset($selected) && $selected === $u['id'] ? TRUE : FALSE)); ?>>
									<?php echo user_pref_name($u['firstname'], $u['prefname'], $u['surname']).(empty($u['title']) ? '' : ' ('.$u['title'].')'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php } ?>
				</li>
				<li>
					<?php if(!logged_in()) : ?>
						<label for="email-label">Your email</label>
						<input type="email" name="email" id="email-label" value="<?php echo set_value('email'); ?>"/>
						</li>
						<li>
						<label for="name-label">Your name</label>
						<input type="text" name="name" id="name-label" value="<?php echo set_value('name'); ?>"/>
					<?php else : ?>
						<label>From</label>
						<input type="radio" name="anonymous" id="personal-label" value="0" <?php echo set_radio('anonymous', '0', TRUE); ?>/>
						<label class="radio-label" for="personal-label"><?php echo $this->session->userdata('email'); ?></label>
						<label></label>
						<input type="radio" name="anonymous" id="anonymous-label" value="1" <?php echo set_radio('anonymous', '1'); ?>/>
						<label class="radio-label" for="anonymous-label">Anonymous</label>
						<label></label>
						<input type="checkbox" name="cc" id="cc-label" value="1" <?php echo set_checkbox('cc', '1'); ?>/>
						<label class="radio-label" for="cc-label" >BCC me</label>
					<?php endif; ?>
				</li>
				<li>
					<label for="subject-label">Subject</label>
					<input type="text" name="subject" id="subject-label" maxlength="100" value="<?php echo set_value('subject'); ?>"/>
				</li>
				<li>
					<label class="msg-label" for="message-label">Message</label>
					<textarea name="message" rows="10" maxlength="10000" id="message-label"><?php echo set_value('message'); ?></textarea>
				</li>
				<li>
					<label></label>
					<input type="submit" value="Send Email" />
				</li>
			</ul>
		<?php echo token_ip('contact');
		if(isset($user)) echo '<input type="hidden" name="user" value="'.$user['id'].'" />';
		echo form_close();
		?>
	</div>
	<div id="butler_map_container" class="jcr-box" style="height:400px;"></div>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Problems with the site?</h2>
		<p>If you have any problems with the site please contact:<br><a href="mailto:butler.jcr-webmaster@durham.ac.uk">butler.jcr-webmaster@durham.ac.uk</a></p>
	</div>
</div>

<div class="content-right width-33 narrow-full" itemscope itemtype="http://schema.org/Organization">
	<div class="jcr-box wotw-outer" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<h2 class="wotw-day">Address</h2>
		<p>The JCR<br />
		<span itemprop="name">Josephine Butler College</span><br />
		Durham University<br />
		<span itemprop="streetAddress">South Road</span><br />
		<span itemprop="addressLocality">Durham, UK</span><br />
		<span itemprop="postalCode">DH1 3DF</span><br /></p>
	</div>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Phone</h2>
		<p><b>Reception:</b> <span itemprop="telephone">0191 334 7260</span></p>
		<p><b>Porters:</b> 0191 334 7270</p>
		<p><b>Admissions:</b> 0191 334 7271</p>
		<p><b>Fax:</b> <span itemprop="fax">0191 334 7259</span></p>
		<p><b>JCR Office:</b> 0191 334 7264</p>
	</div>
	<div class="jcr-box wotw-outer">
		<h3 class="wotw-day">Get In Contact</h3>
		<p>If you would like more help or information then please get in contact:</p>
		<?php $this->load->view('utilities/users_contact', array('level_ids'=>array(2))); ?>
	</div>
	<div class="jcr-box wotw-outer">
		<h2 class="wotw-day">Follow Us</h2>
		<div>
			<a class="sprite-container"  href="http://www.facebook.com/groups/274250999315976/" target="_blank">
				<div class="common-sprite" id="sprite-facebook"></div>
				<div class="inline-block"><p>The JCR on Facebook</p></div>
			</a>
		</div>
		<div>
			<a class="sprite-container"  href="http://twitter.com/butlerjcr" target="_blank">
				<div class="common-sprite" id="sprite-twitter"></div>
				<div class="inline-block"><p>Follow @butlerjcr</p></div>
			</a>
		</div>
	</div>
	<?php if(logged_in()) : ?>
		<div class="jcr-box wotw-outer">
			<h2 class="wotw-day">Livers In</h2>
			<p>Housekeeping Notification Form</p>
			<a href="http://www.dur.ac.uk/butler.college/local/current/housekeeping/" class="jcr-button inline-block" title="Report a problem to college porters">
				<span class="ui-icon ui-icon-mail-closed inline-block"></span>Report Problem
			</a>
		</div>
	<?php endif; ?>
</div>
