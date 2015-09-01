<?php

if(strpos($_SERVER['SERVER_NAME'], 'butlerjcr.co.uk') > 0) {
	$server = "mysql.dur.ac.uk";
	$username = "djb8jcr";
	$password = "VP4GL6dhF3rYdUGB";
	$database = "Xdjb8jcr_family";
} else {
	$server = "localhost";
	$username = "familyUol4i";
	$password = "bk)}k)};J1lL";
	$database = "family";
}

$db = mysql_connect($server,$username,$password);
if(!$db) {
	die('Could not connect: ' . mysql_error());
}

if ($database){
	mysql_select_db($database);
}

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
			 die('My Sql error : ' . mysql_error());
		}

		if(count($output) == 1) {
			$output = $output[0];
		}
	return $output;
}

function find_parents($child_id){
	$query = "select a.id, a.name, a.surname, a.yeargroup, a.angel from people a inner join people b on a.id = b.parent_1 where b.id = '".$child_id."'";
	$parent1 = query($query);

	$query = "select a.id, a.name, a.surname, a.yeargroup, a.angel from people a inner join people b on a.id = b.parent_2 where b.id = '".$child_id."'";
	$parent2 = query($query);

	$query = "select a.id, a.name, a.surname, a.yeargroup, a.angel from people a inner join people b on a.id = b.parent_3 where b.id = '".$child_id."'";
	$parent3 = query($query);

	$query = "select a.id, a.name, a.surname, a.yeargroup, a.angel from people a inner join people b on a.id = b.parent_4 where b.id = '".$child_id."'";
	$parent4 = query($query);

	$parents = array_filter(array($parent1, $parent2, $parent3, $parent4));
	return $parents;
}

function find_children($parent_id){
	$query = "select id, name, surname, yeargroup, angel from people 
		WHERE parent_1 = '".$parent_id."'
		OR parent_2 = '".$parent_id."'
		OR parent_3 = '".$parent_id."'
		OR parent_4 = '".$parent_id."'";
	$children = query($query);

	if(is_array($children[0])) {			// array of children
		return $children;
	} else if(isset($children[0])) {					// just one child
		$children = array($children);
		return $children;
	} else {
		return null;
	}
}

function find_siblings($child_id) {
	$siblings = array();
	$parents = find_parents($child_id);

	if(isset($parents[0])){
		$children = find_children($parents[0]['id']);

		foreach ($children as $child) {
			if ($child['id'] != $child_id) {
				$siblings[] = $child;
			}
		}
	}
	return $siblings;
}

?>