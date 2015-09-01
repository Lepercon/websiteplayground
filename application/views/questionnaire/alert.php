<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="center">
	<img src="<?php echo VIEW_URL.'questionnaire/img/angry.jpg'?>" alt="Angry JCR" />
	<p><?php echo user_pref_name($this->session->userdata('firstname'), $this->session->userdata('prefname')); ?>, the webmaster has been informed of your attempt to access the <?php echo $section; ?> section of this questionnaire and is now very angry.</p>
	<a href="http://www.youtube.com/watch?v=oT3mCybbhf0" target="_blank" class="no-jsify jcr-button inline-block" title="Apologise and go back">
		<span class="ui-icon ui-icon-arrow-1-w inline-block"></span>I'm sorry, I won't do it again
	</a>
</div>