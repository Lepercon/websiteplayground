<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('questionnaire'); ?>

<div>
    <?php echo '<h3>'.$q['name'].'</h3>';
    echo '<p>Open from '.date('H:i \o\n D, d/m/y', $q['questionnaire_opens']).' until '.date('H:i \o\n D, d/m/y', $q['questionnaire_closes']).'</p>';
    if(is_admin()) { ?>
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

<div>
<?php
    if($q['user_has_answered']) echo '<h3>Thank you for answering</h3>';
    else if(time() < $q['questionnaire_opens']) echo '<h3>Questionnaire not yet open</h3>';
    else if(time() >= $q['questionnaire_opens'] && time() <= $q['questionnaire_closes'] && $q['started'] === FALSE) { ?>
        <h3>Before you begin the questionnaire</h3>
        <p>You can only answer this questionnaire once.</p>
        <?php if(!empty($q['notes'])) echo '<p>'.$q['notes'].'</p>'; ?>
        <?php echo form_open('questionnaire/answer/'.$q['id'], array('class' => 'jcr-form'));
            echo token_ip('begin-questionnaire');
            echo form_submit('begin', 'Begin Questionnaire');
        echo form_close();
    }
    else if(time() >= $q['questionnaire_opens'] && time() <= $q['questionnaire_closes'] && $q['started'] === TRUE) {
        echo form_open('questionnaire/answer/'.$q['id'], array('class' => 'jcr-form'));
            foreach($q['question'] as $k => $v) { ?>
                <div class="jcr-box wotw-outer">
                    <h3 class="wotw-day">Question <?php echo $k + 1; ?></h3>
                    <div class="padding-box">
                        <p><?php echo $q['question'][$k]?></p>
                        <?php if(empty($q['options'][$k])) { ?>
                            <textarea type="text" name="answer[<?php echo $k; ?>]" ></textarea>
                        <?php } else {
                            $opts = explode(';', $q['options'][$k]); ?>
                            <ul class="no-list">
                                <?php $i = 1;
                                foreach($opts as $o) {
                                    $o = trim($o);
                                    echo '<li><input type="radio" name="answer['.$k.']" value="'.$o.'" id="option'.$k.$i.'"><label class="radio-label" for="option'.$k.$i.'">'.$o.'</label></li>';
                                    $i++;
                                } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <br />
            <ul class="nolist no-print"><li>
                <?php echo form_submit('answers', 'Submit Answers');
                echo token_ip('questionnaire'); ?>
            </li></ul>
        <?php echo form_close();
    }
    else echo '<h3>Questionnaire closed</h3>';
?>
</div>