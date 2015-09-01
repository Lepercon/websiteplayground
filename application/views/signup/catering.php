<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo back_link('signup/event/'.$signup['id']);
echo print_link(); ?>

<h2>Food &amp; Drink Order Totals</h2>
<?php foreach(array('starter','main','dessert','drink') as $course) :
	$names = array();
	echo '<h3>'.ucfirst($course).'s</h3><ul class="nolist">';
	foreach($cater as $c) $names[] = $c[$course];
	if(strpos($signup[$course.'s'], ',') !== FALSE) foreach(array_count_values($names) as $key => $value) echo '<li>'.$key.': '.$value.'</li>';
	echo '</ul>';
endforeach;
?>

<h2>Drinks</h2>
<?php
$tab = '';
$dri = '';
$num = 0;
?>
<p>This table shows the number of drinks requested, it does not take account of where a bottle is intended to be split between two people.</p>
<table id="signup-food-choices-table">
	<tr>
		<th>Table No.</th>
		<th>Drink</th>
		<th>Quantity</th>
	</tr>
	<?php foreach($cater as $d) : ?>
		<tr>
			<?php
			if(($d['table_num'] !== $tab || $d['drink'] !== $dri) && $num !== 0) {
				?>
				<td><?php echo $tab; ?></td>
				<td><?php echo $dri; ?></td>
				<td><?php echo $num; ?></td>
				<?php
				$num = 1;
			}
			else {
				$num++;
			}
			$tab = $d['table_num'];
			$dri = $d['drink'];
			?>
		</tr>
	<?php endforeach; ?>
		<tr>
			<td><?php echo $tab; ?></td>
			<td><?php echo $dri; ?></td>
			<td><?php echo $num; ?></td>
		</tr>
</table>
