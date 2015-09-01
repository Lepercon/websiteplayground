<?php
$server = "mysql.dur.ac.uk";
$username = "djb8jcr";
$password = "VP4GL6dhF3rYdUGB";
$database = "Xdjb8jcr_gameshow";

$db = mysql_connect($server,$username,$password);
if(!$db) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db($database);

function query($query) {
	$result = mysql_query($query);
	if ($result) {
		if(@mysql_num_rows($result)) {
			while($row = mysql_fetch_array($result)) {
				$output[] = $row;
			}
		} else {
			$output = null;
		}
	} else {
		die('MySQL error : ' . mysql_error());
	}

	if(count($output) == 1) {
		$output = $output[0];
	}
	return $output;
}

$result = query("INSERT INTO family (team, time) VALUES (".$_POST['id'].", ".time().")");

