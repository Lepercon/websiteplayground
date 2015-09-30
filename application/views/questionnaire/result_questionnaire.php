<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('questionnaire/answer/'.$q['id']);

echo '<h2>Results of '.$q['name'].'</h2>';
echo 'Open from '.date('H:i \o\n D, d/m/y', $q['questionnaire_opens']).' until '.date('H:i \o\n D, d/m/y', $q['questionnaire_closes']);
echo '<br />Results latest at '.date('H:i \o\n D, d/m/y', time());
echo (empty($q['notes']) ? '' : '<br />Notes: '.$q['notes'] );

foreach($q['question'] as $k => $v) { ?>
    <div class="jcr-box wotw-outer">
        <h3 class="wotw-day">Question <?php echo $k + 1; ?></h3>
        <div class="padding-box">
            <h3><?php echo $v; ?></h3>
            <ul class="nolist">
            <?php if(empty($q['options'][$k])) {
                foreach($answer as $a) {
                    if($a['q_order'] == $k) {
                        echo '<li>'.(isset($a['user_id']) ? $a['user_id'].': ' : '').$a['answer'].'</li>';
                    }
                }
            } else {
                $answer_array = array();
                foreach($answer as $a) {
                    if($a['q_order'] == $k) {
                        $answer_array[] = $a['answer'];
                    }
                }
                $answer_count = array_count_values($answer_array);
                foreach($answer_count as $k => $v) {
                    echo '<li>'.$k.': '.$v.'</li>';
                }
            } ?>
            </ul>
        </div>
    </div>
<?php } ?>
