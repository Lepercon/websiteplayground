<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// set opt
function so($a,$b) {
    return ($a == $b ? 'selected="selected"':'');
}

function sc($a,$b) {
    return ($a == $b ? 'checked="checked"':'');
}

$hour = array();
$minute = array();
for($i = 0; $i <= 23; $i++) $hour[$i] = sprintf('%02d', $i);
for($i = 0; $i <= 55; $i+=5) $minute[$i] = sprintf('%02d', $i);

?>

<div class="jcr-box">
    <h2>Questionnaire Options</h2>
    <ul class="nolist">
        <li>
            <label for="name">Name</label><?php echo form_input(array(
                'name' => 'name',
                'id' => 'name',
                'maxlength' => '50',
                'title' => 'Questionnaire Name. Required. Max 50 characters.',
                'placeholder' => 'Questionnaire Name',
                'class' => 'input-help',
                'required' => 'required',
                'value' => isset($q) ? $q['name'] : ''
            )); ?>
        </li>
        <?php foreach(array('questionnaire_opens', 'questionnaire_closes') as $k) {
            echo '<li>';
                echo '<label for="'.$k.'_date">'.ucwords(str_replace('questionnaire_', '', $k)).'</label>';
                echo form_input(array(
                    'name' => $k.'_date',
                    'id' => $k.'_date',
                    'value' => isset($q) ? date("d/m/Y", $q[$k]) : date("d/m/Y"),
                    'placeholder' => 'DD/MM/YYYY',
                    'maxlength' => '10',
                    'class' => 'datepicker',
                    'required' => 'required'
                ));
                echo form_dropdown($k.'_hour', $hour, isset($q) ? date("G", $q[$k]) : 0);
                echo form_dropdown($k.'_minute', $minute, isset($q) ? date("i", $q[$k]) : 0);
            echo '</li>';
        } ?>
        <li>
            <label for="anonymous-label">Anonymous</label>
            <input type="hidden" name="anonymous" value="0" />
            <?php $val = isset($q) ? $q['anonymous'] : 1; ?>
            <input type="checkbox" name="anonymous" id="anonymous-label" value="1" <?php echo sc($val, 1);?> /><span>Names will not be shown with results though the system will store a user identifier which can be retrieved.</span>
        </li>
        <li>
            <label for="secure-label">Secure</label>
            <input type="hidden" name="secure" value="0" />
            <?php $val = isset($q) ? $q['secure'] : 1; ?>
            <input type="checkbox" name="secure" id="secure-label" value="1" <?php echo sc($val, 1);?> /><span>Only the questionnaire creator and webmaster can manage the questionnaire.</span>
        </li>
        <li>
            <label for="notes">Notes</label><textarea name="notes" id="notes" rows="5" placeholder="Questionnaire Notes"><?php echo isset($q) ? db_to_textarea($q['notes']) : ''; ?></textarea>
        </li>
    </ul>
</div>
<?php
$count = 1;
if(isset($q) && count($q['question']) > 1) {
    $count = count($q['question']);
}
for($i = 0; $i <= $count - 1; $i++) {
    $val = isset($q) ? $q['options'][$i] : '';
    if(empty($val)) {
        $select = 0;
    } elseif($val == "Yes;No") {
        $select = 2;
    } else {
        $select = 1;
    }?>
    <div class="jcr-box q-question">
        <ul class="nolist">
            <li><label>Question <span class="q-number"><?php echo $i+1; ?></span></label>
                <input type="text" placeholder="Question" maxlength="200" name="question[]" title="Max 200 characters" class="input-help q-question-name" value="<?php echo isset($q) ? $q['question'][$i] : ''; ?>"/>
            </li>
            <li><label>Type</label>
                <input type="radio" name="input<?php echo $i;?>" class="q-opt0 q-radio" value="0" <?php echo $select === 0 ? 'checked="checked"' : ''; ?> /><span>Open Ended</span>
            </li>
            <li><label></label>
                <input type="radio" name="input<?php echo $i;?>" class="q-opt1 q-radio" value="1" <?php echo $select === 1 ? 'checked="checked"' : ''; ?> /><span>Multiple choice</span>
            </li>
            <li><label></label>
                <input type="radio" name="input<?php echo $i;?>" class="q-opt2 q-radio" value="2" <?php echo $select === 2 ? 'checked="checked"' : ''; ?> /><span>Yes/No</span>
            </li>
            <li <?php echo $select === 1 ? 'style="display: list-item;"' : 'style="display: none;"'; ?>><label>Choices</label>
                <input type="text" placeholder="Choices list" maxlength="1000" name="options[]" title="Max 1000 characters; semi-colon separated" class="input-help q-choices" value="<?php echo isset($q) ? $q['options'][$i] : ''; ?>"/>
            </li>
            <li><label></label>
                <a href="#" class="jcr-button inline-block q-reset" title="Reset this question">
                    <span class="ui-icon ui-icon-refresh inline-block"></span>Reset
                </a>
                <a href="#" class="jcr-button inline-block q-delete" title="Delete this question">
                    <span class="ui-icon ui-icon-close inline-block"></span>Delete
                </a>
            </li>
        </ul>
    </div>
<?php } ?>
<a href="" class="jcr-button inline-block" id="q-add" title="Add a question">
    <span class="ui-icon ui-icon-plusthick inline-block"></span>Add
</a><br/><br/>
