<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

echo back_link('green');

eval(error_code()); ?>
<h2>Add a Tip</h2>
<?php
	echo form_open('green/manage_tips', array('class' => 'jcr-form'));
	echo form_input(array(
		'name' => 'tip',
		'value' => ($errors ? set_value('tip') : ''),
		'maxlength' => '500',
		'required' => 'required',
		'class' => 'input-help',
		'placeholder' => 'Green Tip',
		'title' => 'Required field. Enter the tip you would like to show.'
	));
	echo form_input(array(
		'name' => 'date',
		'value' => ($errors ? set_value('date') : ''),
		'maxlength' => '10',
		'required' => 'required',
		'class' => 'datepicker input-help',
		'title' => 'Required field. Enter the date in DD/MM/YYYY format for this tip to be shown on. The tip will be used on every year when this date occurs.',
		'placeholder' => 'DD/MM/YYYY'
	));
	echo token_ip('add_tip');
	echo form_submit('add_tip', 'Add Tip');
	echo form_close();
?>

<h2>List of Tips</h2>
<p>Click the cross next to a tip to delete it.</p>
<?php
$month_array = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
foreach($tips as $t) { ?>
	<div class="block">
		<p><a href="<?php echo site_url('green/delete_tip/'.$t['id']); ?>" class="admin-delete-button no-jsify inline-block jcr-button" title="Delete this tip"><span class="ui-icon ui-icon-close"></span></a><?php echo $month_array[$t['month']].' '.$t['day'].' '.$t['tip']; ?> </p>
	</div>
<?php } ?>
