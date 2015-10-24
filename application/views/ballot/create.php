<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

foreach(range(0, 23) as $r){$hours[$r] = str_pad($r, 2, "0", STR_PAD_LEFT);}
foreach(range(0, 59, 5) as $r){$minutes[$r] = str_pad($r, 2, "0", STR_PAD_LEFT);}
$group_sizes = range(0,99);
unset($group_sizes[0]);

echo back_link('ballot');

echo heading('Create a New Ballot', 2);
echo form_open('', 'class="jcr-form no-jsify"');

echo '<p>'.form_label('Event: ').form_dropdown('event_id', $events).'</p>';
echo '<p>'.form_label('Ballot Name: ').form_input('signup_name', '', 'placeholder="Name" required="required" ').'</p>';
echo '<p>'.form_label('Ballot Open: ').form_input('date-open', '', 'placeholder="Open Date" class="datepicker" required="required" ').' '.form_dropdown('open-hour', $hours).':'.form_dropdown('open-minute', $minutes).'</p>';
echo '<p>'.form_label('Ballot Close: ').form_input('date-close', '', 'placeholder="Close Date" class="datepicker" required="required" ').' '.form_dropdown('close-hour', $hours).':'.form_dropdown('close-minute', $minutes).'</p>';
echo '<p>'.form_label('Max Group: ').form_dropdown('max_group', $group_sizes, 6).'</p>';

echo '<p>'.form_label('Tables: ').'<a href="#" class="remove-table jcr-button" title="Remove the last table.">&nbsp;-&nbsp;</a><a href="#" class="add-table jcr-button" title="Add a new table.">&nbsp;+&nbsp;</a></p>';

echo '<div id="tables">';
foreach(range(1, 5) as $t){
    echo '<p>'.form_label().form_label('Table '.$t.': ').form_dropdown('table[]', $group_sizes, 14).'</p>';
}
echo '</div>';

echo '<p>'.form_label('Base Price: ').form_input('price', '', 'placeholder="£0.00" required="required" pattern="[0-9]*\.{0,1}[0-9]*" title="The price of the meal (before any options are added)."').'</p>';
echo '<p>'.form_label('Allow Guests: ').form_checkbox('allow_guests', '1', FALSE).'</p><br>';
echo '<p>'.form_label('Options: ').'<a href="#" class="remove-option jcr-button" title="Remove the last option.">&nbsp;-&nbsp;</a><a href="#" class="add-option jcr-button" title="Add a new option.">&nbsp;+&nbsp;</a></p>';

echo '<div id="options">';
foreach(range(1, 2) as $t){
    echo '<div><p>'.form_label().form_label('Option '.$t.' Name: ').form_input('option['.$t.'][]', '', 'placeholder="Option '.$t.' Name" required="required" ').'<a href="#" class="remove-sub-option jcr-button" title="Remove the last selection.">&nbsp;-&nbsp;</a><a href="#" class="add-sub-option jcr-button" title="Add a new selection.">&nbsp;+&nbsp;</a></p>';
    foreach(range(1, 3) as $tt){
        echo '<p>'.form_label().form_label().form_label('Selection '.$t.'-'.$tt.': ');
        echo form_input('option['.$t.'][]', '', 'placeholder="Selection '.$t.'-'.$tt.'" required="required" ').' ';
        echo form_input('option['.$t.'][]', '', 'placeholder="Price '.$t.'-'.$tt.'" required="required" ').'</p>';
    }
    echo '</div>';
}
echo '</div>';

echo form_label().form_submit('submit', 'Create');

echo form_close();

?>