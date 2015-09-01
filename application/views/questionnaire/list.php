<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php if(is_admin()) { ?>
	<div class="jcr-box wotw-outerbox">
		<h2 class="wotw-day">Questionnaires</h2>
		<div class="padding-box">
			<p>Create a questionnaire by adding it to an event in the calendar</p>
			<a href="<?php echo site_url('events'); ?>" class="jcr-button inline-block" title="Event calendar">
				<span class="ui-icon ui-icon-calendar inline-block"></span>Calendar
			</a>
		</div>
	</div>
<?php }
if(!empty($questionnaires)) {?>
	<?php if(!is_admin()) { ?>
		<h2 class="bold-header">Questionnaires</h2>
	<?php } ?>
	<?php foreach($questionnaires as $q) { ?>
		<div class="jcr-box wotw-outer">
			<h3 class="wotw-day"><?php echo $q['name']; ?></h3>
			<div class="padding-box">
				<?php if(time() < $q['questionnaire_opens']) { ?>
					<p>Questionnaire opens at <?php echo date('H:i', $q['questionnaire_opens']); ?> on <?php echo date('l jS F Y',$q['questionnaire_opens']); ?>.</p>
				<?php } elseif(time() >= $q['questionnaire_opens'] && time() <= $q['questionnaire_closes']) { ?>
					<p>Questionnaire closes at <?php echo date('H:i', $q['questionnaire_closes']); ?> on <?php echo date('l jS F Y',$q['questionnaire_closes']); ?>.</p>
					<?php if($q['user_has_answered']) { ?>
						<p>You've answered this questionnaire</p>
					<?php } else { ?>
						<a href="<?php echo site_url('questionnaire/answer/'.$q['id']); ?>" class="jcr-button inline-block" title="Answer this questionnaire">
							<span class="ui-icon ui-icon-clipboard inline-block"></span>Answer
						</a>
					<?php } ?>
				<?php } else { ?>
					<p>Questionnaire Closed</p>
				<?php } ?>
				<?php if(is_admin()) { ?>
					<a href="<?php echo site_url('questionnaire/edit/'.$q['id']); ?>" class="jcr-button inline-block" title="Admin: Edit this questionnaire">
						<span class="ui-icon ui-icon-pencil inline-block"></span>Edit
					</a>
					<a href="<?php echo site_url('questionnaire/results/'.$q['id']); ?>" class="jcr-button inline-block" title="Admin: Results of this questionnaire">
						<span class="ui-icon ui-icon-document inline-block"></span>Results
					</a>
					<a href="<?php echo site_url('questionnaire/cancel/'.$q['id']); ?>" class="jcr-button inline-block" title="Admin: Cancel this questionnaire">
						<span class="ui-icon ui-icon-close inline-block"></span>Cancel
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
<?php } else { ?>
	<?php if(!is_admin()) { ?>
		<h2 class="bold-header">Questionnaires</h2>
	<?php } ?>
	<p>No questionnaires available</p>
<?php } ?>