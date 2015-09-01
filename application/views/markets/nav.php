<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo '<h3>';
foreach(array('details', 'meals', 'groceries', 'confirm') as $k => $v) {
	$output = ($k+1).'. '.ucfirst($v);
	if($k == $page_match) {
		// Make upper case if current page
		echo strtoupper($output);
	} elseif($k < $page_match) {
		// Link if before current page
		echo anchor('markets/'.$v, $output);
	} else {
		// Just text if after current page
		echo $output;
	}
	if($k < 3) {
		// 3 = one less than number of elements in navigation array
		echo ' &bull; ';
	}
}
echo '</h3>';?>
