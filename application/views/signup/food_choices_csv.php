<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function sputcsv($row, $delimiter = ',', $enclosure = '"', $eol = "\n") {
	static $fp = false;
	if ($fp === false) $fp = fopen('php://temp', 'r+');
   		// see http://php.net/manual/en/wrappers.php.php - yes there are 2 '.php's on the end.
		// NB: anything you read/write to/from 'php://temp' is specific to this filehandle
	else rewind($fp);
   
	if(fputcsv($fp, $row, $delimiter, $enclosure) === false) return false;
   
	rewind($fp);
	$csv = fgets($fp);
   
	//if($eol != PHP_EOL) $csv = substr($csv, 0, (0 - strlen(PHP_EOL))) . $eol;
	return $csv;
}

echo sputcsv(array('Name', 'Email', 'Table Num', 'Starter', 'Main', 'Dessert', 'Drink', 'Special requirements', 'Pickup location', 'Booked By'));
foreach($reservations as $r) {
	$row = array();
	if(empty($r['uname'])) {
		$name = explode(' ',$r['name']);
		$last = array_pop($name);
		$row[] = $last.(empty($name) ? '' : ', '.implode(' ', $name));
	}
	else $row[] = $r['uname'];
	foreach(array('email', 'table_num', 'starter', 'main', 'dessert', 'drink', 'special', 'pickup', 'booked_by') as $t) $row[] = $r[$t];
	echo sputcsv($row);
}