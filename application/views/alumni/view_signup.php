<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
echo back_link('alumni');

$this->load->view('events/event_info', array('event' => $event_info));
?>
<div class="jcr-box wotw-outer">
<h2 class="wotw-day">Sign Up Info</h2>
<div class="alumni-signup-info">

<?php
echo str_replace(chr(10),'<br>', $alumni_event_info['sign_up_details']);
$n = $signup['number_tickets'];
$guest = explode(';', $signup['guests']);
$options = explode(';', $signup['options']);
$num_options = count(explode(';', $alumni_event_info['options']));
$option_num = 0;
?>
<table>
	<tr>		
		<td>Name:</td>
		<td><?php echo $signup['name']; ?></td>
	</tr>
<?php if($signup['name_at_uni'] != ''){ ?>
	<tr>		
		<td>Name at Uni:</td>
		<td><?php echo $signup['name_at_uni']; ?></td>
	</tr>
<?php } ?>
	<tr>		
		<td>Number of Tickets:</td>
		<td><?php echo $n; ?></td>
	</tr>
	<tr>		
		<td>Total Cost:</td>
		<td><?php echo '£'.number_format($n*$alumni_event_info['cost'],2); ?></td>
	</tr>
	<tr>		
		<td>Email:</td>
		<td><?php echo $signup['email']; ?></td>
	</tr>
	<tr>		
		<td>Address:</td>
		<td><?php echo str_replace(chr(10), '<br>', $signup['address']); ?></td>
	</tr>
	<tr>		
		<td>Phone Number:</td>
		<td><?php echo $signup['telephone']; ?></td>
	</tr>
	<tr>		
		<td>Graduation Year:</td>
		<td><?php echo $signup['graduation_year']; ?></td>
	</tr>
	<tr>		
		<td>Subject:</td>
		<td><?php echo $signup['subject']; ?></td>
	</tr>	
<?php
	for($i=1;$i<=$num_options;$i++){
?>
<tr>		
	<td>Option <?php echo $i; ?>:</td>
	<td><?php echo $options[$option_num++]; ?></td>
</tr>
<?php
	}
?>

<?php
	for($i=1;$i<count($guest);$i++){
?>
<tr>		
	<td>Guest <?php echo $i; ?> Name:</td>
	<td><?php echo $guest[$i-1]; ?></td>
</tr>
<?php
		for($j=1;$j<=$num_options;$j++){
?>
<tr>
	<td></td>		
	<td class="alumni-guest-option-label">Option <?php echo $j; ?>:</td>
	<td><?php echo $options[$option_num++];?></td>
</tr>
<?php

		}
	}
?>
	<tr>		
		<td>Special Requirements:</td>
		<td><?php echo str_replace(chr(10), '<br>', $signup['special_requirements']); ?></td>
	</tr>
</table>
If you have any questions, you can contact our Alumni Relations Assistant, <?php echo $current_scdo; ?>
</div>
</div>