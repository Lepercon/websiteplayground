<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    echo back_link('admin');
    
    $from_old = !$run && isset($_POST['submit']);
    
    if($run){
?>
        <div class="validation_success"><span class="ui-icon ui-icon-check inline-block green-icon"></span>Updated</div>
<?php    
    }
    echo validation_errors('<div class="validation_errors"><span class="ui-icon ui-icon-close inline-block"></span>', '</div>');
    echo form_open('admin/messages', array('class' => 'jcr-form'));
    for($i=0;$i<24;$i++){
        $hour[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
    }
    for($i=0;$i<60;$i++){
        $minute[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
    }

    $i = 0;
?>
<table class="set-messages">
    <tr><th></th><th>Event</th><th>Start</th><th>End</th><th>Message</th></tr>
    <?php
        foreach($messages as $m){
        ?>
            <tr <?php echo $new_id==$m['id']?'class="new-row"':''; ?>>
                <td><?php echo form_checkbox('select-row', 'selected', false, 'class="row-delete-check"'); ?></td>
                <td><?php echo form_dropdown('event-'.++$i, $events, $from_old?set_select('event-'.$i):$m['event_id'], 'class="event" style="width:200px;"');?></td>
                <td><?php 
                    echo form_checkbox('have-start-'.$i, 'not-null', !is_null($m['start']), 'class="disable-enable"');
                    echo form_input(array('name'=>'start-date-'.$i, 'value'=>(is_null($m['start'])?'':date('d/m/Y', $m['start'])), 'style'=>'width:100px;', is_null($m['start'])?'disabled':'enabled'=>''));
                    echo form_dropdown('start-hour-'.$i, $hour, date('G', $m['start']), is_null($m['start'])?'disabled':'');
                    echo form_dropdown('start-minute-'.$i, $minute, date('i', $m['start']), is_null($m['start'])?'disabled':''); 
                ?></td>
                <td><?php 
                    echo form_checkbox('have-end-'.$i, 'not-null', !is_null($m['expiry']), 'class="disable-enable"');
                    echo form_input(array('name'=>'end-date-'.$i, 'value'=>(is_null($m['expiry'])?'':date('d/m/Y', $m['expiry'])), 'style'=>'width:100px;', is_null($m['expiry'])?'disabled':'enabled'=>''));
                    echo form_dropdown('end-hour-'.$i, $hour, date('G', $m['expiry']), is_null($m['expiry'])?'disabled':'');
                    echo form_dropdown('end-minute-'.$i, $minute, date('i', $m['expiry']), is_null($m['expiry'])?'disabled':''); 
                ?></td>
                <td><?php echo form_input(array('name'=>'message-'.$i, 'value'=>($from_old?set_value('message-'.$i):$m['message']), 'class'=>'message-input')); ?></td>
            </tr>
        <?php
        }
    ?>
</table>
<p class="select-all"><?php echo form_checkbox(array('class'=>'all-check')); ?> Select All</p>
<a class="inline-block delete-selected jcr-button" href="#"><span class="ui-icon ui-icon-trash"></span>Delete Selected</a> 
<?php    
    echo form_submit('submit', 'Submit Changes');
    echo form_close();
?>
<p>This is how the message will be displayed:</p>
<div class="wotw-outer jcr-box outer-box">
    <h2 class="wotw-day">Messages from your JCR:</h2>
    <div class="message-scroll">
        <span><b id="title">Event Title</b><font id="dash"> - </font><font id="message">Message</font></span>
    </div>
</div>
<span class="banner-page"></span>
<h3>Add A New Message:</h3>
<?php
    echo form_open('admin/messages', array('class' => 'jcr-form no-jsify'));
    
    echo form_label('Message');
    echo form_input(array('name'=>'new-message', 'id'=>'new-message')).'<br>';
    
    echo form_label('');
    echo form_submit('new-message-form', 'Add New Message');
    
    echo form_close();
?>